<?php

/**
 * @file Contains
 * \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface.
 */

namespace Drupal\currency\Plugin\Currency\ExchangeRateProvider;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\currency\ExchangeRateProviderInterface as GenericExchangeRateProviderInterface;

/**
 * Describes a currency exchange rate provider plugin
 */
interface ExchangeRateProviderInterface extends GenericExchangeRateProviderInterface, PluginInspectionInterface {
}
