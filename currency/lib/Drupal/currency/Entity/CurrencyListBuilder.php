<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyListController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a list builder for currencies.
 */
class CurrencyListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = array(
      'id' => t('Currency code'),
      'label' => t('Name'),
    ) + parent::buildHeader();

    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row = array(
      'id' => $entity->id(),
      'label' => $entity->label(),
    ) + parent::buildRow($entity);

    return $row;
  }
}
