<?php

/**
 * @file
 * Contains Token API hook implementations.
 */

/**
 * Implements hook_token_info().
 */
function currency_token_info() {
  // Token groups.
  $token_info['types']['currency'] = array(
    'description' => t('ISO 4217 currencies.'),
    'name' => t('Currencies'),
    'needs-data' => 'currency',
  );

  // Tokens.
  $token_info['tokens']['code'] = array(
    'description' => t('The ISO 4217 currency code.'),
    'name' => t('Currency code'),
    'type' => 'text',
  );
  $token_info['tokens']['number'] = array(
    'description' => t('The ISO 4217 currency number.'),
    'name' => t('Currency number'),
    'type' => 'text',
  );
  $token_info['tokens']['subunits'] = array(
    'description' => t('The number of subunits.'),
    'name' => t('Subunits'),
    'type' => 'text',
  );
  $token_info['tokens']['sign'] = array(
    'name' => t('Sign'),
    'type' => 'text',
  );
  $token_info['tokens']['title'] = array(
    'name' => t('Name'),
    'type' => 'text',
  );

  return $token_info;
}

/**
 * Implements hook_tokens().
 */
function currency_tokens($type, array $tokens, array $data = array(), array $options = array()) {
  $replacements = array();

  if ($type == 'currency' && isset($data['currency'])) {
    $currency_code = $data['currency'];
    if (isset($tokens['code'])) {
      $replacements[$tokens['code']] = $data['currency'];
      unset($tokens['code']);
    }
    if ($tokens) {
      ctools_include('export');
      $currency = currency_load($data['currency']);
      $map = array(
        'number' => 'ISO4217Number',
        'subunits' => 'subunits',
        'sign' => 'sign',
        'title' => 'title',
      );
      foreach ($tokens as $token => $original) {
        if (isset($map[$token])) {
          $replacements[$original] = $currency->{$map[$token]};
        }
      }
    }
  }

  return $replacements;
}
