<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\LocaleDelegatorTest.
 */

namespace Drupal\currency\Tests;

use Drupal\currency\LocaleDelegator;
use Drupal\simpletest\WebTestBase;

/**
 * Tests class Drupal\currency\LocaleDelegator.
 */
class LocaleDelegatorTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Implements DrupalTestCase::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Drupal\currency\LocaleDelegator',
      'group' => 'Currency',
    );
  }

  /**
   * Tests getCountryCode().
   */
  function testGetCountryCode() {
    $delegator = drupal_container()->get('currency.locale_delegator');

    // Test getting the default.
    $this->assertEqual($delegator->getCountryCode(), NULL);

    // Test setting a custom country.
    $delegator->setCountryCode('NL');
    $this->assertEqual($delegator->getCountryCode(), 'NL');
  }

  /**
   * Tests getLocalePattern().
   */
  function testGetLocalePattern() {
    $delegator = drupal_container()->get('currency.locale_delegator');

    // Test loading the fallback locale.
    $locale_pattern = $delegator->getLocalePattern();
    $this->assertEqual($locale_pattern->locale, $delegator::DEFAULT_LOCALE);

    // Test loading the locale based on the site's default country.
    config('system.data')->set('country.default', 'US');
    $locale_pattern = $delegator->getLocalePattern();
    $this->assertEqual($locale_pattern->locale, 'en_US');

    // Test loading a locale pattern based on request-specific settings.
    $delegator->setCountryCode('IN');
    $locale_pattern = $delegator->getLocalePattern();
    $this->assertEqual($locale_pattern->locale, 'en_IN');

    // Test changing the request-specific country.
    $delegator->setCountryCode('US');
    $locale_pattern = $delegator->getLocalePattern();
    $this->assertEqual($locale_pattern->locale, $delegator::DEFAULT_LOCALE);
  }
}
