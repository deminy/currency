<?php

/**
 * @file Contains \Drupal\currency\Math\BcMath.
 */

namespace Drupal\currency\Math;

/**
 * Provides BC Math mathematical functions.
 */
class BcMath implements MathInterface {

  /**
   * The number of decimals for BC Math calculations.
   *
   * @var int
   */
  protected $precision = 9;

  /**
   * Gets the BC Math precision.
   *
   * @param int $precision
   *
   * @return $this
   */
  public function setPrecision($precision) {
    $this->precision = $precision;

    return $this;
  }

  /**
   * Gets the BC Math precision.
   *
   * @return int
   */
  public function getPrecision() {
    return $this->precision;
  }

  /**
   * {@inheritdoc}
   */
  public function add($number_a, $number_b) {
    return bcadd($number_a, $number_b, $this->getPrecision());
  }

  /**
   * {@inheritdoc}
   */
  public function subtract($number_a, $number_b) {
    return bcsub($number_a, $number_b, $this->getPrecision());
  }

  /**
   * {@inheritdoc}
   */
  public function multiply($number_a, $number_b) {
    return bcmul($number_a, $number_b, $this->getPrecision());
  }

  /**
   * {@inheritdoc}
   */
  public function divide($number_a, $number_b) {
    return bcdiv($number_a, $number_b, $this->getPrecision());
  }

  /**
   * {@inheritdoc}
   */
  public function round($number, $rounding_step) {
    return bcmul(round(bcdiv($number, $rounding_step, $this->getPrecision())), $rounding_step, $this->getPrecision());
  }

  /**
   * {@inheritdoc}
   */
  public function compare($number_a, $number_b) {
    return bccomp($number_a, $number_b, $this->getPrecision());
  }

}
