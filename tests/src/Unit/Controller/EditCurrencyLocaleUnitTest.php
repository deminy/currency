<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\EditCurrencyLocaleUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\currency\Controller\EditCurrencyLocale;
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
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = EditCurrencyLocale::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\EditCurrencyLocale', $form);
  }

  /**
   * @covers ::title
   */
  public function testTitle() {
    $label = $this->randomMachineName();

    $currency_locale = $this->getMockBuilder('\Drupal\currency\Entity\CurrencyLocale')
      ->disableOriginalConstructor()
      ->getMock();
    $currency_locale->expects($this->once())
      ->method('label')
      ->will($this->returnValue($label));

    $this->assertInternalType('string', $this->controller->title($currency_locale));
  }

}
