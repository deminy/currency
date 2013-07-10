<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\views\field\Currency.
 */

namespace Drupal\currency\Plugin\views\field;

use Drupal\Component\Annotation\PluginID;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * A Views field handler to get properties from currencies.
 *
 * This handler has one definition property:
 * - currency_property: the name of the Currency class property of which to
 *   display the value, which must be a scalar.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("currency")
 */
class Currency extends FieldPluginBase {

  /**
   * {@inheritdoc}
   *
   * @throws \InvalidArgumentException
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    if (!isset($configuration['currency_property'])) {
      throw new \InvalidArgumentException('Missing currency property definition.');
    }
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  function render($values) {
    $currency_code = $this->getValue($values);
    $currency = entity_load('currency', $currency_code);
    $property = $this->configuration['currency_property'];
    if ($currency) {
      if ($property == 'label') {
        return $currency->label();
      }
      else {
        return $currency->get($property);
      }
    }
    else {
      return t('Unknuwn currency %currency_code', array(
        '%currency_code' => $currency_code,
      ));
    }
  }
}
