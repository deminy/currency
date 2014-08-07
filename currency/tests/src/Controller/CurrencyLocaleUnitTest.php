<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyLocaleUnitTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\currency\Controller\CurrencyLocale;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\CurrencyLocale
 *
 * @group Currency
 */
class CurrencyLocaleUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\CurrencyLocale
   */
  protected $controller;

  /**
   * The entity form builder.
   *
   * @var \Drupal\Core\Entity\EntityFormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityFormBuilder;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityManager;

  /**
   * The string translation service.
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
    $this->entityFormBuilder = $this->getMock('\Drupal\Core\Entity\EntityFormBuilderInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->controller = new CurrencyLocale($this->entityManager, $this->entityFormBuilder, $this->stringTranslation);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('entity.form_builder', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityFormBuilder),
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityManager),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = CurrencyLocale::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\CurrencyLocale', $form);
  }

  /**
   * @covers ::editTitle
   */
  public function testEditTitle() {
    $label = $this->randomMachineName();
    $string = 'Edit @label';

    $currency_locale = $this->getMockBuilder('\Drupal\currency\Entity\CurrencyLocale')
      ->disableOriginalConstructor()
      ->getMock();
    $currency_locale->expects($this->once())
      ->method('label')
      ->will($this->returnValue($label));

    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->with($string, array(
        '@label' => $label,
      ))
      ->will($this->returnArgument(0));

    $this->assertSame($string, $this->controller->editTitle($currency_locale));
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $currency_locale = $this->getMockBuilder('\Drupal\currency\Entity\CurrencyLocale')
      ->disableOriginalConstructor()
      ->getMock();

    $storage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');
    $storage->expects($this->once())
      ->method('create')
      ->with(array())
      ->will($this->returnValue($currency_locale));

    $this->entityManager->expects($this->once())
      ->method('getStorage')
      ->with('currency_locale')
      ->will($this->returnValue($storage));

    $form = $this->getMock('\Drupal\Core\Entity\EntityFormInterface');

    $this->entityFormBuilder->expects($this->once())
      ->method('getForm')
      ->with($currency_locale)
      ->will($this->returnValue($form));

    $this->assertSame($form, $this->controller->add());
  }

}
