<?php

/**
 * @file
 * Contains \Drupal\currency\Test\Hook\MenuLinkDefaultsUnitTest.
 */

namespace Drupal\currency\Tests\Hook;

use Drupal\currency\Hook\MenuLinkDefaults;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Hook\MenuLinkDefaults.
 */
class MenuLinkDefaultsUnitTest extends UnitTestCase {

  /**
   * The service under test.
   *
   * @var \Drupal\currency\Hook\MenuLinkDefaults.
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Hook\MenuLinkDefaults unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $this->service = new MenuLinkDefaults();
  }

  /**
   * @covers ::invoke
   */
  public function testInvoke() {
    $items = $this->service->invoke();
    $this->assertInternalType('array', $items);
    foreach ($items as $item) {
      $this->assertInternalType('array', $item);
      $this->assertArrayHasKey('route_name', $item);
      $this->assertArrayHasKey('link_title', $item);
    }
  }
}
