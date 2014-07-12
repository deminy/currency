<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Plugin\Filter\CurrencyLocalizeUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Filter;

use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Filter\CurrencyLocalize
 *
 * @group Currency
 */
class CurrencyLocalizeUnitTest extends UnitTestCase {

  /**
   * The currency storage used for testing.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

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
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomName();
    $plugin_definition = array(
      'cache' => TRUE,
      'provider' => $this->randomName(),
    );

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->input = $this->getMock('\Drupal\currency\Input');

    $this->filter = $this->getMockBuilder('\Drupal\currency\Plugin\Filter\CurrencyLocalize')
      ->setConstructorArgs(array($configuration, $plugin_id, $plugin_definition, $this->currencyStorage, $this->input))
      ->setMethods(array('t'))
      ->getMock();
  }

  /**
   * @covers ::process
   */
  function testProcess() {
    $map = array(
      array('100', TRUE, '€100.00'),
      array('100.7654', TRUE, '€100.77'),
      array('1.99', TRUE, '€1.99'),
      array('2.99', TRUE, '€2.99'),
    );
    $currency = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency->expects($this->any())
      ->method('formatAmount')
      ->will($this->returnValueMap($map));

    $this->currencyStorage->expects($this->any())
      ->method('load')
      ->with('EUR')
      ->will($this->returnValue($currency));

    $this->input->expects($this->any())
      ->method('parseAmount')
      ->will($this->returnArgument(0));

    $langcode = $this->randomName(2);
    $cache = TRUE;
    $cache_id = $this->randomName();

    $tokens_valid = array(
      '[currency-localize:EUR:100]' => '€100.00',
      '[currency-localize:EUR:100.7654]' => '€100.77',
      '[currency-localize:EUR:1.99]' => '€1.99',
      '[currency-localize:EUR:2.99]' => '€2.99',
    );
    $tokens_invalid = array(
      // Missing arguments.
      '[currency-localize]',
      '[currency-localize:]',
      '[currency-localize::]',
      '[currency-localize:EUR]',
      // Invalid currency code.
      '[currency-localize:123:456]',
      // Invalid currency code and missing argument.
      '[currency-localize:123]',
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