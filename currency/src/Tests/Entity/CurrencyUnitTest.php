<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyUnitTest.
 */

namespace Drupal\currency\Tests\Entity;

use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\Usage;
use Drupal\simpletest\DrupalUnitTestBase;

/**
 * Tests \Drupal\currency\Entity\Currency.
 */
class CurrencyUnitTest extends DrupalUnitTestBase {

  /**
   * The currency under test.
   *
   * @var \Drupal\currency\Entity\CurrencyInterface
   */
  protected $currency;

  /**
   * {@inheritdoc}
   */
  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Entity\Currency unit test',
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
   * Test formatAmount().
   */
  function testFormatAmount() {
    // Do not install configuration in during setUp(), as not to decrease the
    // performance of the other test methods.
    $this->installConfig(array('currency'));
    $this->currency->setCurrencyCode('BLA');
    $this->currency->setSubunits(100);
    $amount = 12345.6789;
    $formatted = $this->currency->formatAmount($amount, TRUE);
    $formatted_expected = 'BLA 12,345.68';
    $this->assertEqual($formatted, $formatted_expected);
    $formatted = $this->currency->formatAmount($amount, FALSE);
    $formatted_expected = 'BLA 12,345.6789';
    $this->assertEqual($formatted, $formatted_expected);
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
    $usage = new Usage();
    $usage->setStart('1813-01-01')
      ->setEnd('2002-02-28');
    $this->currency->setUsages(array($usage));
    $this->assertTrue($this->currency->isObsolete());

    // A currency that will become obsolete next year.
    $usage = new Usage();
    $usage->setStart('1813-01-01')
      ->setEnd(date('o') + 1 . '-02-28');
    $this->currency->setUsages(array($usage));
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
   * Test getCurrencyCode() and setCurrencyCode.
   */
  function testGetCurrencyCode() {
    $currency_code = $this->randomName(3);
    $this->assertIdentical(spl_object_hash($this->currency->setCurrencyNumber($currency_code)), spl_object_hash($this->currency));
    $this->assertIdentical($this->currency->getCurrencyNumber(), $currency_code);
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
   * Test getHistoricalExchangeRates() and setHistoricalExchangeRates().
   */
  function testGetExchangeRates() {
    $rates = array(
      'ABC' => '0.003',
    );
    $this->assertTrue($this->currency->setHistoricalExchangeRates($rates) instanceof CurrencyInterface);
    $this->assertIdentical($this->currency->getHistoricalExchangeRates(), $rates);
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
   * Test setUsages() and getUsages().
   */
  function testGetUsage() {
    $usage = new Usage();
    $usage->setStart('1813-01-01')
    ->setEnd(date('o') + 1 . '-02-28');
    $this->assertEqual(spl_object_hash($this->currency->setUsages(array($usage))), spl_object_hash($this->currency));
    $this->assertIdentical($this->currency->getUsages(), array($usage));
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
