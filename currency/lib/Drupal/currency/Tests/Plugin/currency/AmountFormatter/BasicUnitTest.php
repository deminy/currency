<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Plugin\Currency\AmountFormatter\BasicUnitTest.
 */

namespace Drupal\currency\Tests\Plugin\Currency\AmountFormatter;

use Drupal\currency\Plugin\Currency\AmountFormatter\Basic;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\Plugin\Currency\AmountFormatter\Basic.
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
   * The translation manager.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $translationManager;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Plugin\Currency\AmountFormatter\Basic unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $configuration = array();
    $plugin_id = $this->randomName();
    $plugin_definition = array();

    $this->localeDelegator = $this->getMock('\Drupal\currency\LocaleDelegatorInterface');

    $this->translationManager = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->formatter = new Basic($configuration, $plugin_id, $plugin_definition, $this->translationManager, $this->localeDelegator);
  }

  /**
   * Test formatAmount().
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
    $translation = $this->randomName();

    $this->translationManager->expects($this->once())
      ->method('translate')
      ->with($translatable_string, $translation_arguments)
      ->will($this->returnValue($translation));

    $this->assertSame($translation, $this->formatter->formatAmount($currency, $amount));
  }
}
