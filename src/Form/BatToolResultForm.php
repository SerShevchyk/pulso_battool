<?php
/**
 * @file
 * Contains Drupal\pulso_battool\Form\BatToolResultForm.
 */

namespace Drupal\pulso_battool\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the bat_tool_result entity edit forms.
 *
 * @ingroup pulso_battool
 */
class BatToolResultForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\pulso_battool\Entity\BatToolResult */
    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    // Redirect to term list after save.
    $form_state->setRedirect('entity.bat_tool_result.collection');
    $entity = $this->getEntity();
    $entity->save();
  }

}
