<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\views\field\Amount.
 */

namespace Drupal\currency\Plugin\views\field;

use Drupal\Component\Annotation\PluginID;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * A Views field handler for currency amounts.
 *
 * This handler has two definition properties:
 * - currency_code
 * - currency_code_field
 * - currency_code_table
 * See $self::defaultDefinition() for a detailed explanation.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("currency_amount")
 */
class Amount extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->definition += $this->defaultDefinition();
  }

  /**
   * Returns default definition values.
   *
   * @return array
   */
  function defaultDefinition() {
    return array(
      // A default currency code to use for the amounts.
      'currency_code' => 'XXX',
      // The name of the database field the currency code is in.
      'currency_code_field' => NULL,
      // The name of the database table currency_field is in. Defaults to the
      // same table this handler is used on.
      'currency_code_table' => NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  function query() {
    $this->ensureMyTable();
    if ($this->definition['currency_code_field']) {
      $this->additional_fields['currency_code_field'] = array(
        'field' => $this->definition['currency_code_field'],
        'table' => $this->definition['currency_code_table'] ? $this->definition['currency_code_table'] : $this->tableAlias,
      );
    }
    parent::query();
  }

  /**
   * {@inheritdoc}
   *
   * @var array
   */
  function defineOptions() {
    $options = parent::defineOptions();

    // Whether to round amounts.
    $options['currency_round'] = array(
      'default' => FALSE,
    );

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  function buildOptionsForm(&$form, &$form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['currency_round'] = array(
      '#type' => 'checkbox',
      '#title' => t('Round amounts based on their currencies'),
      '#default_value' => $this->options['currency_round'],
    );
  }

  /**
   * {@inheritdoc}
   */
  function render($values) {
    $currency = $this->getCurrency($values);
    $amount = $this->getAmount($values);
    if ($this->options['currency_round']) {
      $amount = $currency->roundAmount($amount);
    }

    return $currency->format($amount);
  }

  /**
   * Loads the Currency for this field.
   *
   * @throws RuntimeException
   *
   * @param stdClass $values
   *   A values object as received by $this->render().
   *
   * @return Currency
   */
  function getCurrency(\stdClass $values) {
    $currency = NULL;

    if ($this->definition['currency_code_field']) {
      $currency_code = $this->getValue($values, 'currency_code_field');
      if ($currency_code) {
        $currency = entity_load('currency', $currency_code);
      }
    }
    if (!$currency) {
      $currency = entity_load('currency', $this->definition['currency_code']);
    }
    if (!$currency) {
      $currency = entity_load('currency', 'XXX');
    }
    if (!$currency) {
      throw new RuntimeException(t('Could not load currency with ISO 4217 code XXX.'));
    }

    return $currency;
  }

  /**
   * Gets this field's amount.
   *
   * If the amount cannot be fetched from your implementation's database field
   * as a numeric string, you should override this method so it returns a
   * numeric/decimal representation of the amount.
   *
   * @param stdClass $values
   *   A values object as received by $this->render().
   *
   * @return string
   *   A numeric string.
   */
  function getAmount(\stdClass $values) {
    return $this->getValue($values);
  }
}
