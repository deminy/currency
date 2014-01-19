<?php

/**
 * @file
 * Contains \Drupal\currency\Test\Hook\MenuUnitTest.
 */

namespace Drupal\currency\Tests\Hook;

use Drupal\currency\Hook\Menu;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\Hook\Menu.
 */
class MenuUnitTest extends UnitTestCase {

  /**
   * The service under test.
   *
   * @var \Drupal\currency\Hook\Menu.
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Hook\Menu unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $this->service = new Menu();
  }

  /**
   * @covers \Drupal\currency\Hook\Menu::invoke()
   */
  public function testInvoke() {
    $items = $this->service->invoke();
    $this->assertInternalType('array', $items);
    foreach ($items as $item) {
      $this->assertInternalType('array', $item);
      $this->assertArrayHasKey('route_name', $item);
      $this->assertArrayHasKey('title', $item);
    }
  }
}
