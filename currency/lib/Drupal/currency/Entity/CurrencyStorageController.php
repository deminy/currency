<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\CurrencyStorageController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigStorageController;
use Drupal\currency\Usage;

/**
 * Defines the storage controller class for Currency entities.
 */
class CurrencyStorageController extends ConfigStorageController {

  /**
   * {@inheritdoc}
   */
  protected function buildQuery($ids, $revision_id = FALSE) {
    /** @var \Drupal\currency\Entity\CurrencyInterface[] $currencies */
    $currencies = parent::buildQuery($ids, $revision_id);
    foreach ($currencies as $currency) {
      $usages_data = $currency->getUsages();
      $usages = array();
      foreach ($usages_data as $usage_data) {
        $usage = new Usage();
        $usage->setStart($usage_data['start'])
          ->setEnd($usage_data['end'])
          ->setCountryCode($usage_data['countryCode']);
        $usages[] = $usage;
      }
      $currency->setUsages($usages);
    }

    return $currencies;
  }
}
