<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyLocaleListController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityListController;

/**
 * Defines the default list controller for ConfigEntity objects.
 */
class CurrencyLocaleListController extends ConfigEntityListController {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $row = array(
      'label' => t('Locale'),
    ) + parent::buildHeader();

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = array(
      'label' => $entity->label(),
    ) + parent::buildRow($entity);

    return $row;
  }
}
