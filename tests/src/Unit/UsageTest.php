<?php

/**
 * @file Contians \Drupal\Tests\currency\Unit\UsageTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\currency\Usage;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Usage
 *
 * @group Currency
 */
class UsageTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Usage
   */
  protected $usage;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->usage = new Usage();
  }

  /**
   * @covers ::setStart
   * @covers ::getStart
   */
  public function testGetStart() {
    $start = $this->randomMachineName();

    $this->assertSame($this->usage, $this->usage->setStart($start));
    $this->assertSame($start, $this->usage->getStart());
  }

  /**
   * @covers ::setEnd
   * @covers ::getEnd
   */
  public function testGetEnd() {
    $end = $this->randomMachineName();

    $this->assertSame($this->usage, $this->usage->setEnd($end));
    $this->assertSame($end, $this->usage->getEnd());
  }

  /**
   * @covers ::setCountryCode
   * @covers ::getCountryCode
   */
  public function testGetCountryCode() {
    $country_code = $this->randomMachineName();

    $this->assertSame($this->usage, $this->usage->setCountryCode($country_code));
    $this->assertSame($country_code, $this->usage->getCountryCode());
  }

}
