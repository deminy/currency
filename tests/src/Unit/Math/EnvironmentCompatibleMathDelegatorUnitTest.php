<?php

/**
 * @file Contians \Drupal\Tests\currency\Unit\Math\EnvironmentCompatibleMathDelegatorUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Math;

use Drupal\currency\EnvironmentCompatibilityInterface;
use Drupal\currency\Math\EnvironmentCompatibleMathDelegator;
use Drupal\currency\Math\MathInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Math\EnvironmentCompatibleMathDelegator
 *
 * @group Currency
 */
class EnvironmentCompatibleMathDelegatorUnitTest extends UnitTestCase {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Math\EnvironmentCompatibleMathDelegator
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->sut = new EnvironmentCompatibleMathDelegator();
  }

  /**
   * @covers ::addMathHandler
   * @covers ::getMathHandler
   *
   * @dataProvider providerTestGetMathHandler
   */
  public function testGetMathHandler($environment_compatibility) {
    $compatibility_handler = $this->getMockForAbstractClass('\Drupal\Tests\currency\Unit\Math\EnvironmentCompatibleMathDelegatorUnitTestEnvironmentCompatibilityMath');
    $compatibility_handler->expects($this->atLeastOnce())
      ->method('isCompatibleWithCurrentEnvironment')
      ->willReturn($environment_compatibility);

    $simple_handler = $this->getMock('\Drupal\currency\Math\MathInterface');

    $this->sut->addMathHandler($compatibility_handler)
      ->addMathHandler($simple_handler);

    $method = new \ReflectionMethod($this->sut, 'getMathHandler');
    $method->setAccessible(TRUE);

    $this->assertSame($environment_compatibility ? $compatibility_handler : $simple_handler, $method->invoke($this->sut));
  }

  /**
   * Provides data to self::providerTestGetMathHandler().
   */
  public function providerTestGetMathHandler() {
    return [
      [TRUE],
      [FALSE],
    ];
  }

  /**
   * @covers ::getMathHandler
   *
   * @expectedException \Exception
   */
  public function testGetMathHandlerWithoutHandlers() {
    $method = new \ReflectionMethod($this->sut, 'getMathHandler');
    $method->setAccessible(TRUE);

    $method->invoke($this->sut);
  }

  /**
   * @covers ::add
   * @covers ::addMathHandler
   *
   * @depends testGetMathHandler
   */
  public function testAdd() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();

    $handler = $this->getMock('\Drupal\currency\Math\MathInterface');
    $handler->expects($this->once())
      ->method('add')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->sut->addMathHandler($handler);


    $this->assertSame($result, $this->sut->add($number_a, $number_b));
  }

  /**
   * @covers ::subtract
   * @covers ::addMathHandler
   *
   * @depends testGetMathHandler
   */
  public function testSubtract() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();

    $handler = $this->getMock('\Drupal\currency\Math\MathInterface');
    $handler->expects($this->once())
      ->method('subtract')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->sut->addMathHandler($handler);

    $this->assertSame($result, $this->sut->subtract($number_a, $number_b));
  }

  /**
   * @covers ::multiply
   * @covers ::addMathHandler
   *
   * @depends testGetMathHandler
   */
  public function testMultiply() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();

    $handler = $this->getMock('\Drupal\currency\Math\MathInterface');
    $handler->expects($this->once())
      ->method('multiply')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->sut->addMathHandler($handler);

    $this->assertSame($result, $this->sut->multiply($number_a, $number_b));
  }

  /**
   * @covers ::divide
   * @covers ::addMathHandler
   *
   * @depends testGetMathHandler
   */
  public function testDivide() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();

    $handler = $this->getMock('\Drupal\currency\Math\MathInterface');
    $handler->expects($this->once())
      ->method('divide')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->sut->addMathHandler($handler);

    $this->assertSame($result, $this->sut->divide($number_a, $number_b));
  }

  /**
   * @covers ::round
   * @covers ::addMathHandler
   *
   * @depends testGetMathHandler
   */
  public function testRound() {
    $number = mt_rand();
    $rounding_step = mt_rand();
    $result = mt_rand();

    $handler = $this->getMock('\Drupal\currency\Math\MathInterface');
    $handler->expects($this->once())
      ->method('round')
      ->with($number, $rounding_step)
      ->willReturn($result);

    $this->sut->addMathHandler($handler);

    $this->assertSame($result, $this->sut->round($number, $rounding_step));
  }

  /**
   * @covers ::compare
   * @covers ::addMathHandler
   *
   * @depends testGetMathHandler
   */
  public function testCompare() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();

    $handler = $this->getMock('\Drupal\currency\Math\MathInterface');
    $handler->expects($this->once())
      ->method('compare')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->sut->addMathHandler($handler);

    $this->assertSame($result, $this->sut->compare($number_a, $number_b));
  }

}

/**
 * Provides a math handler with environment compatibility.
 */
abstract class EnvironmentCompatibleMathDelegatorUnitTestEnvironmentCompatibilityMath implements MathInterface, EnvironmentCompatibilityInterface {
}
