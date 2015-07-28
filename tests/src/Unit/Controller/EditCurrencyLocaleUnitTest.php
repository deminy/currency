<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\EditCurrencyLocaleUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\currency\Controller\EditCurrencyLocale;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\EditCurrencyLocale
 *
 * @group Currency
 */
class EditCurrencyLocaleUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\EditCurrencyLocale
   */
  protected $controller;

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
    $this->stringTranslation = $this->getStringTranslationStub();

    $this->controller = new EditCurrencyLocale($this->stringTranslation);
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
      ->will($this->returnValueMap($map));

    $form = EditCurrencyLocale::create($container);
    $this->assertInstanceOf(EditCurrencyLocale::class, $form);
  }

  /**
   * @covers ::title
   */
  public function testTitle() {
    $label = $this->randomMachineName();

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);
    $currency_locale->expects($this->once())
      ->method('label')
      ->will($this->returnValue($label));

    $this->assertInternalType('string', $this->controller->title($currency_locale));
  }

}
