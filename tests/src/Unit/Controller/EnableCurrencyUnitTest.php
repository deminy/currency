<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\EnableCurrencyUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\currency\Controller\EnableCurrency;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\EnableCurrency
 *
 * @group Currency
 */
class EnableCurrencyUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\EnableCurrency
   */
  protected $controller;

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $urlGenerator;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->urlGenerator = $this->getMock('\Drupal\Core\Routing\UrlGeneratorInterface');

    $this->controller = new EnableCurrency($this->urlGenerator);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('url_generator', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->urlGenerator),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = EnableCurrency::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\EnableCurrency', $form);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $url = $this->randomMachineName();

    $currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();
    $currency->expects($this->once())
      ->method('enable');
    $currency->expects($this->once())
      ->method('save');

    $this->urlGenerator->expects($this->once())
      ->method('generateFromRoute')
      ->with('entity.currency.collection')
      ->will($this->returnValue($url));

    $response = $this->controller->execute($currency);
    $this->assertInstanceOf('\Symfony\Component\HttpFoundation\RedirectResponse', $response);
    $this->assertSame($url, $response->getTargetUrl());
  }

}
