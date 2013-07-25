<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\InputTest.
 */

namespace Drupal\currency\Tests;

use Drupal\currency\Input;
use Drupal\simpletest\UnitTestBase;

/**
 * Tests \Drupal\currency\Input
 */
class InputTest extends UnitTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'description' => '',
      'name' => 'Drupal\currency\Input',
      'group' => 'Currency',
    );
  }

  /**
   * Tests parseAmount().
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
      $this->assertFalse(Input::parseAmount($amount));
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
      $amount_validated = Input::parseAmount($amount[0]);
      $this->assertEqual($amount_validated, $amount[1]);
    }
  }
}
