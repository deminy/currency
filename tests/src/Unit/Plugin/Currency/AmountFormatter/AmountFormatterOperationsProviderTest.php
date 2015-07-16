<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Currency\AmountFormatter\AmountFormatterOperationsProviderTest.
 */

namespace Drupal\Tests\plugin\Unit;

use Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterOperationsProvider;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterOperationsProvider
 *
 * @group Currency
 */
class AmountFormatterOperationsProviderTest extends DefaultPluginTypeOperationsProviderTest {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterOperationsProvider
   */
  protected $sut;

  public function setUp() {
    parent::setUp();

    $this->sut = new AmountFormatterOperationsProvider($this->stringTranslation);
  }

}
