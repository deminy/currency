<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Entity\CurrencyTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\Usage;
use Drupal\simpletest\DrupalUnitTestBase;

/**
 * Tests class Drupal\currency\Entity\Currency.
 */
class CurrencyTest extends DrupalUnitTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Entity\Currency',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  function setUp() {
    parent::setUp();
    $this->currency = entity_create('currency', array());
  }

  /**
   * Test format().
   */
  function testFormat() {
    // Do not install configuration in during setUp(), as not to decrease the
    // performance of the other test methods.
    $this->installConfig(array('currency'));
    $this->currency->setSign('€');
    $amount = 12345.6789;
    $formatted = $this->currency->format($amount);
    $formatted_expected = '€12,345.6789';
    $this->assertEqual($formatted, $formatted_expected);
  }

  /**
   * Test roundAmount().
   */
  function testRoundAmount() {
    $this->currency->setSubunits(1000);
    $this->assertTrue($this->currency->roundAmount('12.34'), '12.340');
    $this->assertTrue($this->currency->roundAmount('1234.5678'), '1234.568');
  }

  /**
   * Tests getDecimals().
   */
  function testGetDecimals() {
    foreach (array(1, 2, 3) as $decimals) {
      $this->currency->setSubunits(pow(10, $decimals));
      $this->assertEqual($this->currency->getDecimals(), $decimals);
    }
  }

  /**
   * Tests isObsolete().
   */
  function testIsObsolete() {
    // A currency without usage data.
    $this->assertFalse($this->currency->isObsolete());

    // A currency that is no longer being used.
    $this->currency->setUsage(array(new Usage(array(
      'usageFrom' => '1813-01-01',
      'usageTo' => '2002-02-28',
    ))));
    $this->assertTrue($this->currency->isObsolete());

    // A currency that will become obsolete next year.
    $this->currency->setUsage(array(new Usage(array(
      'usageFrom' => '1813-01-01',
      'usageTo' => date('o') + 1 . '-02-28',
    ))));
    $this->assertFalse($this->currency->isObsolete());
  }

  /**
   * Test getAlternativeSigns() and setAlternativeSigns().
   */
  function testGetAlternativeSigns() {
    $alternative_signs = array('A', 'B');
    $this->assertTrue($this->currency->setAlternativeSigns($alternative_signs) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getAlternativeSigns(), $alternative_signs);
  }

  /**
   * Test id() and setCurrencyCode().
   */
  function testId() {
    $currency_code = $this->randomName(3);
    $this->assertTrue($this->currency->setCurrencyCode($currency_code) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->id(), $currency_code);
  }

  /**
   * Test getCurrencyNumber() and setCurrencyNumber.
   */
  function testGetCurrencyNumber() {
    $currency_number = '000';
    $this->assertTrue($this->currency->setCurrencyNumber($currency_number) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getCurrencyNumber(), $currency_number);
  }

  /**
   * Test getExchangeRates() and setExchangeRates().
   */
  function testGetExchangeRates() {
    $rates = array(
      'ABC' => '0.003',
    );
    $this->assertTrue($this->currency->setExchangeRates($rates) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getExchangeRates(), $rates);
  }

  /**
   * Test label() and setLabel().
   */
  function testLabel() {
    $label = $this->randomString();
    $this->assertTrue($this->currency->setLabel($label) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->label(), $label);
  }

  /**
   * Test getRoundingStep() and setRoundingStep().
   */
  function testGetRoundingStep() {
    $this->currency->setSubunits(100000);
    $this->assertIdentical($this->currency->getRoundingStep(), '0.000010000');
    $step = 5;
    $this->assertTrue($this->currency->setRoundingStep($step) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getRoundingStep(), $step);
  }

  /**
   * Test setSign() and getSign().
   */
  function testGetSign() {
    $currency_code = 'ABC';
    $this->assertTrue($this->currency->setCurrencyCode($currency_code) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->id(), $currency_code);
  }

  /**
   * Test setSubunits() and getSubunits().
   */
  function testGetSubunits() {
    $subunits = 73;
    $this->assertTrue($this->currency->setSubunits($subunits) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getSubunits(), $subunits);
  }

  /**
   * Test setUsage() and getUsage().
   */
  function testGetUsage() {
    $usage = new Usage(array(
      'usageFrom' => '1813-01-01',
      'usageTo' => date('o') + 1 . '-02-28',
    ));
    $this->assertTrue($this->currency->setUsage(array($usage)) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getUsage(), array($usage));
  }

  /**
   * Test options().
   */
  function testOptions() {
    $this->installConfig(array('currency'));
    $options = $this->currency->options();
    $this->assertTrue(is_array($options));
    $this->assertTrue(count($options));
    $this->assertTrue(is_string(reset($options)));
  }
}
