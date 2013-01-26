<?php

/**
 * @file
 * Contains Internationalization hook implementations.
 */

/**
 * Implements hook_i18n_object_info().
 */
function currency_i18n_object_info() {
  // Information for node type object, see i18n_node_i18n_object_info()
  $objects['currency'] = array(
    // Generic object properties, title, etc..
    'title' => t('Currency'),
    // Field to be used as key to index different node types
    'key' => 'type',
    // Mapping object fields and menu place holders
    'placeholders' => array(
    	'%currency' => 'ISO4217Code',
    ),
    // Path for automatically generated translation tabs. Note placeholders above are used here.
    'edit path' => 'admin/config/regional/currency/list/%currency/edit',
    'translate tab' => 'admin/config/regional/currency/list/%currency/edit/translate',
    // Metadata for string translation
    // In this case we are defining fields and keys for string translation's string names
    // String ids are of the form: [textgroup]:[type]:[key]:[property]
    // Thus in this case we'll have string names like
    // - node:type:story:name
    // - node:type:story:description
    'string translation' => array(
      'textgroup' => 'currency',
      'type' => 'currency',
      'properties' => array(
        'name' => t('Currency name'),
      ),
      'translate path' => 'admin/config/regional/currency/list/%currency/edit/translate/%i18n_language',
    )
  );

  return $objects;
}

/**
* Implements hook_i18n_string_info().
*/
function currency_i18n_string_info() {
  $groups['currency'] = array(
    'title' => t('Currencies'),
    // @debug
    // 'format' => FALSE,
    // 'list' => TRUE,
  );

  return $groups;
}
