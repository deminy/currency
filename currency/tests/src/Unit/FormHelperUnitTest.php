<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\FormHelperUnitTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\currency\FormHelper;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\FormHelper
 *
 * @group Currency
 */
class FormHelperUnitTest extends UnitTestCase {

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The currency locale storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyLocaleStorage;

  /**
   * The form helper under test.
   *
   * @var \Drupal\currency\FormHelper
   */
  protected $formHelper;

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
    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->currencyLocaleStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $map = [
      ['currency', $this->currencyStorage],
      ['currency_locale', $this->currencyLocaleStorage],
    ];
    $entity_manager->expects($this->atLeastOnce())
      ->method('getStorage')
      ->willReturnMap($map);

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->formHelper = new FormHelper($this->stringTranslation, $entity_manager);
  }

  /**
   * @covers ::getCurrencyOptions
   */
  public function testCurrencyOptionsWithoutLimitation() {
    $this->currencyStorage->expects($this->once())
      ->method('loadMultiple')
      ->willReturn([]);

    $this->assertSame([], $this->formHelper->getCurrencyOptions(NULL));
  }

  /**
   * @covers ::getCurrencyOptions
   */
  public function testCurrencyOptionsWithLimitation() {
    $this->currencyStorage->expects($this->never())
      ->method('loadMultiple');

    $currency_locale_id_a = $this->randomMachineName();
    $currency_locale_label_a = $this->randomMachineName();
    $currency_locale_a = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_locale_a->expects($this->atLeastOnce())
      ->method('id')
      ->willReturn($currency_locale_id_a);
    $currency_locale_a->expects($this->atLeastOnce())
      ->method('label')
      ->willReturn($currency_locale_label_a);
    $currency_locale_a->expects($this->atLeastOnce())
      ->method('status')
      ->willReturn(TRUE);
    $currency_locale_b = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_locale_b->expects($this->atLeastOnce())
      ->method('status')
      ->willReturn(FALSE);
    $currency_locale_id_c = $this->randomMachineName();
    $currency_locale_label_c = $this->randomMachineName();
    $currency_locale_c = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_locale_c->expects($this->atLeastOnce())
      ->method('id')
      ->willReturn($currency_locale_id_c);
    $currency_locale_c->expects($this->atLeastOnce())
      ->method('label')
      ->willReturn($currency_locale_label_c);
    $currency_locale_c->expects($this->atLeastOnce())
      ->method('status')
      ->willReturn(TRUE);

    $expected_options = [
      $currency_locale_id_a => $currency_locale_label_a . ' (' . $currency_locale_id_a . ')',
      $currency_locale_id_c => $currency_locale_label_c . ' (' . $currency_locale_id_c . ')',
    ];
    natcasesort($expected_options);

    $this->assertSame($expected_options, $this->formHelper->getCurrencyOptions([$currency_locale_a, $currency_locale_b, $currency_locale_c]));
  }

  /**
   * @covers ::getCurrencyLocaleOptions
   */
  public function testCurrencyLocaleOptionsWithoutLimitation() {
    $this->currencyLocaleStorage->expects($this->once())
      ->method('loadMultiple')
      ->willReturn([]);

    $this->assertSame([], $this->formHelper->getCurrencyLocaleOptions(NULL));
  }

  /**
   * @covers ::getCurrencyLocaleOptions
   */
  public function testCurrencyLocaleOptionsWithLimitation() {
    $this->currencyStorage->expects($this->never())
      ->method('loadMultiple');

    $currency_locale_id_a = $this->randomMachineName();
    $currency_locale_label_a = $this->randomMachineName();
    $currency_locale_a = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');
    $currency_locale_a->expects($this->atLeastOnce())
      ->method('id')
      ->willReturn($currency_locale_id_a);
    $currency_locale_a->expects($this->atLeastOnce())
      ->method('label')
      ->willReturn($currency_locale_label_a);
    $currency_locale_a->expects($this->atLeastOnce())
      ->method('status')
      ->willReturn(TRUE);
    $currency_locale_b = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');
    $currency_locale_b->expects($this->atLeastOnce())
      ->method('status')
      ->willReturn(FALSE);
    $currency_locale_id_c = $this->randomMachineName();
    $currency_locale_label_c = $this->randomMachineName();
    $currency_locale_c = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');
    $currency_locale_c->expects($this->atLeastOnce())
      ->method('id')
      ->willReturn($currency_locale_id_c);
    $currency_locale_c->expects($this->atLeastOnce())
      ->method('label')
      ->willReturn($currency_locale_label_c);
    $currency_locale_c->expects($this->atLeastOnce())
      ->method('status')
      ->willReturn(TRUE);

    $expected_options = [
      $currency_locale_id_a => $currency_locale_label_a,
      $currency_locale_id_c => $currency_locale_label_c,
    ];
    natcasesort($expected_options);

    $this->assertSame($expected_options, $this->formHelper->getCurrencyLocaleOptions([$currency_locale_a, $currency_locale_b, $currency_locale_c]));
  }

}
