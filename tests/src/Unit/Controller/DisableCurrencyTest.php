<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\DisableCurrencyTest.
 */

namespace Drupal\Tests\currency\Unit\Controller;

use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\currency\Controller\DisableCurrency;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @coversDefaultClass \Drupal\currency\Controller\DisableCurrency
 *
 * @group Currency
 */
class DisableCurrencyTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\DisableCurrency
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
    $this->urlGenerator = $this->getMock(UrlGeneratorInterface::class);

    $this->controller = new DisableCurrency($this->urlGenerator);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $container = $this->getMock(ContainerInterface::class);
    $map = array(
      array('url_generator', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->urlGenerator),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = DisableCurrency::create($container);
    $this->assertInstanceOf(DisableCurrency::class, $form);
  }

  /**
   * @covers ::execute
   */
  public function testExecute() {
    $url = $this->randomMachineName();

    $currency = $this->getMock(CurrencyInterface::class);
    $currency->expects($this->once())
      ->method('disable');
    $currency->expects($this->once())
      ->method('save');

    $this->urlGenerator->expects($this->once())
      ->method('generateFromRoute')
      ->with('entity.currency.collection')
      ->will($this->returnValue($url));

    $response = $this->controller->execute($currency);
    $this->assertInstanceOf(RedirectResponse::class, $response);
    $this->assertSame($url, $response->getTargetUrl());
  }

}
