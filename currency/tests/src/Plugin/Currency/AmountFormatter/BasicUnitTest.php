<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Plugin\Currency\AmountFormatter\BasicUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\AmountFormatter;

use Drupal\currency\Plugin\Currency\AmountFormatter\Basic;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\AmountFormatter\Basic
 *
 * @group Currency
 */
class BasicUnitTest extends UnitTestCase {

  /**
   * The formatter under test.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\Basic
   */
  protected $formatter;

  /**
   * The locale delegator used for testing.
   *
   * @var \Drupal\currency\LocaleDelegatorInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $localeDelegator;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $this->localeDelegator = $this->getMock('\Drupal\currency\LocaleDelegatorInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->formatter = new Basic($configuration, $plugin_id, $plugin_definition, $this->stringTranslation, $this->localeDelegator);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('currency.locale_delegator', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->localeDelegator),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $formatter = Basic::create($container, $configuration, $plugin_id, $plugin_definition);
    $this->assertInstanceOf('\Drupal\currency\Plugin\Currency\AmountFormatter\Basic', $formatter);
  }

  /**
   * @covers ::formatAmount
   */
  function testFormatAmount() {
    $decimal_separator = '@';
    $grouping_separator ='%';
    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');
    $currency_locale->expects($this->any())
      ->method('getDecimalSeparator')
      ->will($this->returnValue($decimal_separator));
    $currency_locale->expects($this->any())
      ->method('getGroupingSeparator')
      ->will($this->returnValue($grouping_separator));

    $this->localeDelegator->expects($this->any())
      ->method('getCurrencyLocale')
      ->will($this->returnValue($currency_locale));

    // The formatter must not alter the decimals.
    $amount = '987654.321';
    $formatted_amount = '987%654@321';

    $currency_sign = '₴';
    $currency_code = 'UAH';
    $currency_decimals = 2;
    $currency = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency->expects($this->any())
      ->method('getCurrencyCode')
      ->will($this->returnValue($currency_code));
    $currency->expects($this->any())
      ->method('getDecimals')
      ->will($this->returnValue($currency_decimals));
    $currency->expects($this->any())
      ->method('getSign')
      ->will($this->returnValue($currency_sign));

    $translatable_string = '!currency_code !amount';
    $translation_arguments = array(
      '!currency_code' => $currency_code,
      '!currency_sign' => $currency_sign,
      '!amount' => $formatted_amount,
    );
    $translation = $this->randomMachineName();

    $this->stringTranslation->expects($this->once())
      ->method('translate')
      ->with($translatable_string, $translation_arguments)
      ->will($this->returnValue($translation));

    $this->assertSame($translation, $this->formatter->formatAmount($currency, $amount));
  }
}
