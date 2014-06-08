<?php

/**
 * @file
 * Contains \Drupal\currency\Test\Hook\PermissionUnitTest.
 */

namespace Drupal\currency\Tests\Hook;

use Drupal\currency\Hook\Permission;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Hook\Permission
 */
class PermissionUnitTest extends UnitTestCase {

  /**
   * The service under test.
   *
   * @var \Drupal\currency\Hook\Permission.
   */
  protected $service;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

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
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');

    $this->service = new Permission($this->stringTranslation);
  }

  /**
   * @covers ::invoke
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
