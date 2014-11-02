<?php

/**
 * @file Contians \Drupal\Tests\currency\Unit\Math\MathDelegatorUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Math;

use Drupal\currency\Math\MathDelegator;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Math\MathDelegator
 *
 * @group Currency
 */
class MathDelegatorUnitTest extends UnitTestCase {

  /**
   * The container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $container;

  /**
   * The math service under test.
   *
   * @var \Drupal\currency\Math\MathDelegator|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $math;

  /**
   * The BC Math math service.
   *
   * @var \Drupal\currency\Math\MathDelegator|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $mathBcMath;

  /**
   * The native math service.
   *
   * @var \Drupal\currency\Math\MathDelegator|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $mathNative;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->mathBcMath = $this->getMock('\Drupal\currency\Math\MathInterface');

    $this->mathNative = $this->getMock('\Drupal\currency\Math\MathInterface');

    $this->container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('currency.math.bcmath', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mathBcMath),
      array('currency.math.native', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->mathNative),
    );
    $this->container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $this->math = $this->getMockBuilder('\Drupal\currency\Math\MathDelegator')
      ->setConstructorArgs(array($this->container))
      ->setMethods(array('isExtensionLoaded'))
      ->getMock();
  }

  /**
   * @covers ::getMath
   */
  public function testGetMathBcMath() {
    $this->math->expects($this->once())
      ->method('isExtensionLoaded')
      ->with('bcmath')
      ->willReturn(TRUE);

    $method = new \ReflectionMethod($this->math, 'getMath');
    $method->setAccessible(TRUE);

    $this->assertSame($this->mathBcMath, $method->invoke($this->math));
  }

  /**
   * @covers ::getMath
   */
  public function testGetMathNative() {
    $this->math->expects($this->once())
      ->method('isExtensionLoaded')
      ->with('bcmath')
      ->willReturn(FALSE);

    $method = new \ReflectionMethod($this->math, 'getMath');
    $method->setAccessible(TRUE);

    $this->assertSame($this->mathNative, $method->invoke($this->math));
  }

  /**
   * @covers ::add
   */
  public function testAdd() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();
    $this->mathNative->expects($this->once())
      ->method('add')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->assertSame($result, $this->math->add($number_a, $number_b));
  }

  /**
   * @covers ::subtract
   */
  public function testSubtract() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();
    $this->mathNative->expects($this->once())
      ->method('subtract')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->assertSame($result, $this->math->subtract($number_a, $number_b));
  }

  /**
   * @covers ::multiply
   */
  public function testMultiply() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();
    $this->mathNative->expects($this->once())
      ->method('multiply')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->assertSame($result, $this->math->multiply($number_a, $number_b));
  }

  /**
   * @covers ::divide
   */
  public function testDivide() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();
    $this->mathNative->expects($this->once())
      ->method('divide')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->assertSame($result, $this->math->divide($number_a, $number_b));
  }

  /**
   * @covers ::round
   */
  public function testRound() {
    $number = mt_rand();
    $rounding_step = mt_rand();
    $result = mt_rand();
    $this->mathNative->expects($this->once())
      ->method('round')
      ->with($number, $rounding_step)
      ->willReturn($result);

    $this->assertSame($result, $this->math->round($number, $rounding_step));
  }

  /**
   * @covers ::compare
   */
  public function testCompare() {
    $number_a = mt_rand();
    $number_b = mt_rand();
    $result = mt_rand();
    $this->mathNative->expects($this->once())
      ->method('compare')
      ->with($number_a, $number_b)
      ->willReturn($result);

    $this->assertSame($result, $this->math->compare($number_a, $number_b));
  }

  /**
   * @covers ::isExtensionLoaded
   */
  public function testIsExtensionLoaded() {
    $math = new MathDelegator($this->container);

    $method = new \ReflectionMethod($math, 'isExtensionLoaded');
    $method->setAccessible(TRUE);

    $extensions = array('bcmath', $this->randomMachineName());
    foreach ($extensions as $extension) {
      $this->assertSame(extension_loaded($extension), $method->invoke($math, $extension));
    }
  }

}
