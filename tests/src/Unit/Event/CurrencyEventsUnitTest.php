<?php

/**
 * @file
 * Contains \Drupal\currency\Event\CurrencyEventsUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Event;

use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Event\CurrencyEvents
 *
 * @group Currency
 */
class CurrencyEventsUnitTest extends UnitTestCase {

  /**
   * Tests constants with event names.
   */
  public function testEventNames() {
    $class = new \ReflectionClass('\Drupal\currency\Event\CurrencyEvents');
    foreach ($class->getConstants() as $event_name) {
      // Make sure that every event name is properly namespaced.
      $this->assertSame(0, strpos($event_name, 'drupal.currency.'));
    }
  }

}
