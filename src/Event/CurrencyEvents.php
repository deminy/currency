<?php

/**
 * @file
 * Contains \Drupal\currency\Event\CurrencyEvents.
 */

namespace Drupal\currency\Event;

/**
 * Defines Currency events.
 */
final class CurrencyEvents {

  /**
   * The name of the event that is fired to resolve the current country code.
   *
   * @see \Drupal\currency\Event\ResolveCountryCode
   */
  const RESOLVE_COUNTRY_CODE = 'drupal.currency.resolve_country_code';
}
