<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderOperationsProviderTest.
 */

namespace Drupal\Tests\plugin\Unit;

use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderOperationsProvider;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderOperationsProvider
 *
 * @group Currency
 */
class ExchangeRateProviderOperationsProviderTest extends DefaultPluginTypeOperationsProviderTest {

  /**
   * The class under test.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderOperationsProvider
   */
  protected $sut;

  public function setUp() {
    parent::setUp();

    $this->sut = new ExchangeRateProviderOperationsProvider($this->stringTranslation);
  }

}
