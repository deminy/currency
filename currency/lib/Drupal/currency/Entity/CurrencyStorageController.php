<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyStorageController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigStorageController;
use Drupal\currency\Usage;
use Drupal\Core\Config\Config;

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
      $usages_data = $currency->getUsage();
      $usage = array();
      foreach ($usages_data as $usage_data) {
        $usage[] = new Usage($usage_data);
      }
      $currency->setUsage($usage);
    }

    return $currencies;
  }
}
