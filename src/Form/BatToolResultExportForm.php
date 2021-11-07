<?php

namespace Drupal\pulso_battool\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Response;

/**
 * Implements a MentalResilienceStressForm.
 */
class BatToolResultExportForm extends FormBase {

  /**
   * @var array|mixed|null
   */
  private $options;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['start_date'] = [
      '#type' => 'date',
      '#title' => 'Start Date',
      '#format' => 'm/d/Y',
      '#required' => FALSE,
      '#default_value' => date("m/d/Y", time()),
      '#date_date_format' => 'm/d/Y',
    ];

    $form['end_date'] = [
      '#type' => 'date',
      '#title' => 'End Date',
      '#format' => 'm/d/Y',
      '#required' => FALSE,
      '#default_value' => date("m/d/Y", time()),
      '#date_date_format' => 'm/d/Y',
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Export'),
      '#submit' => ['::submitForm'],
    ];

    $form['actions']['remove'] = [
      '#type' => 'submit',
      '#value' => $this->t('Remove'),
      '#submit' => ['::submitRemoveForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'bat_tool_result_export_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $url = Url::fromUserInput("/exports/bat-tool-result", [
      "query" => [
        "start_date" => $values["start_date"],
        "end_date" => $values["end_date"]
      ]
    ]);

    $form_state->setRedirectUrl($url);
  }

  /**
   * Remove results
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function submitRemoveForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $startDate = $values["start_date"];
    $endDate = $values["end_date"];

    if (!isset($startDate) || empty($startDate) || !isset($endDate) || empty($endDate)) {
      return $form_state->setRedirect("pulso_battool.bat_tool_result_remove");
    }
    else {
      $url = Url::fromUserInput("/bat-tool-result/remove", [
        "query" => [
          "start_date" => $values["start_date"],
          "end_date" => $values["end_date"]
        ]
      ]);

      $form_state->setRedirectUrl($url);
    }

  }
}
