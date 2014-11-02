<?php

/**
 * @file Contians \Drupal\Tests\currency\Unit\Math\NativeUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Math;

use Drupal\currency\Math\Native;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Math\Native
 *
 * @group Currency
 */
class NativeUnitTest extends UnitTestCase {

  /**
   * The math service under test.
   *
   * @var \Drupal\currency\Math\Native
   */
  protected $math;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->math = new Native();
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $this->assertSame(3.7539016, $this->math->add(1.2005729, 2.5533287));
    $this->assertSame(3.7539016, $this->math->add('1.2005729', '2.5533287'));
  }

  /**
   * @covers ::subtract
   */
  public function testSubtract() {
    $this->assertSame(-1.3527558, $this->math->subtract(1.2005729, 2.5533287));
    $this->assertSame(-1.3527558, $this->math->subtract('1.2005729', '2.5533287'));
  }

  /**
   * @covers ::multiply
   */
  public function testMultiply() {
    $this->assertSame(3.06545724201223, $this->math->multiply(1.2005729, 2.5533287));
    $this->assertSame(3.06545724201223, $this->math->multiply('1.2005729', '2.5533287'));
  }

  /**
   * @covers ::divide
   */
  public function testDivide() {
    $this->assertSame(1.2005729, $this->math->divide(3.06545724201223, 2.5533287));
    $this->assertSame(1.2005729, $this->math->divide('3.06545724201223', '2.5533287'));
  }

  /**
   * @covers ::round
   */
  public function testRound() {
    // Test a value that must be rounded down.
    $this->assertSame(2.5525, $this->math->round(2.5533287, 0.0025));
    $this->assertSame(2.5525, $this->math->round('2.5533287', '0.0025'));
    // Test a value that must be rounded up.
    $this->assertSame(100.77, $this->math->round(100.7654, 0.01));
    $this->assertSame(100.77, $this->math->round('100.7654', '0.01'));
  }

  /**
   * @covers ::compare
   */
  public function testCompare() {
    // Test two equal numbers.
    $this->assertSame(0, $this->math->compare(0.123456789, 0.123456789));
    $this->assertSame(0, $this->math->compare('0.123456789', '0.123456789'));

    // Test with the first operand larger than the second.
    $this->assertSame(1, $this->math->compare(1.000000001, 1));
    $this->assertSame(1, $this->math->compare('1.000000001', '1'));

    // Test with the first operand smaller than the second.
    $this->assertSame(-1, $this->math->compare(1, 1.000000001));
    $this->assertSame(-1, $this->math->compare('1', '1.000000001'));
  }

}
