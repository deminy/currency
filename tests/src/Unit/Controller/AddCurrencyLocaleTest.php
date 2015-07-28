<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\AddCurrencyLocaleTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\currency\Controller\AddCurrencyLocale;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\AddCurrencyLocale
 *
 * @group Currency
 */
class AddCurrencyLocaleTest extends UnitTestCase {

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
   * The class under test.
   *
   * @var \Drupal\currency\Controller\AddCurrencyLocale
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->entityFormBuilder = $this->getMock(EntityFormBuilderInterface::class);

    $this->entityManager = $this->getMock(EntityManagerInterface::class);

    $this->sut = new AddCurrencyLocale($this->entityManager, $this->entityFormBuilder);
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

    $sut = AddCurrencyLocale::create($container);
    $this->assertInstanceOf(AddCurrencyLocale::class, $sut);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);

    $storage = $this->getMock(EntityStorageInterface::class);
    $storage->expects($this->once())
      ->method('create')
      ->with(array())
      ->willReturn($currency_locale);

    $this->entityManager->expects($this->once())
      ->method('getStorage')
      ->with('currency_locale')
      ->willReturn($storage);

    $form = $this->getMock(EntityFormInterface::class);

    $this->entityFormBuilder->expects($this->once())
      ->method('getForm')
      ->with($currency_locale)
      ->willReturn($form);

    $this->assertSame($form, $this->sut->execute());
  }

}
