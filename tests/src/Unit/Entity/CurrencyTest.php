<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\CurrencyTest.
 */

namespace Drupal\Tests\currency\Unit\Entity;

use Commercie\Currency\Usage;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\currency\Entity\Currency;
use Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterInterface;
use Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Entity\Currency
 *
 * @group Currency
 */
class CurrencyTest extends UnitTestCase {

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
   * The class under test.
   *
   * @var \Drupal\currency\Entity\Currency
   */
  protected $sut;

  /**
   * {@inheritdoc}
   */
  function setUp() {
    $this->entityTypeId = $this->randomMachineName();

    $this->currencyAmountFormatterManager = $this->getMock(AmountFormatterManagerInterface::class);

    $this->entityManager = $this->getMock(EntityManagerInterface::class);

    $this->sut = new Currency([], $this->entityTypeId);
    $this->sut->setCurrencyAmountFormatterManager($this->currencyAmountFormatterManager);
    $this->sut->setEntityManager($this->entityManager);
  }

  /**
   * @covers ::__construct
   */
  public function testConstruct() {
    $this->sut = new Currency([], $this->entityTypeId);
  }

  /**
   * @covers ::setCurrencyAmountFormatterManager
   * @covers ::getCurrencyAmountFormatterManager
   */
  public function testGetCurrencyAmountFormatterManager() {
    $method = new \ReflectionMethod($this->sut, 'getCurrencyAmountFormatterManager');
    $method->setAccessible(TRUE);

    $this->assertSame($this->sut, $this->sut->setCurrencyAmountFormatterManager($this->currencyAmountFormatterManager));
    $this->assertSame($this->currencyAmountFormatterManager, $method->invoke($this->sut));
  }

  /**
   * @covers ::setEntityManager
   * @covers ::entityManager
   */
  public function testEntityManager() {
    $method = new \ReflectionMethod($this->sut, 'entityManager');
    $method->setAccessible(TRUE);

    $this->assertSame($this->sut, $this->sut->setEntityManager($this->entityManager));
    $this->assertSame($this->entityManager, $method->invoke($this->sut));
  }

  /**
   * @covers ::getRoundingStep
   * @covers ::setRoundingStep
   */
  function testGetRoundingStep() {
    $rounding_step = mt_rand();

    $this->assertSame($this->sut, $this->sut->setRoundingStep($rounding_step));
    $this->assertSame($rounding_step, $this->sut->getRoundingStep());
  }

  /**
   * @covers ::getRoundingStep
   */
  function testGetRoundingStepBySubunits() {
    $subunits = 5;
    $rounding_step = '0.200000';

    $this->sut->setSubunits($subunits);

    $this->assertSame($rounding_step, $this->sut->getRoundingStep());
  }

  /**
   * @covers ::getRoundingStep
   */
  function testGetRoundingStepUnavailable() {
    $this->assertNull($this->sut->getRoundingStep());
  }

  /**
   * @covers ::formatAmount
   * @covers ::getCurrencyAmountFormatterManager
   *
   * @depends testGetRoundingStep
   *
   * @dataProvider providerTestFormatAmount
   */
  function testFormatAmount($expected, $amount, $amount_with_currency_precision_applied) {
    $amount_formatter = $this->getMock(AmountFormatterInterface::class);
    $amount_formatter->expects($this->atLeastOnce())
      ->method('formatAmount')
      ->with($this->sut, $amount_with_currency_precision_applied)
      ->willReturn($expected);

    $this->currencyAmountFormatterManager->expects($this->atLeastOnce())
      ->method('getDefaultPlugin')
      ->willReturn($amount_formatter);

    $this->sut->setCurrencyCode('BLA');
    $this->sut->setSubunits(100);

    $this->assertSame($expected, $this->sut->formatAmount($amount, $amount !== $amount_with_currency_precision_applied));
  }

  /**
   * Provides data to self::testFormatAmount().
   */
  public function providerTestFormatAmount() {
    return [
      ['BLA 12,345.68', '12345.6789', '12345.68'],
      ['BLA 12,345.6789', '12345.6789', '12345.6789'],
    ];
  }

  /**
   * @covers ::getDecimals
   */
  function testGetDecimals() {
    foreach ([1, 2, 3] as $decimals) {
      $this->sut->setSubunits(pow(10, $decimals));
      $this->assertSame($decimals, $this->sut->getDecimals());
    }
  }

  /**
   * @covers ::isObsolete
   */
  function testIsObsolete() {
    // A currency without usage data.
    $this->assertFalse($this->sut->isObsolete());

    // A currency that is no longer being used.
    $usage = new Usage();
    $usage->setStart('1813-01-01')
      ->setEnd('2002-02-28');
    $this->sut->setUsages([$usage]);
    $this->assertTrue($this->sut->isObsolete());

    // A currency that will become obsolete next year.
    $usage = new Usage();
    $usage->setStart('1813-01-01')
      ->setEnd(date('o') + 1 . '-02-28');
    $this->sut->setUsages([$usage]);
    $this->assertFalse($this->sut->isObsolete());
  }

  /**
   * @covers ::getAlternativeSigns
   * @covers ::setAlternativeSigns
   */
  function testGetAlternativeSigns() {
    $alternative_signs = ['A', 'B'];
    $this->assertSame($this->sut, $this->sut->setAlternativeSigns($alternative_signs));
    $this->assertSame($alternative_signs, $this->sut->getAlternativeSigns());
  }

  /**
   * @covers ::id
   * @covers ::setCurrencyCode
   */
  function testId() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame($this->sut, $this->sut->setCurrencyCode($currency_code));
    $this->assertSame($currency_code, $this->sut->id());
  }

  /**
   * @covers ::getCurrencyCode
   * @covers ::setCurrencyCode
   */
  function testGetCurrencyCode() {
    $currency_code = $this->randomMachineName(3);
    $this->assertSame($this->sut, $this->sut->setCurrencyCode($currency_code));
    $this->assertSame($currency_code, $this->sut->getCurrencyCode());
  }

  /**
   * @covers ::getCurrencyNumber
   * @covers ::setCurrencyNumber
   */
  function testGetCurrencyNumber() {
    $currency_number = '000';
    $this->assertSame($this->sut, $this->sut->setCurrencyNumber($currency_number));
    $this->assertSame($currency_number, $this->sut->getCurrencyNumber());
  }

  /**
   * @covers ::label
   * @covers ::setLabel
   */
  function testLabel() {
    $entity_type = $this->getMock(EntityTypeInterface::class);
    $entity_type->expects($this->atLeastOnce())
      ->method('getKey')
      ->with('label')
      ->willReturn('label');

    $this->entityManager->expects($this->atLeastOnce())
      ->method('getDefinition')
      ->with($this->entityTypeId)
      ->willReturn($entity_type);

    $label = $this->randomMachineName();
    $this->assertSame($this->sut, $this->sut->setLabel($label));
    $this->assertSame($label, $this->sut->label());
  }

  /**
   * @covers ::getSign
   * @covers ::setSign
   */
  function testGetSign() {
    $sign = $this->randomMachineName(1);
    $this->assertSame($this->sut, $this->sut->setSign($sign));
    $this->assertSame($sign, $this->sut->getSign());
  }

  /**
   * @covers ::setSubunits
   * @covers ::getSubunits
   */
  function testGetSubunits() {
    $subunits = 73;
    $this->assertSame($this->sut, $this->sut->setSubunits($subunits));
    $this->assertSame($subunits, $this->sut->getSubunits());
  }

  /**
   * @covers ::setUsages
   * @covers ::getUsages
   */
  function testGetUsage() {
    $usage = new Usage();
    $usage->setStart('1813-01-01')
    ->setEnd(date('o') + 1 . '-02-28');
    $this->assertSame($this->sut, $this->sut->setUsages([$usage]));
    $this->assertSame([$usage], $this->sut->getUsages());
  }

  /**
   * @covers ::toArray
   */
  public function testToArray() {
    $entity_type = $this->getMock(EntityTypeInterface::class);
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

    $this->sut->setAlternativeSigns($expected_array['alternativeSigns']);
    $this->sut->setLabel($label);
    $this->sut->setUsages($usages);
    $this->sut->setSubunits($subunits);
    $this->sut->setRoundingStep($rounding_step);
    $this->sut->setSign($sign);
    $this->sut->setStatus($status);
    $this->sut->setCurrencyCode($currency_code);
    $this->sut->setCurrencyNumber($currency_number);

    $array = $this->sut->toArray();
    $this->assertArrayHasKey('uuid', $array);
    unset($array['uuid']);
    $this->assertEquals($expected_array, $array);
  }

}
