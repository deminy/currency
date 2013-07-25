<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Plugin\Core\Entity\CurrencyLocalePatternTest.
 */

namespace Drupal\currency\Tests\Plugin\Core\Entity;

use Drupal\currency\Plugin\Core\Entity\CurrencyLocalePatternInterface;
use Drupal\simpletest\DrupalUnitTestBase;

/**
 * Tests class Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern.
 */
class CurrencyLocalePatternTest extends DrupalUnitTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Plugin\Core\Entity\CurrencyLocalePattern',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();
    $this->currency_locale_pattern = entity_create('currency_locale_pattern', array(
      'pattern' => '¤-#,##0.00[XXX][999]',
      'decimalSeparator' => ',',
      'groupingSeparator' => '.',
    ));
  }

  /**
   * Test format().
   */
  function testFormat() {
    $currency = entity_create('currency', array())
      ->setSign('€')
      ->setCurrencyCode('EUR')
      ->setCurrencyNumber('978');
    $amount = 12345.6789;
    $formatted = $this->currency_locale_pattern->format($currency, $amount);
    $formatted_expected = '€-12.345,6789EUR978';
    $this->assertEqual($formatted, $formatted_expected);
  }

  /**
   * Test setDecimalSeparator() and getDecimalSeparator().
   */
  function testGetDecimalSeparator() {
    $separator = $this->randomName();
    $this->assertTrue($this->currency_locale_pattern->setDecimalSeparator($separator) instanceof CurrencyLocalePatternInterface);
    $this->assertIdentical($this->currency_locale_pattern->getDecimalSeparator(), $separator);
  }

  /**
   * Test setGroupingSeparator() and getGroupingSeparator().
   */
  function testGetGroupingSeparator() {
    $separator = $this->randomName();
    $this->assertTrue($this->currency_locale_pattern->setGroupingSeparator($separator) instanceof CurrencyLocalePatternInterface);
    $this->assertIdentical($this->currency_locale_pattern->getGroupingSeparator(), $separator);
  }

  /**
   * Test setLocale(), id(), getLanguageCode(), and getCountryCode().
   */
  function testId() {
    $this->assertTrue($this->currency_locale_pattern->setLocale('nL', 'Nl') instanceof CurrencyLocalePatternInterface);
    $this->assertIdentical($this->currency_locale_pattern->id(), 'nl_NL');
    $this->assertIdentical($this->currency_locale_pattern->getLanguageCode(), 'nl');
    $this->assertIdentical($this->currency_locale_pattern->getCountryCode(), 'NL');
  }

  /**
   * Test setPattern() and getPattern().
   */
  function testGetPattern() {
    $pattern = $this->randomName();
    $this->assertTrue($this->currency_locale_pattern->setPattern($pattern) instanceof CurrencyLocalePatternInterface);
    $this->assertIdentical($this->currency_locale_pattern->getPattern(), $pattern);
  }
}
