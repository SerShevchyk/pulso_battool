<?php

/**
 * @file
 * Contains \Drupal\pulso_battool\Entity\Controller\BatToolResultListBuilder.
 */

namespace Drupal\pulso_battool\Entity\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list controller for bat_tool_result entity.
 *
 * @ingroup pulso_battool
 */
class BatToolResultListBuilder extends EntityListBuilder {

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager')->getStorage($entity_type->id()),
      $container->get('url_generator')
    );
  }

  /**
   * Constructs a new BatToolResultListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * The entity type term.
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   * The entity storage class.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   * The url generator.
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, UrlGeneratorInterface $url_generator) {
    parent::__construct($entity_type, $storage);
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   *
   * We override ::render() so that we can add our own content above the table.
   * parent::render() is where EntityListBuilder creates the table using our
   * buildHeader() and buildRow() implementations.
   */
  public function render() {
    $build["title"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#attributes' => [
        'class' => ["bat-introduction"],
      ],
      '#value' => "Export Bat Tool Results",
    ];

    $form = \Drupal::formBuilder()->getForm('Drupal\pulso_battool\Form\BatToolResultExportForm');
    $renderer = \Drupal::service('renderer');
    $form = $renderer->renderPlain($form);

    $build['export'] = [
      '#markup' => $form
    ];

    $build["title2"] = [
      '#type' => 'html_tag',
      '#tag' => 'h2',
      '#attributes' => [
        'class' => ["bat-introduction"],
      ],
      '#value' => "Bat Tool Results",
    ];

    $build['table'] = parent::render();
    return $build;
  }

  /**
   * {@inheritdoc}
   *
   * Building the header and content lines for the dictionary_term list.
   *
   * Calling the parent::buildHeader() adds a column for the possible actions
   * and inserts the 'edit' and 'delete' links as defined for the entity type.
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['created'] = $this->t('Created');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\pulso_battool\Entity\BatToolResult */
    $row['id'] = $entity->id();
    $row['created'] = \Drupal::service('date.formatter')->format($entity->getCreatedTime(), 'd M Y');;
    return $row + parent::buildRow($entity);
  }

}
