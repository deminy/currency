<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Token.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Token integration.
 *
 * @group Currency
 */
class Token extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Tests token integration.
   */
  function testTokenIntegration() {
    $token_service = \Drupal::token();

    $tokens = array(
      '[currency:code]' => 'XXX',
      '[currency:number]' => '999',
      '[currency:subunits]' => '0',
    );
    $data = array(
      'currency' => 'XXX',
    );
    foreach ($tokens as $token => $replacement) {
      $this->assertEqual($token_service->replace($token, $data), $replacement);
    }
  }
}
