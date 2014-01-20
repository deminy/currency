<?php

/**
 * @file
 * Contains \Drupal\currency\Hook\Menu.
 */

namespace Drupal\currency\Hook;

/**
 * Implements hook_menu().
 *
 * @see currency_menu()
 */
class Menu {

  /**
   * Invokes the implementation.
   */
  public function invoke() {
    // Currency entities.
    $items['admin/config/regional/currency'] = array(
      'route_name' => 'currency.currency.list',
      'title' => 'Currencies',
    );
    $items['admin/config/regional/currency/%/edit'] = array(
      'route_name' => 'currency.currency.edit',
      'title' => 'Edit a currency',
    );
    $items['admin/config/regional/currency/%/delete'] = array(
      'route_name' => 'currency.currency.delete',
      'title' => 'Delete a currency',
    );

    // Currency locales.
    $items['admin/config/regional/currency-localization/locale'] = array(
      'route_name' => 'currency.currency_locale.list',
      'title' => 'Currency formatting',
    );
    $items['admin/config/regional/currency-localization/locale/%/edit'] = array(
      'route_name' => 'currency.currency_locale.edit',
      'title' => 'Edit a currency locale',
    );
    $items['admin/config/regional/currency-localization/locale/%/delete'] = array(
      'route_name' => 'currency.currency_locale.delete',
      'title' => 'Delete a currency locale',
    );

    // Currency exchange rate providers.
    $items['admin/config/regional/currency-exchange'] = array(
      'description' => 'Configure how currency exchange rates should be retrieved.',
      'route_name' => 'currency.exchange_rate_provider.config',
      'title' => 'Currency exchange',
    );
    $items['admin/config/regional/currency-exchange/fixed'] = array(
      'description' => 'Administer fixed currency exchange rates.',
      'route_name' => 'currency.exchange_rate_provider.fixed_rates.overview',
      'title' => 'Fixed rates',
    );
    $items['admin/config/regional/currency-exchange/fixed/%/%'] = array(
      'route_name' => 'currency.exchange_rate_provider.fixed_rates.edit',
      'title' => 'Configure an exchange rate',
    );

    // Amount formatting.
    $items['admin/config/regional/currency-localization'] = array(
      'description' => 'Configure amount formatting for different locales.',
      'title' => 'Currency amount formatting',
      'route_name' => 'currency.amount_formatting',
    );

    return $items;
  }

}
