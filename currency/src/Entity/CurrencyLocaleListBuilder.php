<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyLocaleListBuilder.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a list builder for currency locales.
 */
class CurrencyLocaleListBuilder extends ConfigEntityListBuilder {

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
