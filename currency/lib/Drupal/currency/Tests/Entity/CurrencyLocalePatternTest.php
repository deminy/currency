<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Entity\CurrencyLocalePatternTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\currency\Entity\CurrencyLocalePatternInterface;
use Drupal\simpletest\DrupalUnitTestBase;

/**
 * Tests class Drupal\currency\Entity\CurrencyLocalePattern.
 */
class CurrencyLocalePatternTest extends DrupalUnitTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Entity\CurrencyLocalePattern',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();
    $this->currency_locale_pattern = entity_create('currency_locale_pattern', array())
      ->setLocale('nl', 'NL')
      ->setPattern('¤-#,##0.00¤¤')
      ->setDecimalSeparator('@')
      ->setGroupingSeparator('%');
  }

  /**
   * Test format().
   */
  function testFormat() {
    // ICU, the C library that PHP's Intl extension uses for formatting, is
    // known to have trouble formatting combinations of currencies and locales
    // that it does not know. In order to make sure this works, test such a
    // combination, such as the Ukrainian Hryvnia (UAH) with Dutch, Netherlands
    // (nl_NL).
    $currency = entity_create('currency', array())
      ->setSign('₴')
      ->setCurrencyCode('UAH')
      ->setCurrencyNumber('980');
    $results = array(
      // An amount with no decimals should be formatted without decimals and
      // decimal separator.
      '123' => '₴-123UAH',
      // An amount with three groupings should have two grouping separators. All
      // of its three decimals should be formatted, even if the currency only
      // has two.
      '1234567.890' => '₴-1%234%567@890UAH',
      // An amount with only one decimal should be formatted with only one.
      '.3' => '₴-0@3UAH',
    );
    foreach ($results as $amount=> $expected) {
      $formatted = $this->currency_locale_pattern->formatAmount($currency, $amount);
      $this->assertEqual($formatted, $expected);
    }
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
