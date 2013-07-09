<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyStorageController.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Config\Entity\ConfigStorageController;
use Drupal\currency\Usage;

/**
 * Defines the storage controller class for Currency entities.
 */
class CurrencyStorageController extends ConfigStorageController {

  /**
   * Overrides parent::buildQuery().
   */
  protected function buildQuery($ids, $revision_id = FALSE) {
    $currencies = parent::buildQuery($ids, $revision_id);
    foreach ($currencies as $currency) {
      $usages_data = $currency->usage;
      $currency->usage = array();
      foreach ($usages_data as $usage_data) {
        $currency->usage[] = new Usage($usage_data);
      }
    }

    return $currencies;
  }
}
