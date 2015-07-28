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
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->sut = new Usage();
  }

  /**
   * @covers ::setStart
   * @covers ::getStart
   */
  public function testGetStart() {
    $start = $this->randomMachineName();

    $this->assertSame($this->sut, $this->sut->setStart($start));
    $this->assertSame($start, $this->sut->getStart());
  }

  /**
   * @covers ::setEnd
   * @covers ::getEnd
   */
  public function testGetEnd() {
    $end = $this->randomMachineName();

    $this->assertSame($this->sut, $this->sut->setEnd($end));
    $this->assertSame($end, $this->sut->getEnd());
  }

  /**
   * @covers ::setCountryCode
   * @covers ::getCountryCode
   */
  public function testGetCountryCode() {
    $country_code = $this->randomMachineName();

    $this->assertSame($this->sut, $this->sut->setCountryCode($country_code));
    $this->assertSame($country_code, $this->sut->getCountryCode());
  }

}
