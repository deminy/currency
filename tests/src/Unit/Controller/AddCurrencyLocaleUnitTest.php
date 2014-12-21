<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\AddCurrencyLocaleUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\currency\Controller\AddCurrencyLocale;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\AddCurrencyLocale
 *
 * @group Currency
 */
class AddCurrencyLocaleUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\AddCurrencyLocale
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
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->entityFormBuilder = $this->getMock('\Drupal\Core\Entity\EntityFormBuilderInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');

    $this->controller = new AddCurrencyLocale($this->entityManager, $this->entityFormBuilder);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('entity.form_builder', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityFormBuilder),
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityManager),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = AddCurrencyLocale::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\AddCurrencyLocale', $form);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
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

    $this->assertSame($form, $this->controller->execute());
  }

}
