<?php

/**
 * @file Contains \Drupal\currency\Math.
 */

namespace Drupal\currency;

/**
 * Provides mathematical functions.
 */
class Math implements MathInterface {

  /**
   * The number of decimals for BCMath calculations.
   *
   * @var int
   */
  protected $bcmathPrecision = 9;

  /**
   * Gets the BCMath precision.
   *
   * @param int $precision
   *
   * @return $this
   */
  public function setBcmathPrecision($precision) {
    $this->bcmathPrecision = $precision;

    return $this;
  }

  /**
   * Gets the BCMath precision.
   *
   * @return int
   */
  public function getBcmathPrecision() {
    return $this->bcmathPrecision;
  }

  /**
   * {@inheritdoc}
   */
  public function add($number_a, $number_b) {
    if ($this->isExtensionLoaded('bcmath')) {
      return bcadd($number_a, $number_b, $this->getBcmathPrecision());
    }
    else {
      return $number_a + $number_b;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function subtract($number_a, $number_b) {
    if ($this->isExtensionLoaded('bcmath')) {
      return bcsub($number_a, $number_b, $this->getBcmathPrecision());
    }
    else {
      return $number_a - $number_b;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function multiply($number_a, $number_b) {
    if ($this->isExtensionLoaded('bcmath')) {
      return bcmul($number_a, $number_b, $this->getBcmathPrecision());
    }
    else {
      return $number_a * $number_b;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function divide($number_a, $number_b) {
    if ($this->isExtensionLoaded('bcmath')) {
      return bcdiv($number_a, $number_b, $this->getBcmathPrecision());
    }
    else {
      return $number_a / $number_b;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function round($number, $rounding_step) {
    if ($this->isExtensionLoaded('bcmath')) {
      return bcmul(round(bcdiv($number, $rounding_step, $this->getBcmathPrecision())), $rounding_step, $this->getBcmathPrecision());
    }
    else {
      return round($number / $rounding_step) * $rounding_step;
    }
  }

  /**
   * Wraps extension_loaded().
   *
   * @param string $name
   *   The name of the extension.
   *
   * @return boolean
   */
  protected function isExtensionLoaded($name) {
    return extension_loaded($name);
  }
}
