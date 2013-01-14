<?php

/**
 * @file
 * Contains class CurrencyAmountViewsHandlerField.
 */

/**
 * A Views field handler for currency amounts.
 *
 * This handler has two definition properties, that function identically to and
 * provide default values for the handler's options:
 * - currency_code
 * - currency_code_field
 *   See $this->option_definition() for a detailed explanation.
 */
class CurrencyAmountViewsHandlerField extends views_handler_field {

  /**
   * Overrides parent::option_definition().
   */
  function option_definition() {
    $options = parent::option_definition();

    // The ISO 4217 currency code of the currency to use to display the amount.
    $options['currency_code'] = array(
      'default' => !empty($this->definition['currency_code']) ? $this->definition['currency_code'] : 'XXX',
    );
    // The name of the fields that contains ISO 4217 currency codes. For every
    // row that does not contain a valid code, the value of the "currency_code"
    // option is used instead.
    $options['currency_code_field'] = array(
      'default' => !empty($this->definition['currency_code_field']) ? $this->definition['currency_code_field'] : '',
    );

    return $options;
  }

  /**
   * Overrides parent::options_form().
   */
  function options_form(&$form, &$form_state) {
    parent::options_form($form, $form_state);
    // The currency code.
    $options = array();
    foreach (ctools_export_crud_load_all('currency') as $currency) {
      $options[$currency->ISO4217Code] = $currency->title;
    }
    asort($options);
    $handler_options = $this->option_definition();
    $form['currency_code'] = array(
      '#type' => 'select',
      '#title' => t('Default currency'),
      '#options' => $options,
      '#default_value' => $this->options['currency_code'],
      '#required' => TRUE,
    );

    // The currency code field.
    $options = array();
    foreach ($this->view->display_handler->get_handlers('field') as $handler) {
      // We only allow fields up to this one.
      if ($handler->field == $this->options['id']) {
        break;
      }
      $options[$handler->table . '_' . $handler->field] = $handler->ui_name();
    }
    asort($options);
    $form['currency_code_field'] = array(
      '#type' => 'select',
      '#title' => t('Currency field'),
      '#description' => t('Another field that contains the ISO 4217 currency codes of the currencies to use for displaying the amounts. If no field is specified, or if the specified field does not contain a valid currency code, then the default currency will be used.'),
      '#options' => $options,
      '#empty_value' => '',
      '#default_value' => $this->options['currency_code_field'],
    );
  }

  /**
   * Overrides parent::render().
   *
   * @throws RuntimeException
   */
  function render($values) {
    $currency = $this->getCurrency($values);
    $amount = $this->getAmount($this->get_value($values));

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
  function getCurrency(stdClass $values) {
    $currency = NULL;

    // Check if the configured field exists, because the option might be
    // outdated.
    $currency_code_field = $this->options['currency_code_field'];
    if (isset($values->{$currency_code_field})) {
      $currency_code = $values->{$currency_code_field};
      $currency = ctools_export_crud_load('currency', $currency_code);
    }
    if (!$currency) {
      $currency = ctools_export_crud_load('currency', $this->options['currency_code']);
    }
    if (!$currency) {
      $currency = ctools_export_crud_load('currency', 'XXX');
    }
    if (!$currency) {
      throw new RuntimeException(t('Could not load currency with ISO 4217 code XXX.'));
    }

    return $currency;
  }

  /**
   * Gets this field's amount as a float.
   *
   * If the amount cannot be fetched from your implementation's database field
   * as a float or a format that can easily be parsed to a float, such as a
   * string or an integer, you should override this method so it returns a
   * float representation of the amount.
   *
   * @param mixed $amount_value
   *   The amount as Views fetched it from the database.
   *
   * @return float
   */
  function getAmount($amount_value) {
    // Even if the amount is stored as a float, in most cases it is returned as
    // a string, so cast it.
    return (float) $amount_value;
  }
}
