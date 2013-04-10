<?php

/**
 * @file
 * Definition of Drupal\currency\CurrencyLocalePatternListController.
 */

namespace Drupal\currency;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Config\Entity\ConfigEntityListController;

/**
 * Defines the default list controller for ConfigEntity objects.
 */
class CurrencyLocalePatternListController extends ConfigEntityListController {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $row = parent::buildHeader();
    unset($row['id']);
    $row['label'] = t('Locale');

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = parent::buildRow($entity);
    unset($row['id']);

    return $row;
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);
    unset($operations['enable']);
    unset($operations['disable']);

    return $operations;
  }
}
