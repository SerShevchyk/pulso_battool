<?php

namespace Drupal\pulso_battool\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;

/**
 * Implements a MentalResilienceStressForm.
 */
class BatToolResultsRemoveAll extends FormBase {

  /**
   * @var array|mixed|null
   */
  private $options;

  /**
   * @var bool
   */
  private $all;

  /**
   * @var mixed
   */
  private $startDate;

  /**
   * @var mixed
   */
  private $endDate;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->startDate = \Drupal::request()->query->get('start_date');
    $this->endDate = \Drupal::request()->query->get('end_date');
    $this->all = empty($this->startDate) || empty($this->endDate);

    $form['text'] = [
      '#type' => 'html_tag',
      '#tag' => 'h3',
      '#value' => $this->all ? $this->t("Are you sure you want to delete all results?") : $this->t('Are you sure you want to delete all entries for the selected time period?')
    ];

    $form['actions']['remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove'),
      '#submit' => ['::submitRemoveForm'],
    ];

    $form['actions']['cancel'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel'),
      '#submit' => ['::submitCancelForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bat_tool_result_remove_all';
  }

  /**
   * {@inheritdoc}
   */
  public function submitCancelForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect("entity.bat_tool_result.collection");
  }

  /**
   * Remove results.
   */
  public function submitRemoveForm(array &$form, FormStateInterface $form_state) {
    $db = \Drupal::database();
    $query = $db->select('bat_tool_result', 'btr');
    $query->leftJoin('bat_tool_result_field_data', 'btrfd', 'btrfd.id = btr.id');
    $query->fields('btr', ['id']);

    if (!$this->all) {
      $query->condition('btrfd.created', strtotime($this->startDate), '>=');
      if ($this->startDate == $this->endDate) {
        $this->endDate = strtotime($this->endDate) + 86400;
      }
      $query->condition('btrfd.created', $this->endDate, '<=');
    }

    $ids = $query->execute()->fetchCol();
    $storage_handler = \Drupal::entityTypeManager()->getStorage("bat_tool_result");
    $entities = $storage_handler->loadMultiple($ids);
    $storage_handler->delete($entities);

    \Drupal::messenger()->addStatus("Entities were removed successfully");

    $form_state->setRedirect("entity.bat_tool_result.collection");
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

}
