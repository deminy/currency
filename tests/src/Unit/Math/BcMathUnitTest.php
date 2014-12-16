<?php

/**
 * @file Contians \Drupal\Tests\currency\Unit\Math\BcMathUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Math;

use Drupal\currency\Math\BcMath;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Math\BcMath
 *
 * @requires extension BCMath
 *
 * @group Currency
 */
class BcMathUnitTest extends UnitTestCase {

  /**
   * The math service under test.
   *
   * @var \Drupal\currency\Math\BcMath
   */
  protected $math;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->math = new BcMath();
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $this->assertSame('3.753901600', $this->math->add(1.2005729, 2.5533287));
    $this->assertSame('3.753901600', $this->math->add('1.2005729', '2.5533287'));
  }

  /**
   * @covers ::subtract
   */
  public function testSubtract() {
    $this->assertSame('-1.352755800', $this->math->subtract(1.2005729, 2.5533287));
    $this->assertSame('-1.352755800', $this->math->subtract('1.2005729', '2.5533287'));
  }

  /**
   * @covers ::multiply
   */
  public function testMultiply() {
    $this->assertSame('3.065457242', $this->math->multiply(1.2005729, 2.5533287));
    $this->assertSame('3.065457242', $this->math->multiply('1.2005729', '2.5533287'));
  }

  /**
   * @covers ::divide
   */
  public function testDivide() {
    $this->assertSame('3.000000000', $this->math->divide(6, 2));
    $this->assertSame('1.200572900', $this->math->divide('3.06545724201223', '2.5533287'));
  }

  /**
   * @covers ::round
   */
  public function testRound() {
    // Test a value that must be rounded down.
    $this->assertSame('2.5525', $this->math->round(2.5533287, 0.0025));
    $this->assertSame('2.5525', $this->math->round('2.5533287', '0.0025'));
    // Test a value that must be rounded up.
    $this->assertSame('100.77', $this->math->round(100.7654, 0.01));
    $this->assertSame('100.77', $this->math->round('100.7654', '0.01'));
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

  /**
   * @covers ::setPrecision
   * @covers ::getPrecision
   */
  public function testGetBcmathPrecision() {
    $precision = mt_rand();

    $this->assertSame($this->math, $this->math->setPrecision($precision));
    $this->assertSame($precision, $this->math->getPrecision());
  }

}
