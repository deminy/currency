<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\InputTest.
 */

namespace Drupal\Tests\currency\Unit;

use Drupal\currency\Input;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Input
 *
 * @group Currency
 */
class InputTest extends UnitTestCase {

  /**
   * The input parser under test.
   *
   * @var \Drupal\currency\Input
   */
  protected $input;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->input = new Input();
  }

  /**
   * @covers ::parseAmount
   * @covers ::parseAmountDecimalSeparator
   * @covers ::parseAmountNegativeFormat
   */
  public function testParseAmount() {
    $amounts_invalid = array(
      'a',
      'a123',
      '123%',
      '.5.',
      '123,456,789.00,00',
    );
    foreach ($amounts_invalid as $amount) {
      $this->assertFalse($this->input->parseAmount($amount));
    }
    $amounts_valid = array(
      // Integers.
      array(123, '123'),
      // Floats.
      array(123.456, '123.456'),
      array(-123.456, '-123.456'),
      // Integer strings.
      array('123', '123'),
      // Decimal strings using different decimal separators.
      array('123.456', '123.456'),
      array('123,456', '123.456'),
      array('123٫456', '123.456'),
      array('123/456', '123.456'),
      // Negative strings.
      array('-123', '-123'),
      array('(123)', '-123'),
      array('123-', '-123'),
      array('--123', '123'),
      array('(--123-)', '123'),
    );
    foreach ($amounts_valid as $amount) {
      $amount_validated = NULL;
      $amount_validated = $this->input->parseAmount($amount[0]);
      $this->assertEquals($amount_validated, $amount[1]);
    }
  }

}
