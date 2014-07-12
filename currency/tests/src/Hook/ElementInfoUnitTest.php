<?php

/**
 * @file
 * Contains \Drupal\currency\Test\Hook\ElementInfoUnitTest.
 */

namespace Drupal\currency\Tests\Hook;

use Drupal\Core\Render\Element;
use Drupal\currency\Hook\ElementInfo;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Hook\ElementInfo
 *
 * @group Currency
 */
class ElementInfoUnitTest extends UnitTestCase {

  /**
   * The service under test.
   *
   * @var \Drupal\currency\Hook\ElementInfo
   */
  protected $service;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->service = new ElementInfo();
  }

  /**
   * @covers ::invoke
   */
  public function testInvoke() {
    $elements = $this->service->invoke();
    $this->assertInternalType('array', $elements);
    foreach ($elements as $element) {
      $this->assertInternalType('array', $element);
      $this->assertSame(0, count(Element::children($element)));
    }
  }
}
