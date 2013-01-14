<?php

/**
 * @file
 * Contains the Ctools exportables UI for "currency_locale_pattern"
 * exportables.
 */

$plugin = array(
  'access' => 'currency.currency_locale_pattern.administer',
  'form' => array(
    'settings' => 'currency_form_currency_locale_pattern',
  ),
  'handler' => array(
    'class' => 'CurrencyLocalePatternCtoolsExportUI',
    'parent' => 'ctools_export_ui',
  ),
  'menu' => array(
    'menu item' => 'currency_locale_pattern',
    'menu prefix' => 'admin/config/regional/currency',
    'menu title' => 'Localization', 
  ),
  'schema' => 'currency_locale_pattern',
  'title plural' => t('locale patterns'),
  'title plural proper' => t('Locale patterns'),
  'title singular' => t('locale pattern'),
  'title singular proper' => t('Locale pattern'),
);