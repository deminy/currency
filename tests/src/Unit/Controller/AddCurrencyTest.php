<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\AddCurrencyTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\currency\Controller\AddCurrency;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\AddCurrency
 *
 * @group Currency
 */
class AddCurrencyTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\AddCurrency
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
   */
  public function setUp() {
    $this->entityFormBuilder = $this->getMock(EntityFormBuilderInterface::class);

    $this->entityManager = $this->getMock(EntityManagerInterface::class);

    $this->controller = new AddCurrency($this->entityManager, $this->entityFormBuilder);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = array(
      array('entity.form_builder', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityFormBuilder),
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->entityManager),
    );
    $container->expects($this->any())
      ->method('get')
      ->willReturnMap($map);

    $form = AddCurrency::create($container);
    $this->assertInstanceOf(AddCurrency::class, $form);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $currency = $this->getMock(CurrencyInterface::class);

    $storage = $this->getMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('create')
      ->with(array())
      ->willReturn($currency);

    $this->entityManager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($storage);

    $form = $this->getMock(EntityFormInterface::class);

    $this->entityFormBuilder->expects($this->once())
      ->method('getForm')
      ->with($currency)
      ->willReturn($form);

    $this->assertSame($form, $this->controller->execute());
  }

}
