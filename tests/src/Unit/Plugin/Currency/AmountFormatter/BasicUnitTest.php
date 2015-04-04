<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Currency\AmountFormatter\BasicUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency\AmountFormatter;

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
   * The locale resolver.
   *
   * @var \Drupal\currency\LocaleResolverInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $localeResolver;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomMachineName();
    $plugin_definition = array();

    $this->localeResolver = $this->getMock('\Drupal\currency\LocaleResolverInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->formatter = new Basic($configuration, $plugin_id, $plugin_definition, $this->stringTranslation, $this->localeResolver);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('currency.locale_resolver', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->localeResolver),
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

    $this->localeResolver->expects($this->any())
      ->method('resolveCurrencyLocale')
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
