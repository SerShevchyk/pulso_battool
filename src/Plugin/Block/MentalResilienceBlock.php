<?php

namespace Drupal\pulso_battool\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Url;

/**
 * Provides a block for MentalResilienceBlock.
 *
 * @Block(
 *   id = "mental_resilience_block",
 *   admin_label = @Translation("Mental Resilience Block"),
 * )
 */
class MentalResilienceBlock extends BlockBase {

  protected $step = "stress";

  /**
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  private $defaultConfiguration;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $this->defaultConfiguration = \Drupal::config('pulso_battool.settings');

    return [
      "mental_resilience_block" => $this->defaultConfiguration->get("mental_resilience_block"),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();

    $mentalResilienceForm = \Drupal::formBuilder()->getForm('Drupal\pulso_battool\Form\MentalResilienceForm');
    $renderer = \Drupal::service('renderer');
    $mentalResilienceForm = $renderer->renderPlain($mentalResilienceForm);

    return [
      '#theme' => 'mental_resilience_block',
      '#data' => $config["mental_resilience_block"] ?? [],
      '#mentalResilienceForm' => $mentalResilienceForm ?? [],
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

    $form['stress'] = [
      "#type" => "details",
      '#title' => $this->t('Stress Result Descriptions'),
    ];

    $form['stress']["green"] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Stress Result: Green'),
      '#default_value' => $config['mental_resilience_block']["block_settings"]["stress"]["result"]["green"]["value"] ?? '',
    ];

    $form['stress']["orange"] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Stress Result: Orange'),
      '#default_value' => $config['mental_resilience_block']["block_settings"]["stress"]["result"]["orange"]["value"] ?? '',
    ];

    $form['stress']["red"] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Stress Result: Red'),
      '#default_value' => $config['mental_resilience_block']["block_settings"]["stress"]["result"]["red"]["value"] ?? '',
    ];

    $form['burnout'] = [
      "#type" => "details",
      '#title' => $this->t('Burn Out Result Descriptions'),
    ];

    $form['burnout']["green"] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Burn Out Result: Green'),
      '#default_value' => $config['mental_resilience_block']["block_settings"]["burnout"]["result"]["green"]["value"] ?? '',
    ];

    $form['burnout']["orange"] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Burn Out Result: Orange'),
      '#default_value' => $config['mental_resilience_block']["block_settings"]["burnout"]["result"]["orange"]["value"] ?? '',
    ];

    $form['burnout']["red"] = [
      '#type' => 'text_format',
      '#format' => 'full_html',
      '#title' => $this->t('Burn Out Result: Red'),
      '#default_value' => $config['mental_resilience_block']["block_settings"]["burnout"]["result"]["red"]["value"] ?? '',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);

    $values = $form_state->getValues();

    $config = $this->getConfiguration();
    $configEditable = \Drupal::configFactory()->getEditable('pulso_battool.settings');


    foreach ($values as $sectionKey => $value) {
      if (is_array($value)) {
        foreach ($value as $key => $item) {
          $config["mental_resilience_block"]["block_settings"][$sectionKey]["result"][$key]["value"] = $item["value"];
          $configEditable->set("mental_resilience_block.block_settings.$sectionKey.result.$key.value", $item["value"]);
        }
      }
    }
    $this->configuration['mental_resilience_block'] = $config["mental_resilience_block"];
    $configEditable->save();
  }

}
