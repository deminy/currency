<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Plugin\Filter\CurrencyLocalizeUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Plugin\Filter;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\InputInterface;
use Drupal\currency\Plugin\Filter\CurrencyLocalize;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Plugin\Filter\CurrencyLocalize
 *
 * @group Currency
 */
class CurrencyLocalizeUnitTest extends UnitTestCase {

  /**
   * The currency storage used for testing.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The filter under test.
   *
   * @var \Drupal\currency\Plugin\Filter\CurrencyLocalize
   */
  protected $filter;

  /**
   * The input parser used for testing.
   *
   * @var \Drupal\currency\InputInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $input;

  /**
   * The plugin definiton.
   *
   * @var mixed[]
   */
  protected $pluginDefinition;

  /**
   * The string translator.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $configuration = [];
    $plugin_id = $this->randomMachineName();
    $this->pluginDefinition = [
      'cache' => TRUE,
      'provider' => $this->randomMachineName(),
    ];

    $this->currencyStorage = $this->getMock(EntityStorageInterface::class);

    $this->input = $this->getMock(InputInterface::class);

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->filter = new CurrencyLocalize($configuration, $plugin_id, $this->pluginDefinition, $this->stringTranslation, $this->currencyStorage, $this->input);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $entity_manager = $this->getMock(EntityManagerInterface::class);
    $entity_manager->expects($this->atLeastOnce())
      ->method('getStorage')
      ->with('currency')
      ->willReturn($this->currencyStorage);

    $container = $this->getMock(ContainerInterface::class);
    $map = [
      ['entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager],
      ['currency.input', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->input],
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
    ];
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $filter = CurrencyLocalize::create($container, [], '', $this->pluginDefinition);
    $this->assertInstanceOf(CurrencyLocalize::class, $filter);
  }

  /**
   * @covers ::process
   * @covers ::processCallback
   */
  function testProcess() {
    $map = [
      ['100', TRUE, LanguageInterface::TYPE_CONTENT, '€100.00'],
      ['100.7654', TRUE, LanguageInterface::TYPE_CONTENT, '€100.77'],
      ['1.99', TRUE, LanguageInterface::TYPE_CONTENT, '€1.99'],
      ['2.99', TRUE, LanguageInterface::TYPE_CONTENT, '€2.99'],
    ];
    $currency = $this->getMock(CurrencyInterface::class);
    $currency->expects($this->any())
      ->method('formatAmount')
      ->will($this->returnValueMap($map));

    $this->currencyStorage->expects($this->any())
      ->method('load')
      ->with('EUR')
      ->will($this->returnValue($currency));

    $this->input->expects($this->any())
      ->method('parseAmount')
      ->will($this->returnArgument(0));

    $langcode = $this->randomMachineName(2);
    $cache = TRUE;
    $cache_id = $this->randomMachineName();

    $tokens_valid = [
      '[currency-localize:EUR:100]' => '€100.00',
      '[currency-localize:EUR:100.7654]' => '€100.77',
      '[currency-localize:EUR:1.99]' => '€1.99',
      '[currency-localize:EUR:2.99]' => '€2.99',
    ];
    $tokens_invalid = [
      // Missing arguments.
      '[currency-localize]',
      '[currency-localize:]',
      '[currency-localize::]',
      '[currency-localize:EUR]',
      // Invalid currency code.
      '[currency-localize:123:456]',
      // Invalid currency code and missing argument.
      '[currency-localize:123]',
    ];

    foreach ($tokens_valid as $token => $replacement) {
      $this->assertSame($replacement, $this->filter->process($token, $langcode, $cache, $cache_id));
    }
    foreach ($tokens_invalid as $token) {
      $this->assertSame($token, $this->filter->process($token, $langcode, $cache, $cache_id));
    }
  }

  /**
   * @covers ::tips
   */
  public function testTips() {
    $this->assertInternalType('string', $this->filter->tips());
  }
}
