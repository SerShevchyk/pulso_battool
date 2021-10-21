<?php

namespace Drupal\pulso_battool\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BatToolResultExport.
 *
 * @package Drupal\pulso_battool\Controller
 */
class BatToolResultExport extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Drupal\Core\Entity\Query\QueryFactory definition.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQueryFactory;

  /**
   * MyCSVReport constructor.
   */
  public function __construct(QueryFactory $entityQueryFactory) {
    $this->entityQueryFactory = $entityQueryFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * Export a CSV of data.
   */
  public function build() {
    $fields = \Drupal::config('pulso_battool.settings')->get("mental_resilience_block.fields");
    $additionals = ["age", "gender", "diplom", "sector", "occupation", "work_time", "contract", "family_situation"];

    $startDate = \Drupal::request()->query->get('start_date');
    $endDate = \Drupal::request()->query->get('end_date');

    $db = \Drupal::database();
    $query = $db->select('bat_tool_result', 'btr');
    $query->leftJoin('bat_tool_result_field_data', 'btrfd', 'btrfd.id = btr.id');
    $query->fields('btr', ['id']);
    $query->fields('btrfd', ['created']);

    foreach ($fields as $sectionKey => $section) {
      foreach ($section as $key => $label) {
        $fieldsList[] = $sectionKey . "_" . $key;
        $fieldsLabels[$sectionKey . "_" . $key] = $label;
      }
    }

    foreach ($additionals as $additional) {
      $fieldsLabels[$additional] = str_replace("_", " ", ucfirst($additional));
    }

    $query->fields('btrfd', array_merge($additionals, $fieldsList));

    if (isset($startDate) && !empty($startDate) && isset($endDate) && !empty($endDate)) {
      $query->condition('btrfd.created', strtotime($startDate), '>=');
      $query->condition('btrfd.created', strtotime($endDate), '<=');
    }

    $query->orderBy('btrfd.created', 'DESC');
    $results = $query->execute()->fetchAll();

    $handle = fopen('php://temp', 'w+');
    $header = $fieldsLabels;
    fputcsv($handle, $header);

    foreach ($results as $result) {
      $data = [];
      foreach ($fieldsLabels as $key => $fieldsLabel) {
        $data[$key] = $result->{$key};
      }

      $t[] = $data;
      fputcsv($handle, array_values($data));
    }

    rewind($handle);
    $csvData = stream_get_contents($handle);
    fclose($handle);

    $response = new Response();
    $response->headers->set('Content-Type', 'text/csv');
    $response->headers->set('Content-Disposition', 'attachment; filename="bat_tool_result.csv"');
    $response->setContent($csvData);

    return $response;
  }
}
