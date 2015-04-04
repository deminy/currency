<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\CurrencyServiceProviderUnitTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\currency\CurrencyServiceProvider;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\CurrencyServiceProvider
 */
class CurrencyServiceProviderUnitTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\CurrencyServiceProvider
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();

    $this->sut = new CurrencyServiceProvider();
  }

  /**
   * @covers ::register
   */
  public function testRegister() {
    $container = new ContainerBuilder();

    $this->sut->register($container);
  }

}
