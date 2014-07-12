<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\CurrencyUnitTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\currency\Controller\Currency;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\Currency
 *
 * @group Currency
 */
class CurrencyUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\Currency
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
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->entityFormBuilder = $this->getMock('\Drupal\Core\Entity\EntityFormBuilderInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->urlGenerator = $this->getMock('\Drupal\Core\Routing\UrlGeneratorInterface');

    $this->controller = new Currency($this->entityManager, $this->entityFormBuilder, $this->stringTranslation, $this->urlGenerator);
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
      array('url_generator', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->urlGenerator),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = Currency::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\Currency', $form);
  }

  /**
   * @covers ::enable
   */
  public function testEnable() {
    $url = $this->randomName();

    $currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();
    $currency->expects($this->once())
      ->method('enable');
    $currency->expects($this->once())
      ->method('save');

    $this->urlGenerator->expects($this->once())
      ->method('generateFromRoute')
      ->with('currency.currency.list')
      ->will($this->returnValue($url));

    $response = $this->controller->enable($currency);
    $this->assertInstanceOf('\Symfony\Component\HttpFoundation\RedirectResponse', $response);
    $this->assertSame($url, $response->getTargetUrl());
  }

  /**
   * @covers ::disable
   */
  public function testDisable() {
    $url = $this->randomName();

    $currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();
    $currency->expects($this->once())
      ->method('disable');
    $currency->expects($this->once())
      ->method('save');

    $this->urlGenerator->expects($this->once())
      ->method('generateFromRoute')
      ->with('currency.currency.list')
      ->will($this->returnValue($url));

    $response = $this->controller->disable($currency);
    $this->assertInstanceOf('\Symfony\Component\HttpFoundation\RedirectResponse', $response);
    $this->assertSame($url, $response->getTargetUrl());
  }

  /**
   * @covers ::editTitle
   */
  public function testEditTitle() {
    $label = $this->randomName();
    $string = 'Edit @label';

    $currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();
    $currency->expects($this->once())
      ->method('label')
      ->will($this->returnValue($label));

    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->with($string, array(
        '@label' => $label,
      ))
      ->will($this->returnArgument(0));

    $this->assertSame($string, $this->controller->editTitle($currency));
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();

    $storage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');
    $storage->expects($this->once())
      ->method('create')
      ->with(array())
      ->will($this->returnValue($currency));

    $this->entityManager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->will($this->returnValue($storage));

    $form = $this->getMock('\Drupal\Core\Entity\EntityFormInterface');

    $this->entityFormBuilder->expects($this->once())
      ->method('getForm')
      ->with($currency)
      ->will($this->returnValue($form));

    $this->assertSame($form, $this->controller->add());
  }

}
