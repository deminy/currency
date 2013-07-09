<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyListController.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityListController;

/**
 * Defines the default list controller for ConfigEntity objects.
 */
class CurrencyListController extends ConfigEntityListController {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $row = parent::buildHeader();
    $row['id'] = t('Currency code');

    return $row;
  }
}
