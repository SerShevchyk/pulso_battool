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
class BatToolResultRemove extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Build.
   */
  public function build() {
    $form = \Drupal::formBuilder()->getForm('Drupal\pulso_battool\Form\BatToolResultsRemoveAll');

    return $form;
  }
}
