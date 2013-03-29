<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\currency\exchanger\BartFeenstraCurrency.
 */

namespace Drupal\currency\Plugin\currency\exchanger;

use Drupal\Component\Annotation\Plugin;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Annotation\Translation;
use Drupal\currency\Exchanger\ExchangerInterface;

/**
 * Provides fixed exchange rates as provided by bartfeenstra/currency.
 *
 * @Plugin(
 *   id = "currency_bartfeenstra_currency",
 *   label = @Translation("Historical rates")
 * )
 */
class BartFeenstraCurrency extends PluginBase implements ExchangerInterface {

  /**
   * Implements \Drupal\currency\Exchanger\ExchangerInterface::load().
   */
  function load($currency_code_from, $currency_code_to) {
    if (in_array($currency_code_from, \BartFeenstra\Currency\Currency::resourceListAll()) && in_array($currency_code_to, \BartFeenstra\Currency\Currency::resourceListAll())) {
      // Check if the requested rate is available.
      $currency_from = new \BartFeenstra\Currency\Currency();
      $currency_from->resourceLoad($currency_code_from);
      if ($currency_from && isset($currency_from->exchangeRates[$currency_code_to])) {
        return $currency_from->exchangeRates[$currency_code_to];
      }

      // Conversion rates are two-way. If a reverse rate is unavailable, set it.
      $currency_to = new \BartFeenstra\Currency\Currency();
      $currency_to->resourceLoad($currency_code_to);
      if ($currency_to && isset($currency_to->exchangeRates[$currency_code_from])) {
        return bcdiv(1, $currency_to->exchangeRates[$currency_code_from], CURRENCY_BCMATH_SCALE);
      }
    }

    // There is no available exchange rate.
    return FALSE;
  }

  /**
   * Implements \Drupal\currency\Exchanger\ExchangerInterface::loadMultiple().
   */
  function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to) {
        $rates[$currency_code_from][$currency_code_to] = self::load($currency_code_from, $currency_code_to);
      }
    }

    return $rates;
  }
}
