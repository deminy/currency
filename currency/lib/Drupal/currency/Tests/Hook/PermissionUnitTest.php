<?php

/**
 * @file
 * Contains \Drupal\currency\Test\Hook\PermissionUnitTest.
 */

namespace Drupal\currency\Tests\Hook;

use Drupal\currency\Hook\Permission;
use Drupal\Tests\UnitTestCase;

/**
 * Tests \Drupal\currency\Hook\Permission.
 */
class PermissionUnitTest extends UnitTestCase {

  /**
   * The service under test.
   *
   * @var \Drupal\currency\Hook\Permission.
   */
  protected $service;

  /**
   * The translation manager service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $translationManager;

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Hook\Permission unit test',
      'group' => 'Currency',
    );
  }

  /**
   * {@inheritdoc
   */
  public function setUp() {
    $this->translationManager = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->service = new Permission($this->translationManager);
  }

  /**
   * @covers \Drupal\currency\Hook\Permission::invoke()
   */
  public function testInvoke() {
    $permissions = $this->service->invoke();
    $this->assertInternalType('array', $permissions);
    foreach ($permissions as $permission) {
      $this->assertInternalType('array', $permission);
      $this->assertArrayHasKey('title', $permission);
    }
  }
}
