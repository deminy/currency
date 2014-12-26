<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\CurrencyUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Entity;

use Drupal\currency\Entity\Currency;
use Drupal\currency\Usage;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Entity\Currency
 *
 * @group Currency
 */
class CurrencyUnitTest extends UnitTestCase {

  /**
   * The currency under test.
   *
   * @var \Drupal\currency\Entity\Currency
   */
  protected $currency;

  /**
   * The currency amount formatter manager.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyAmountFormatterManager;

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityManager;

  /**
   * The entity type ID.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * The math provider.
   *
   * @var \Drupal\currency\Math\MathInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  public $math;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   * @covers ::setEntityManager
   * @covers ::setCurrencyAmountFormatterManager
   * @covers ::setMath
   */
  function setUp() {
    $this->entityTypeId = $this->randomMachineName();

    $this->currencyAmountFormatterManager = $this->getMock('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface');

    $this->entityManager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');

    $this->math = $this->getMock('\Drupal\currency\Math\MathInterface');

    $this->currency = new Currency([], $this->entityTypeId);
    $this->currency->setCurrencyAmountFormatterManager($this->currencyAmountFormatterManager);
    $this->currency->setEntityManager($this->entityManager);
    $this->currency->setMath($this->math);
  }
  /**
   * @covers ::getRoundingStep
   * @covers ::setRoundingStep
   * @covers ::getMath
   */
  function testGetRoundingStep() {
    $rounding_step = mt_rand();

    $this->assertSame($this->currency, $this->currency->setRoundingStep($rounding_step));
    $this->assertSame($rounding_step, $this->currency->getRoundingStep());
  }

  /**
   * @covers ::getRoundingStep
   */
  function testGetRoundingStepBySubunits() {
    $subunits = mt_rand();
    $rounding_step = mt_rand();

    $this->math->expects($this->atLeastOnce())
      ->method('divide')
      ->with(1, $subunits)
      ->willReturn($rounding_step);

    $this->currency->setSubunits($subunits);

    $this->assertSame($rounding_step, $this->currency->getRoundingStep());
  }

  /**
   * @covers ::getRoundingStep
   */
  function testGetRoundingStepUnavailable() {
    $this->assertNull($this->currency->getRoundingStep());
  }

  /**
   * @covers ::formatAmount
   * @covers ::getCurrencyAmountFormatterManager
   * @covers ::getMath
   *
   * @depends testGetRoundingStep
   *
   * @dataProvider providerTestFormatAmount
   */
  function testFormatAmount($expected, $amount, $amount_with_currency_precision_applied) {
    $amount_formatter = $this->getMock('\Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface');
    $amount_formatter->expects($this->atLeastOnce())
      ->method('formatAmount')
      ->with($this->currency, $amount_with_currency_precision_applied)
      ->willReturn($expected);

    $this->currencyAmountFormatterManager->expects($this->atLeastOnce())
      ->method('getDefaultPlugin')
      ->willReturn($amount_formatter);

    if ($amount !== $amount_with_currency_precision_applied) {
      $this->math->expects($this->atLeastOnce())
        ->method('round')
        ->with($amount, $this->currency->getRoundingStep())
        ->willReturn($amount_with_currency_precision_applied);
    }
    else {
      $this->math->expects($this->never())
        ->method('round');
    }

    $this->currency->setCurrencyCode('BLA');
    $this->currency->setSubunits(100);

    $this->assertSame($expected, $this->currency->formatAmount($amount, $amount !== $amount_with_currency_precision_applied));
  }

  /**
   * Provides data to self::testFormatAmount().
   */
  public function providerTestFormatAmount() {
    return [
      ['BLA 12,345.68', '12345.6789', '12345.67'],
      ['BLA 12,345.6789', '12345.6789', '12345.6789'],
    ];
  }

  /**
   * @covers ::getDecimals
   */
  function testGetDecimals() {
    foreach ([1, 2, 3] as $decimals) {
      $this->currency->setSubunits(pow(10, $decimals));
      $this->assertSame($decimals, $this->currency->getDecimals());
    }
  }

  /**
   * @covers ::isObsolete
   */
  function testIsObsolete() {
    // A currency without usage data.
    $this->assertFalse($this->currency->isObsolete());

    // A currency that is no longer being used.
    $usage = new Usage();
    $usage->setStart('1813-01-01')
      ->setEnd('2002-02-28');
    $this->currency->setUsages([$usage]);
    $this->assertTrue($this->currency->isObsolete());

    // A currency that will become obsolete next year.
    $usage = new Usage();
    $usage->setStart('1813-01-01')
      ->setEnd(date('o') + 1 . '-02-28');
    $this->currency->setUsages([$usage]);
    $this->assertFalse($this->currency->isObsolete());
  }

  /**
   * @covers ::getAlternativeSigns
   * @covers ::setAlternativeSigns
   */
  function testGetAlternativeSigns() {
    $alternative_signs = ['A', 'B'];
    $this->assertSame($this->currency, $this->currency->setAlternativeSigns($alternative_signs));
    $this->assertSame($alternative_signs, $this->currency->getAlternativeSigns());
  }

  /**
   * @covers ::id
   * @covers ::setCurrencyCode
   */
  function testId() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame($this->currency, $this->currency->setCurrencyCode($currency_code));
    $this->assertSame($currency_code, $this->currency->id());
  }

  /**
   * @covers ::getCurrencyCode
   * @covers ::setCurrencyCode
   */
  function testGetCurrencyCode() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame($this->currency, $this->currency->setCurrencyCode($currency_code));
    $this->assertSame($currency_code, $this->currency->getCurrencyCode());
  }

  /**
   * @covers ::getCurrencyNumber
   * @covers ::setCurrencyNumber
   */
  function testGetCurrencyNumber() {
    $currency_number = '000';
    $this->assertSame($this->currency, $this->currency->setCurrencyNumber($currency_number));
    $this->assertSame($currency_number, $this->currency->getCurrencyNumber());
  }

  /**
   * @covers ::label
   * @covers ::setLabel
   */
  function testLabel() {
    $entity_type = $this->getMock('\Drupal\Core\Entity\EntityTypeInterface');
    $entity_type->expects($this->atLeastOnce())
      ->method('getKey')
      ->with('label')
      ->willReturn('label');

    $this->entityManager->expects($this->atLeastOnce())
      ->method('getDefinition')
      ->with($this->entityTypeId)
      ->willReturn($entity_type);

    $label = $this->randomMachineName();
    $this->assertSame($this->currency, $this->currency->setLabel($label));
    $this->assertSame($label, $this->currency->label());
  }

  /**
   * @covers ::getSign
   * @covers ::setSign
   */
  function testGetSign() {
    $sign = $this->randomMachineName(1);
    $this->assertSame($this->currency, $this->currency->setSign($sign));
    $this->assertSame($sign, $this->currency->getSign());
  }

  /**
   * @covers ::setSubunits
   * @covers ::getSubunits
   */
  function testGetSubunits() {
    $subunits = 73;
    $this->assertSame($this->currency, $this->currency->setSubunits($subunits));
    $this->assertSame($subunits, $this->currency->getSubunits());
  }

  /**
   * @covers ::setUsages
   * @covers ::getUsages
   */
  function testGetUsage() {
    $usage = new Usage();
    $usage->setStart('1813-01-01')
    ->setEnd(date('o') + 1 . '-02-28');
    $this->assertSame($this->currency, $this->currency->setUsages([$usage]));
    $this->assertSame([$usage], $this->currency->getUsages());
  }

  /**
   * @covers ::entityManager
   */
  public function testEntityManager() {
    $method = new \ReflectionMethod($this->currency, 'entityManager');
    $method->setAccessible(TRUE);
    $this->assertSame($this->entityManager, $method->invoke($this->currency));
  }

  /**
   * @covers ::toArray
   */
  public function testToArray() {
    $entity_type = $this->getMock('\Drupal\Core\Entity\EntityTypeInterface');
    $entity_type->expects($this->atLeastOnce())
      ->method('getKey')
      ->with('label')
      ->willReturn('label');

    $this->entityManager->expects($this->atLeastOnce())
      ->method('getDefinition')
      ->with($this->entityTypeId)
      ->willReturn($entity_type);

    $alternative_signs = [$this->randomMachineName(), $this->randomMachineName(), $this->randomMachineName()];
    $currency_code = $this->randomMachineName();
    $currency_number = mt_rand();
    $exchange_rates = [
      $this->randomMachineName() => mt_rand(),
      $this->randomMachineName() => mt_rand(),
      $this->randomMachineName() => mt_rand(),
    ];
    $rounding_step = mt_rand();
    $sign = $this->randomMachineName();
    $subunits = mt_rand();
    $status = TRUE;
    $label = $this->randomMachineName();

    $usage_start_a = mt_rand();
    $usage_end_a = mt_rand();
    $usage_country_code_a = $this->randomMachineName();
    $usage_start_b = mt_rand();
    $usage_end_b = mt_rand();
    $usage_country_code_b = $this->randomMachineName();
    $usage_start_c = mt_rand();
    $usage_end_c = mt_rand();
    $usage_country_code_c = $this->randomMachineName();
    /** @var \Drupal\currency\Usage[] $usages */
    $usages = [
      (new Usage())->setStart($usage_start_a)->setEnd($usage_end_a)->setCountryCode($usage_country_code_a),
      (new Usage())->setStart($usage_start_b)->setEnd($usage_end_b)->setCountryCode($usage_country_code_b),
      (new Usage())->setStart($usage_start_c)->setEnd($usage_end_c)->setCountryCode($usage_country_code_c),
    ];

    $expected_array['alternativeSigns'] = $alternative_signs;
    $expected_array['currencyCode'] = $currency_code;
    $expected_array['currencyNumber'] = $currency_number;
    $expected_array['label'] = $label;
    $expected_array['roundingStep'] = $rounding_step;
    $expected_array['sign'] = $sign;
    $expected_array['subunits'] = $subunits;
    $expected_array['status'] = $status;
    $expected_array['usages'] = [
      [
        'start' => $usage_start_a,
        'end' => $usage_end_a,
        'countryCode' => $usage_country_code_a,
      ],
      [
        'start' => $usage_start_b,
        'end' => $usage_end_b,
        'countryCode' => $usage_country_code_b,
      ],
      [
        'start' => $usage_start_c,
        'end' => $usage_end_c,
        'countryCode' => $usage_country_code_c,
      ],
    ];

    $this->currency->setAlternativeSigns($expected_array['alternativeSigns']);
    $this->currency->setLabel($label);
    $this->currency->setUsages($usages);
    $this->currency->setSubunits($subunits);
    $this->currency->setRoundingStep($rounding_step);
    $this->currency->setSign($sign);
    $this->currency->setStatus($status);
    $this->currency->setCurrencyCode($currency_code);
    $this->currency->setCurrencyNumber($currency_number);

    $array = $this->currency->toArray();
    $this->assertArrayHasKey('uuid', $array);
    unset($array['uuid']);
    $this->assertEquals($expected_array, $array);
  }

}
