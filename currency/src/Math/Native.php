<?php

/**
 * @file Contains \Drupal\currency\Math\Native.
 */

namespace Drupal\currency\Math;

/**
 * Provides native PHP mathematical functions.
 */
class Native implements MathInterface {

  /**
   * {@inheritdoc}
   */
  public function add($number_a, $number_b) {
    return $number_a + $number_b;
  }

  /**
   * {@inheritdoc}
   */
  public function subtract($number_a, $number_b) {
    return $number_a - $number_b;
  }

  /**
   * {@inheritdoc}
   */
  public function multiply($number_a, $number_b) {
    return $number_a * $number_b;
  }

  /**
   * {@inheritdoc}
   */
  public function divide($number_a, $number_b) {
    return $number_a / $number_b;
  }

  /**
   * {@inheritdoc}
   */
  public function round($number, $rounding_step) {
    return round($number / $rounding_step) * $rounding_step;
  }

  /**
   * {@inheritdoc}
   */
  public function compare($number_a, $number_b) {
    if ($number_a == $number_b) {
      return 0;
    }
    elseif ($number_a > $number_b) {
      return 1;
    }
    else {
      return -1;
    }
  }

}
