<?php

/**
 * @file
 * Contains \Drupal\currency\EnvironmentCompatibilityInterface.
 */

namespace Drupal\currency;

/**
 * Defines an environment compatibility check.
 */
interface EnvironmentCompatibilityInterface {

  /**
   * Checks compatibility with the current environment.
   *
   * @return bool
   *   Whether $this is compatible with the current environment.
   */
  public function isCompatibleWithCurrentEnvironment();

}
