<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\EditCurrencyLocaleTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\currency\Controller\EditCurrencyLocale;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\EditCurrencyLocale
 *
 * @group Currency
 */
class EditCurrencyLocaleTest extends UnitTestCase {

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Controller\EditCurrencyLocale
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->stringTranslation = $this->getStringTranslationStub();

    $this->sut = new EditCurrencyLocale($this->stringTranslation);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = array(
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $sut = EditCurrencyLocale::create($container);
    $this->assertInstanceOf(EditCurrencyLocale::class, $sut);
  }

  /**
   * @covers ::title
   */
  public function testTitle() {
    $label = $this->randomMachineName();

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);
    $currency_locale->expects($this->once())
      ->method('label')
      ->willReturn($label);

    $this->assertInstanceOf(TranslatableMarkup::class, $this->sut->title($currency_locale));
  }

}
