<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\EventDispatcherTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\currency\Event\CurrencyEvents;
use Drupal\currency\Event\ResolveCountryCode;
use Drupal\currency\EventDispatcher;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \Drupal\currency\EventDispatcher
 *
 * @group Currency
 */
class EventDispatcherTest extends UnitTestCase {

  /**
   * The Symfony event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $symfonyEventDispatcher;

  /**
   * The class under test.
   *
   * @var \Drupal\currency\EventDispatcher
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->symfonyEventDispatcher = $this->getMock(EventDispatcherInterface::class);

    $this->sut = new EventDispatcher($this->symfonyEventDispatcher);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    new EventDispatcher($this->symfonyEventDispatcher);
  }

  /**
   * @covers ::resolveCountryCode
   */
  public function testResolveCountryCode() {;
    $this->symfonyEventDispatcher->expects($this->once())
      ->method('dispatch')
      ->with(CurrencyEvents::RESOLVE_COUNTRY_CODE, $this->isInstanceOf(ResolveCountryCode::class));

    $this->sut->resolveCountryCode();
  }

}
