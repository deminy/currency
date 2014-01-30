<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Controller\AmountFormattingFormWebTest.
 */

namespace Drupal\currency\Tests\Controller;

use Drupal\simpletest\WebTestBase;

/**
 * Tests \Drupal\currency\Controller\AmountFormattingForm.
 */
class AmountFormattingFormWebTest extends WebTestBase {

  public static $modules = array('currency');

  /**
   * {@inheritdoc}
   */
  static function getInfo() {
    return array(
      'description' => '',
      'name' => '\Drupal\currency\Controller\AmountFormattingForm web test',
      'group' => 'Currency',
    );
  }

  /**
   * Tests listing().
   */
  function testListing() {
    $account = $this->drupalCreateUser(array('access administration pages'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency-formatting');
    $this->assertResponse('403');

    $account = $this->drupalCreateUser(array('currency.amount_formatting.administer'));
    $this->drupalLogin($account);
    $this->drupalGet('admin/config/regional/currency-formatting');
    $this->assertResponse('200');

    $this->assertFieldChecked('edit-default-plugin-id-currency-basic');
    $this->drupalPostForm(NULL, array(), t('Save configuration'));
  }
}
