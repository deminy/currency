<?php

/**
 * @file
 * Views integration.
 */

/**
 * Implements hook_views_data().
 */
function currency_exchange_rate_db_table_views_data() {
  // Table configuration.
  $data['currency_exchange_rate_db_table']['table'] = array(
    'base' => array(
      'title' => t('Currency exchange rates'),
    ),
    'group' => t('Currency'),
  );

  // Field configuration.
  $properties = array(
    'currency_code_from' => array(
      'currencyCode' => t('Source currency code'),
      'currencyNumber' => t('Source currency number'),
      'subunits' => t('Source currency number subunits'),
      'sign' => t('Source currency sign'),
      'label' => t('Source currency name'),
    ),
    'currency_code_to' => array(
      'currencyCode' => t('Destination currency code'),
      'currencyNumber' => t('Destination currency number'),
      'subunits' => t('Destination currency number of subunits'),
      'sign' => t('Destination currency sign'),
      'label' => t('Destination currency name'),
    ),
  );
  foreach ($properties as $field => $field_properties) {
    foreach ($field_properties as $property => $title) {
      // Don't alias the currency code.
      $suffix = $property == 'currencyCode' ? '' : '_' . $property;
      $data['currency_exchange_rate_db_table'][$field . $suffix] = array(
        'title' => $title,
        'real field' => $field,
        'field' => array(
          'id' => 'currency',
          'currency_property' => $property,
        ),
      );
    }
  }
  $data['currency_exchange_rate_db_table']['rate'] = array(
    'title' => t('Conversion rate'),
    'field' => array(
      'id' => 'numeric',
      'float' => TRUE,
    ),
    'sort' => array(
      'id' => 'numeric',
    ),
    'filter' => array(
      'id' => 'numeric',
    ),
    'argument' => array(
      'id' => 'numeric',
    ),
  );
  $data['currency_exchange_rate_db_table']['rate_amount'] = array(
    'title' => t('Conversion rate (as an amount)'),
    'real field' => 'rate',
    'field' => array(
      'id' => 'currency_amount',
      'currency_code_field' => 'currency_code_to',
    ),
  );
  $data['currency_exchange_rate_db_table']['currency_from'] = array(
    'title' => t('Source currency'),
    'real field' => 'currency_code_from',
    'filter' => array(
      'id' => 'currency',
    ),
  );
  $data['currency_exchange_rate_db_table']['currency_to'] = array(
    'title' => t('Destination currency'),
    'real field' => 'currency_code_to',
    'filter' => array(
      'id' => 'currency',
    ),
  );

  return $data;
}
