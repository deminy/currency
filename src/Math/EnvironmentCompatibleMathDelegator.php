<?php

/**
 * @file Contains \Drupal\currency\Math\EnvironmentCompatibleMathDelegator.
 */

namespace Drupal\currency\Math;

use Drupal\currency\EnvironmentCompatibilityInterface;

/**
 * Delegates mathematical calculations to a suitable back-end.
 */
class EnvironmentCompatibleMathDelegator implements MathInterface {

  /**
   * The available math handlers.
   *
   * @var \Drupal\currency\Math\MathInterface[]
   */
  protected $mathHandlers = [];

  /**
   * The used math handler
   *
   * @see self::getMath()
   *
   * @var \Drupal\currency\Math\MathInterface
   */
  protected $mathHandler;

  /**
   * Adds an available math handler.
   *
   * Handlers are checked for availability in the order they are added.
   *
   * @param \Drupal\currency\Math\MathInterface
   *
   * @return $this
   */
  public function addMathHandler(MathInterface $handler) {
    $this->mathHandlers[] = $handler;

    return $this;
  }

  /**
   * Returns the math class.
   *
   * @return \Drupal\currency\Math\MathInterface
   *
   * @throws \Exception
   */
  protected function getMathHandler() {
    if (!$this->mathHandler) {
      foreach ($this->mathHandlers as $handler) {
        if ($handler instanceof EnvironmentCompatibilityInterface && !$handler->isCompatibleWithCurrentEnvironment()) {
          continue;
        }
        $this->mathHandler = $handler;
        break;
      }
    }

    if (!$this->mathHandler) {
      throw new \Exception('There are no available or compatible math handlers.');
    }

    return $this->mathHandler;
  }

  /**
   * {@inheritdoc}
   */
  public function add($number_a, $number_b) {
    return $this->getMathHandler()->add($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function subtract($number_a, $number_b) {
    return $this->getMathHandler()->subtract($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function multiply($number_a, $number_b) {
    return $this->getMathHandler()->multiply($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function divide($number_a, $number_b) {
    return $this->getMathHandler()->divide($number_a, $number_b);
  }

  /**
   * {@inheritdoc}
   */
  public function round($number, $rounding_step) {
    return $this->getMathHandler()->round($number, $rounding_step);
  }

  /**
   * {@inheritdoc}
   */
  public function compare($number_a, $number_b) {
    return $this->getMathHandler()->compare($number_a, $number_b);
  }

}
