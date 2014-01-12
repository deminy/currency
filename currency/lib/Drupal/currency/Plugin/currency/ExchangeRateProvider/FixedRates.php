<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginBase;

/**
 * Provides manually entered exchange rates.
 *
 * @CurrencyExchangeRateProvider(
 *   id = "currency_fixed_rates",
 *   label = @Translation("Fixed rates"),
 *   operations = {
 *     "admin/config/regional/currency-exchange/fixed" = @Translation("configure"),
 *   }
 * )
 */
class FixedRates extends PluginBase implements ExchangeRateProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function load($currency_code_from, $currency_code_to) {
    $rates = $this->loadAll();
    if (isset($rates[$currency_code_from]) && isset($rates[$currency_code_from][$currency_code_to])) {
      return $rates[$currency_code_from][$currency_code_to];
    }
    // Calculate the reverse on the fly, because adding it to the statically
    // cached data would require additional checks when deleting rates, to see
    // if the they are reversed from other rates or are originals.
    elseif (isset($rates[$currency_code_to]) && isset($rates[$currency_code_to][$currency_code_from])) {
      return bcdiv(1, $rates[$currency_code_to][$currency_code_from], CURRENCY_BCMATH_SCALE);
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $currency_codes) {
    $rates = array();
    foreach ($currency_codes as $currency_code_from => $currency_codes_to) {
      foreach ($currency_codes_to as $currency_code_to) {
        $rates[$currency_code_from][$currency_code_to] = $this->load($currency_code_from, $currency_code_to);
      }
    }

    return $rates;
  }

  /**
   * Loads all available exchange rates.
   *
   * @return array
   *   The array structure is identical to the return value of
   *   self::loadMultiple().
   */
  public function loadAll() {
    $config = \Drupal::config('currency.exchanger.fixed_rates');

    return $config->get('rates');
  }

  /**
   * Saves a nexchange rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   * @param string $rate
   *
   * @return NULL
   */
  public function save($currency_code_from, $currency_code_to, $rate) {
    $config = \Drupal::config('currency.exchanger.fixed_rates');
    $rates = $config->get('rates');
    $rates[$currency_code_from][$currency_code_to] = $rate;
    $config->set('rates', $rates);
    $config->save();
  }

  /**
   * Deletes an exchange rate.
   *
   * @param string $currency_code_from
   * @param string $currency_code_to
   *
   * @return NULL
   */
  public function delete($currency_code_from, $currency_code_to) {
    $config = \Drupal::config('currency.exchanger.fixed_rates');
    $rates = $config->get('rates');
    unset($rates[$currency_code_from][$currency_code_to]);
    $config->set('rates', $rates);
    $config->save();
  }
}
