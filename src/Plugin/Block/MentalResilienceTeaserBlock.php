<?php

namespace Drupal\pulso_battool\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Provides a block teaser for MentalResilienceTeaserBlock.
 *
 * @Block(
 *   id = "mental_resilience_teaser_block",
 *   admin_label = @Translation("Mental Resilience Teaser Block"),
 * )
 */
class MentalResilienceTeaserBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $defaultConfiguration = \Drupal::config('pulso_battool.settings');

    return [
      "mental_resilience_teaser_block" => $defaultConfiguration->get("mental_resilience_teaser_block")
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->defaultConfiguration();

    return [
      '#theme' => 'mental_resilience_teaser_block',
      '#data' => $config["mental_resilience_teaser_block"] ?? [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();

    $form['mental_resilience_teaser_block']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config['mental_resilience_teaser_block']["title"] ?? '',
    ];

    $form['mental_resilience_teaser_block']['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $config['mental_resilience_teaser_block']["description"] ?? '',
    ];

    $form['mental_resilience_teaser_block']['button_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button Title'),
      '#default_value' => $config['mental_resilience_teaser_block']["button_title"] ?? '',
    ];

    $form['mental_resilience_teaser_block']['redirect_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect Url'),
      '#default_value' => $config['mental_resilience_teaser_block']["redirect_url"] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $values = $form_state->getValues();
    $this->configuration['mental_resilience_teaser_block'] = $values["mental_resilience_teaser_block"];
  }
}
