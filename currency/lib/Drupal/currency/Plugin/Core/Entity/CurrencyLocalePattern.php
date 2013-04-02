<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use BartFeenstra\CLDR\CurrencyFormatter;
use Drupal\Component\Annotation\Plugin;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\currency\Plugin\Core\Entity\Currency;

/**
 * Defines a currency entity class.
 *
 * @Plugin(
 *   config_prefix = "currency.currency_locale_pattern",
 *   controller_class = "Drupal\Core\Config\Entity\ConfigStorageController",
 *   entity_keys = {
 *     "id" = "locale",
 *     "label" = "locale",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   fieldable = FALSE,
 *   id = "currency_locale_pattern",
 *   label = @Translation("Currency locale pattern"),
 *   module = "currency"
 * )
 */
class CurrencyLocalePattern extends ConfigEntityBase {

  /**
   * The decimal separator character.
   *
   * @var string
   */
  public $decimalSeparator = NULL;

  /**
   * The grouping separator character.
   *
   * @var string
   */
  public $groupingSeparator = NULL;

  /**
   * The locale identifier.
   *
   * The identifier consists of a language code, an underscore, and a country
   * code. Examples: nl_NL, en_US.
   *
   * @var string
   */
  public $locale = NULL;

  /**
   * The Unicode CLDR number pattern.
   *
   * @var string
   */
  public $pattern = NULL;

  /**
   * Whether the locale pattern is enabled.
   *
   * @var string
   */
  public $status = NULL;

  /**
   * The UUID for this entity.
   *
   * @var string
   */
  public $uuid = NULL;

  /**
   * Overrides parent::id().
   */
  public function id() {
    return isset($this->locale) ? $this->locale : NULL;
  }

  /**
   * Formats an amount using this pattern.
   *
   * @param Currency $currency
   * @param string $amount
   *   A numeric string.
   *
   * @return string
   */
  function format(Currency $currency, $amount) {
    static $formatter = NULL;

    if (is_null($formatter) || $formatter->pattern != $this->pattern) {
      $formatter = new CurrencyFormatter($this->pattern, array(
        CurrencyFormatter::SYMBOL_SPECIAL_DECIMAL_SEPARATOR => $this->decimalSeparator,
        CurrencyFormatter::SYMBOL_SPECIAL_GROUPING_SEPARATOR => $this->groupingSeparator,
      ));
    }

    $formatted = $formatter->format($amount, $currency->sign);
    $formatted = str_replace(array('[XXX]', '[999]'), array($currency->currencyCode, $currency->currencyNumber), $formatted);

    return $formatted;
  }
}
