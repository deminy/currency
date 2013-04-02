<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\Core\Entity\CurrencyLocalePatternCRUD.
 */

namespace Drupal\currency\Tests\Plugin\Core\Entity;

use Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern;
use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern.
 */
class CurrencyLocalePatternCRUD extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Overrides parent::getInfo().
   */
  public static function getInfo() {
    return array(
      'name' => 'Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern entity CRUD',
      'group' => 'Currency',
    );
  }

  /**
   * Test CRUD functionality.
   */
  function testCRUD() {
    // Test loading a default currency locale pattern.
    $locale_pattern_loaded = entity_load('currency_locale_pattern', 'nl_NL');
    $this->assertTrue($locale_pattern_loaded instanceof CurrencyLocalePattern);

    $locale = 'xx_XX';

    // Test that no locale pattern with this locale code exists yet.
    $config = config('currency.currency_locale_pattern.' . $locale);
    $this->assertIdentical($config->get('pattern'), NULL);

    // Test creating a custom locale pattern.
    $locale_pattern = entity_create('currency_locale_pattern', array());
    $this->assertTrue($locale_pattern instanceof CurrencyLocalePattern);
    $this->assertTrue($locale_pattern->uuid);

    // Test saving a custom locale pattern.
    $pattern = '¤ #.##0,00';
    $locale_pattern->set('locale', $locale);
    $locale_pattern->set('pattern', $pattern);
    $locale_pattern->save();
    $config = config('currency.currency_locale_pattern.' . $locale);
    $this->assertEqual($config->get('pattern'), $pattern);

    // Test loading a custom currency locale pattern.
    $locale_pattern_loaded = entity_load('currency_locale_pattern', $locale);
    $this->assertEqual($locale_pattern->get('pattern'), $locale_pattern_loaded->get('pattern'));

    // Test deleting a custom currency.
    $locale_pattern->delete();
    $config = config('currency.currency_locale_pattern.' . $locale);
    $this->assertIdentical($config->get('pattern'), NULL);
  }
}