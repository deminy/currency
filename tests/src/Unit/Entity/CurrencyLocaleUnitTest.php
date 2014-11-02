<?php

/**
 * @file
 * Contains \Tests\currency\Unit\Entity\CurrencyLocaleUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Entity;

use Drupal\Core\Language\LanguageManager;
use Drupal\currency\Entity\CurrencyLocale;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\simpletest\KernelTestBase;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Entity\CurrencyLocale
 *
 * @group Currency
 */
class CurrencyLocaleUnitTest extends UnitTestCase {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $countryManager;

  /**
   * The currency locale under test.
   *
   * @var \Drupal\currency\Entity\CurrencyLocale
   */
  protected $currencyLocale;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   *
   * @covers ::setCountryManager
   */
  function setUp() {
    $this->countryManager = $this->getMock('\Drupal\Core\Locale\CountryManagerInterface');

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->currencyLocale = new CurrencyLocale([], $this->randomMachineName());
    $this->currencyLocale->setCountryManager($this->countryManager);
    $this->currencyLocale->setStringTranslation($this->stringTranslation);
  }

  /**
   * @covers ::setDecimalSeparator
   * @covers ::getDecimalSeparator
   */
  function testGetDecimalSeparator() {
    $separator = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setDecimalSeparator($separator));
    $this->assertSame($separator, $this->currencyLocale->getDecimalSeparator());
  }

  /**
   * @covers ::setGroupingSeparator
   * @covers ::getGroupingSeparator
   */
  function testGetGroupingSeparator() {
    $separator = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setGroupingSeparator($separator));
    $this->assertSame($separator, $this->currencyLocale->getGroupingSeparator());
  }

  /**
   * @covers ::setLocale
   * @covers ::getLocale
   */
  function testGetLocale() {
    $language_code = $this->randomMachineName();
    $country_code = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setLocale($language_code, $country_code));
    $this->assertSame(strtolower($language_code) . '_' . strtoupper($country_code), $this->currencyLocale->getLocale());
  }

  /**
   * @covers ::id
   *
   * @depends testGetLocale
   */
  function testId() {
    $language_code = $this->randomMachineName();
    $country_code = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setLocale($language_code, $country_code));
    $this->assertSame(strtolower($language_code) . '_' . strtoupper($country_code), $this->currencyLocale->id());
  }

  /**
   * @covers ::getLanguageCode
   *
   * @depends testGetLocale
   */
  function testGetLanguageCode() {
    $language_code = $this->randomMachineName();
    $country_code = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setLocale($language_code, $country_code));
    $this->assertSame(strtolower($language_code), $this->currencyLocale->getLanguageCode());
  }

  /**
   * @covers ::getCountryCode
   *
   * @depends testGetLocale
   */
  function testGetCountryCode() {
    $language_code = $this->randomMachineName();
    $country_code = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setLocale($language_code, $country_code));
    $this->assertSame(strtoupper($country_code), $this->currencyLocale->getCountryCode());
  }

  /**
   * @covers ::setPattern
   * @covers ::getPattern
   */
  function testGetPattern() {
    $pattern = $this->randomMachineName();
    $this->assertSame($this->currencyLocale, $this->currencyLocale->setPattern($pattern));
    $this->assertSame($pattern, $this->currencyLocale->getPattern());
  }

  /**
   * @covers ::label
   * @covers ::getCountryManager
   *
   * @depends testGetLocale
   */
  function testLabel() {
    $languages = LanguageManager::getStandardLanguageList();

    $language_code = array_rand($languages);

    $country_code_a = strtoupper($this->randomMachineName());
    $country_code_b = strtoupper($this->randomMachineName());
    $country_code_c = strtoupper($this->randomMachineName());

    $country_list = [
      $country_code_a => $this->randomMachineName(),
      $country_code_b => $this->randomMachineName(),
      $country_code_c => $this->randomMachineName(),
    ];

    $this->countryManager->expects($this->atLeastOnce())
      ->method('getList')
      ->willReturn($country_list);

    $this->currencyLocale->setLocale($language_code, $country_code_b);

    $expected = $languages[$language_code][0] . ' (' . $country_list[$country_code_b] . ')';
    $this->assertSame($expected, $this->currencyLocale->label());
  }

  /**
   * @covers ::toArray
   */
  public function testToArray() {
    $language_code = strtolower($this->randomMachineName());

    $country_code = strtoupper($this->randomMachineName());

    $expected_array = [
      'decimalSeparator' => $this->randomMachineName(),
      'groupingSeparator' => $this->randomMachineName(),
      'locale' => $language_code . '_' . $country_code,
      'pattern' => $this->randomMachineName(),
    ];

    $this->currencyLocale->setLocale($language_code, $country_code);
    $this->currencyLocale->setDecimalSeparator($expected_array['decimalSeparator']);
    $this->currencyLocale->setGroupingSeparator($expected_array['groupingSeparator']);
    $this->currencyLocale->setPattern($expected_array['pattern']);

    $array = $this->currencyLocale->toArray();
    $this->assertArrayHasKey('uuid', $array);
    unset($array['uuid']);
    $this->assertEquals($expected_array, $array);
  }

}
