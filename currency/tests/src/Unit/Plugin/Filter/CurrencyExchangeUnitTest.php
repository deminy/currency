<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Plugin\Filter\CurrencyExchangeUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Filter;

use Drupal\currency\ExchangeRate;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Filter\CurrencyExchange
 *
 * @group Currency
 */
class CurrencyExchangeUnitTest extends UnitTestCase {

  /**
   * The exchange rate provider used for testing.
   *
   * @var \Drupal\currency\ExchangeRateProviderInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $exchangeRateProvider;

  /**
   * The filter under test.
   *
   * @var \Drupal\currency\Plugin\Filter\CurrencyExchange|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $filter;

  /**
   * The input parser used for testing.
   *
   * @var \Drupal\currency\Input|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $input;

  /**
   * The math service used for testing.
   *
   * @var \Drupal\currency\Math\MathInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $math;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array(
      'cache' => TRUE,
      'provider' => $this->randomMachineName(),
    );

    $this->exchangeRateProvider = $this->getMock('\Drupal\currency\ExchangeRateProviderInterface');

    $this->input = $this->getMock('\Drupal\currency\Input');

    $this->math = $this->getMock('\Drupal\currency\Math\MathInterface');

    $this->filter = $this->getMockBuilder('\Drupal\currency\Plugin\Filter\CurrencyExchange')
      ->setConstructorArgs(array($configuration, $plugin_id, $plugin_definition, $this->exchangeRateProvider, $this->math, $this->input))
      ->setMethods(array('t'))
      ->getMock();
  }

  /**
   * @covers ::process
   */
  public function testProcess() {
    $currency_code_from = 'EUR';
    $currency_code_to = 'NLG';
    $rate = '2.20371';
    $exchange_rate = new ExchangeRate(NULL, NULL, $currency_code_from, $currency_code_to, $rate);

    $this->input->expects($this->any())
      ->method('parseAmount')
      ->will($this->returnArgument(0));

    $this->exchangeRateProvider->expects($this->any())
      ->method('load')
      ->with($currency_code_from, $currency_code_to)
      ->will($this->returnValue($exchange_rate));

    $map = array(
      array(1, $rate, $rate),
      array('1', $rate, $rate),
      array('2', $rate, '4.40742'),
    );
    $this->math->expects($this->any())
      ->method('multiply')
      ->will($this->returnValueMap($map));

    $langcode = $this->randomMachineName(2);
    $cache = TRUE;
    $cache_id = $this->randomMachineName();

    $tokens_valid = array(
      '[currency:EUR:NLG]' => '2.20371',
      '[currency:EUR:NLG:1]' => '2.20371',
      '[currency:EUR:NLG:2]' => '4.40742',
    );
    $tokens_invalid = array(
      // Missing arguments.
      '[currency]',
      '[currency:]',
      '[currency::]',
      '[currency:EUR]',
      // Invalid currency code.
      '[currency:EUR:123]',
      '[currency:123:EUR]',
      // Invalid currency code and missing argument.
      '[currency:123]',
    );

    foreach ($tokens_valid as $token => $replacement) {
      $this->assertSame($replacement, $this->filter->process($token, $langcode, $cache, $cache_id));
    }
    foreach ($tokens_invalid as $token) {
      $this->assertSame($token, $this->filter->process($token, $langcode, $cache, $cache_id));
    }
  }

  /**
   * @covers ::tips
   */
  public function testTips() {
    $this->filter->expects($this->any())
      ->method('t')
      ->will($this->returnArgument(0));

    $this->assertInternalType('string', $this->filter->tips());
  }
}