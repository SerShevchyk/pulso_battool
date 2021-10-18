<?php
/**
 * @file
 * Contains \Drupal\pulso_battool\Form\BatToolResultSettingsForm.
 */

namespace Drupal\pulso_battool\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContentEntityExampleSettingsForm.
 *
 * @package Drupal\pulso_battool\Form
 *
 * @ingroup pulso_battool
 */
class BatToolResultSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'bat_tool_result_term_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Empty implementation of the abstract submit class.
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['bat_tool_result_term_settings']['#markup'] = 'Settings form for Bat Tool Result. Manage field settings here.';
    return $form;
  }

}
