<?php

/**
 * @file Contains \Drupal\currency\Math\MathDelegator.
 */

namespace Drupal\currency\Math;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delegates mathematical calculations to a suitable back-end.
 */
class MathDelegator implements MathInterface {

  /**
   * The container.
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * The wrapped math class.
   *
   * @var \Drupal\currency\Math\MathInterface
   */
  protected $math;

  /**
   * Constructs a new instance.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface
   */
  public function __construct(ContainerInterface $container) {
    $this->container = $container;
  }

  /**
   * Returns the math class.
   *
   * @return \Drupal\currency\Math\MathInterface
   */
  protected function getMath() {
    if (!$this->math) {
      if ($this->isExtensionLoaded('bcmath')) {
        $this->math = $this->container->get('currency.math.bcmath');
      }
      else {
        $this->math = $this->container->get('currency.math.native');
      }
    }

    return $this->math;
  }

  /**
   * {@inheritdoc}
   */
  public function add($number_a, $number_b) {
    return $this->getMath()->add($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function subtract($number_a, $number_b) {
    return $this->getMath()->subtract($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function multiply($number_a, $number_b) {
    return $this->getMath()->multiply($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function divide($number_a, $number_b) {
    return $this->getMath()->divide($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function round($number, $rounding_step) {
    return $this->getMath()->round($number, $rounding_step);
  }

  /**
   * {@inheritdoc}
   */
  public function compare($number_a, $number_b) {
    return $this->getMath()->compare($number_a, $number_b);
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
