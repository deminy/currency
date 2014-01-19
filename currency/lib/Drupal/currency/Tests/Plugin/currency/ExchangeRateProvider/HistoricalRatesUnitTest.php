<?php

/**
 * @file Contains
 * \Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider\HistoricalRatesUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\ExchangeRateProvider;

use Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates.
 */
class HistoricalRatesUnitTest extends UnitTestCase {

  /**
   * The currency storage used for testing.
   *
   * @var \Drupal\Core\Entity\EntityStorageControllerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The math service.
   *
   * @var \Drupal\currency\MathInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $math;

  /**
   * The plugin under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates
   */
  protected $plugin;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\Currency\ExchangeRateProvider\HistoricalRates unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomName();
    $plugin_definition = array();

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageControllerInterface');

    $this->math = $this->getMock('\Drupal\currency\MathInterface');

    $this->plugin = new HistoricalRates($configuration, $plugin_id, $plugin_definition, $this->currencyStorage, $this->math);
  }

  /**
   * Tests load().
   */
  public function testLoad() {
    $rates = $this->prepareExchangeRates();

    $this->math->expects($this->any())
      ->method('divide')
      ->with(1, $rates['EUR']['NLG'])
      ->will($this->returnValue($rates['NLG']['EUR']));

    // Test a rate that is stored in config.
    $this->assertSame($rates['EUR']['NLG'], $this->plugin->load('EUR', 'NLG')->getRate());

    // Test a reverse exchange rate.
    $this->assertSame($rates['NLG']['EUR'], $this->plugin->load('NLG', 'EUR')->getRate());

    // Test an unavailable exchange rate.
    $this->assertNull($this->plugin->load('NLG', 'UAH'));
  }

  /**
   * Tests loadMultiple().
   */
  public function testLoadMultiple() {
    $rates = $this->prepareExchangeRates();

    $this->math->expects($this->any())
      ->method('divide')
      ->with(1, $rates['EUR']['NLG'])
      ->will($this->returnValue($rates['NLG']['EUR']));

    $rates = array(
      'EUR' => array(
        'NLG' => $rates['EUR']['NLG'],
      ),
      'NLG' => array(
        'EUR' => $rates['NLG']['EUR'],
      ),
      'ABC' => array(
        'XXX' => NULL,
      ),
    );

    $returned_rates = $this->plugin->loadMultiple(array(
      // Test a rate that is stored in config.
      'EUR' => array('NLG'),
      // Test a reverse exchange rate.
      'NLG' => array('EUR'),
      // Test an unavailable exchange rate.
      'ABC' => array('XXX'),
    ));
    $this->assertSame($rates['EUR']['NLG'], $returned_rates['EUR']['NLG']->getRate());
    $this->assertSame($rates['NLG']['EUR'], $returned_rates['NLG']['EUR']->getRate());
    $this->assertNull($returned_rates['ABC']['XXX']);
  }

  /**
   * Stores random exchange rates in the mocked config and returns them.
   *
   * @return array
   */
  protected function prepareExchangeRates() {
    $rates = array(
      'EUR' => array(
        'NLG' => '2.20371',
      ),
      'NLG' => array(
        'EUR' => '0.453780216',
      ),
    );

    $currency_eur = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_eur->expects($this->any())
      ->method('getHistoricalExchangeRates')
      ->will($this->returnValue($rates['EUR']));
    $currency_nlg = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_nlg->expects($this->any())
      ->method('getHistoricalExchangeRates')
      ->will($this->returnValue($rates['NLG']));

    $map = array(
      array('EUR', $currency_eur),
      array('NLG', $currency_nlg),
    );
    $this->currencyStorage->expects($this->any())
      ->method('load')
      ->will($this->returnValueMap($map));

    return $rates;
  }
}