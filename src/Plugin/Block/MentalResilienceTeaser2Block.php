<?php

namespace Drupal\pulso_battool\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;
use Drupal\file\Entity\File;

/**
 * Provides a block teaser for MentalResilienceTeaser2Block.
 *
 * @Block(
 *   id = "mental_resilience_teaser_2_block",
 *   admin_label = @Translation("Mental Resilience Teaser 2 Block"),
 * )
 */
class MentalResilienceTeaser2Block extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $defaultConfiguration = \Drupal::config('pulso_battool.settings');

    return [
      "mental_resilience_teaser_2_block" => $defaultConfiguration->get("mental_resilience_teaser_2_block")
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $build = [
      '#theme' => 'mental_resilience_teaser_2_block',
      '#data' => $config["mental_resilience_teaser_2_block"] ?? [],
    ];

    if ($config['mental_resilience_teaser_2_block']['image'] ?? false) {
      $image_field = $config['mental_resilience_teaser_2_block']['image'];
      $image_uri = File::load($image_field[0]);
      if ($image_uri) {
        $build['#data']['image_uri'] = $image_uri->uri->value;
      }
    } else {
        $defaultConfiguration = $this->defaultConfiguration();
        $build['#data']['image_uri'] = drupal_get_path('module', 'pulso_battool') . '/' . $defaultConfiguration['mental_resilience_teaser_2_block']['image'];
    }

    return $build;
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

    $form['mental_resilience_teaser_2_block']['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#default_value' => $config['mental_resilience_teaser_2_block']["title"] ?? '',
    ];

    $form['mental_resilience_teaser_2_block']['description'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Description'),
      '#default_value' => $config['mental_resilience_teaser_2_block']["description"] ?? '',
    ];

    $form['mental_resilience_teaser_2_block']['button_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Button Title'),
      '#default_value' => $config['mental_resilience_teaser_2_block']["button_title"] ?? '',
    ];

    $form['mental_resilience_teaser_2_block']['redirect_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Redirect Url'),
      '#default_value' => $config['mental_resilience_teaser_2_block']["redirect_url"] ?? '',
    ];

    $form['mental_resilience_teaser_2_block']['image'] = [
        '#type' => 'managed_file',
        '#upload_location' => 'public://images/',
        '#title' => $this->t('Image'),
        '#description' => $this->t("Image to show in the teaser block"),
        '#default_value' => $config['mental_resilience_teaser_2_block']["image"] ?? '',
        '#upload_validators' => [
          'file_validate_extensions' => ['gif png jpg jpeg'],
          'file_validate_size' => [25600000],
        ],
        '#states'        => [
          'visible'      => [
            ':input[name="image_type"]' => ['value' => $this->t('Upload New Image(s)')],
          ]
        ]
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $values = $form_state->getValues();
    $this->configuration['mental_resilience_teaser_2_block'] = $values["mental_resilience_teaser_2_block"];

    if ($values['mental_resilience_teaser_2_block']['image'][0] ?? false) {
        $file = File::load($values['mental_resilience_teaser_2_block']['image'][0]);
        $file->setPermanent();
        $file->save();
    }
  }
}
