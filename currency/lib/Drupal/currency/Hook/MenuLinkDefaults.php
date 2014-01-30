<?php

/**
 * @file
 * Contains \Drupal\currency\Hook\MenuLinkDefaults.
 */

namespace Drupal\currency\Hook;

/**
 * Implements hook_menu_link_defaults().
 *
 * @see currency_menu_link_defaults()
 */
class MenuLinkDefaults {

  /**
   * Invokes the implementation.
   */
  public function invoke() {
    $links['currency.currency.list'] = array(
      'route_name' => 'currency.currency.list',
      'link_title' => 'Currencies',
      'parent' => 'system.admin.config.regional',
    );

    $links['currency.amount_formatting'] = array(
      'description' => 'Configure amount formatting for different locales.',
      'link_title' => 'Currency amount formatting',
      'route_name' => 'currency.amount_formatting',
      'parent' => 'system.admin.config.regional',
    );

    $links['currency.exchange_rate_provider.config'] = array(
      'description' => 'Configure how currency exchange rates should be retrieved.',
      'route_name' => 'currency.exchange_rate_provider.config',
      'link_title' => 'Currency exchange',
      'parent' => 'system.admin.config.regional',
    );

    return $links;
  }

}
