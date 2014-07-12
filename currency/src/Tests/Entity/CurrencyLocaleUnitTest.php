<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyLocaleUnitTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\simpletest\KernelTestBase;

/**
 * \Drupal\currency\Entity\CurrencyLocale unit test.
 *
 * @group Currency
 */
class CurrencyLocaleUnitTest extends KernelTestBase {

  /**
   * The currency locale under test.
   *
   * @var \Drupal\currency\Entity\CurrencyLocaleInterface
   */
  protected $currencyLocale;

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();
    $this->currencyLocale = entity_create('currency_locale', array());
    $this->currencyLocale->setLocale('nl', 'NL')
      ->setPattern('¤-#,##0.00¤¤')
      ->setDecimalSeparator('@')
      ->setGroupingSeparator('%');
  }

  /**
   * Test setDecimalSeparator() and getDecimalSeparator().
   */
  function testGetDecimalSeparator() {
    $separator = $this->randomName();
    $this->assertTrue($this->currencyLocale->setDecimalSeparator($separator) instanceof CurrencyLocaleInterface);
    $this->assertIdentical($this->currencyLocale->getDecimalSeparator(), $separator);
  }

  /**
   * Test setGroupingSeparator() and getGroupingSeparator().
   */
  function testGetGroupingSeparator() {
    $separator = $this->randomName();
    $this->assertTrue($this->currencyLocale->setGroupingSeparator($separator) instanceof CurrencyLocaleInterface);
    $this->assertIdentical($this->currencyLocale->getGroupingSeparator(), $separator);
  }

  /**
   * Test setLocale(), id(), getLanguageCode(), and getCountryCode().
   */
  function testId() {
    $this->assertTrue($this->currencyLocale->setLocale('nL', 'Nl') instanceof CurrencyLocaleInterface);
    $this->assertIdentical($this->currencyLocale->id(), 'nl_NL');
    $this->assertIdentical($this->currencyLocale->getLanguageCode(), 'nl');
    $this->assertIdentical($this->currencyLocale->getCountryCode(), 'NL');
  }

  /**
   * Test setPattern() and getPattern().
   */
  function testGetPattern() {
    $pattern = $this->randomName();
    $this->assertTrue($this->currencyLocale->setPattern($pattern) instanceof CurrencyLocaleInterface);
    $this->assertIdentical($this->currencyLocale->getPattern(), $pattern);
  }
}
