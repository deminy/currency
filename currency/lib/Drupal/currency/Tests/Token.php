<?php

/**
 * @file
 * Contains class \Drupal\currency\Tests\Token.
 */

namespace Drupal\currency\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests token integration.
 */
class Token extends WebTestBase {

  public static $modules = array('currency');

  /**
   * Overrides parent::getInfo().
   */
  static function getInfo() {
    return array(
      'name' => 'Token integration',
      'group' => 'Currency',
    );
  }

  /**
   * Tests token integration.
   */
  function testTokenIntegration() {
    $data = array(
      'EUR' => array(
        '[currency:code]' => 'EUR',
        '[currency:number]' => '978',
        '[currency:subunits]' => '100',
      ),
      'BHD' => array(
        '[currency:code]' => 'BHD',
        '[currency:number]' => '048',
        '[currency:subunits]' => '1000',
      ),
    );
    foreach ($data as $currency_code => $tokens) {
      $data = array(
        'currency' => $currency_code,
      );
      foreach ($tokens as $token => $replacement) {
        $this->assertEqual(token_replace($token, $data), $replacement);
      }
    }
  }
}
