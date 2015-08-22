<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use BartFeenstra\CurrencyExchange\ExchangeRateProviderInterface as GenericExchangeRateProviderInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Defines a currency exchange rate provider plugin.
 */
interface ExchangeRateProviderInterface extends GenericExchangeRateProviderInterface, PluginInspectionInterface {
}
