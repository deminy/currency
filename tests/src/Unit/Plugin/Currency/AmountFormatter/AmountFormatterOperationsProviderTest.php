<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Currency\AmountFormatter\AmountFormatterOperationsProviderTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Currency\AmountFormatter;

use Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterOperationsProvider;
use Drupal\Tests\plugin\Unit\PluginType\DefaultPluginTypeOperationsProviderTest;

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
