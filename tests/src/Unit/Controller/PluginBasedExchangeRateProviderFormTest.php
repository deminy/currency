<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\PluginBasedExchangeRateProviderFormTest.
 */

namespace Drupal\Tests\currency\Unit\Controller {

  use Drupal\Core\Form\FormState;
  use Drupal\currency\Controller\PluginBasedExchangeRateProviderForm;
  use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface;
  use Drupal\currency\PluginBasedExchangeRateProvider;
  use Drupal\Tests\UnitTestCase;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * @coversDefaultClass \Drupal\currency\Controller\PluginBasedExchangeRateProviderForm
   *
   * @group Currency
   */
  class PluginBasedExchangeRateProviderFormTest extends UnitTestCase {

    /**
     * The currency exchange rate provider manager.
     *
     * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currencyExchangeRateProviderManager;

    /**
     * The currency exchange rate provider.
     *
     * @var \Drupal\currency\PluginBasedExchangeRateProvider|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $exchangeRateProvider;

    /**
     * The string translator.
     *
     * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringTranslation;

    /**
     * The class under test.
     *
     * @var \Drupal\currency\Controller\PluginBasedExchangeRateProviderForm
     */
    protected $sut;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
      $this->currencyExchangeRateProviderManager = $this->getMock(ExchangeRateProviderManagerInterface::class);

      $this->exchangeRateProvider = $this->getMockBuilder(PluginBasedExchangeRateProvider::class)
        ->disableOriginalConstructor()
        ->getMock();

      $this->stringTranslation = $this->getStringTranslationStub();

      $this->sut = new PluginBasedExchangeRateProviderForm($this->stringTranslation, $this->exchangeRateProvider, $this->currencyExchangeRateProviderManager);
    }

    /**
     * @covers ::create
     * @covers ::__construct
     */
    function testCreate() {
      $container = $this->getMock(ContainerInterface::class);
      $map = [
        ['plugin.manager.currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->currencyExchangeRateProviderManager],
        ['currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->exchangeRateProvider],
        ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
      ];
      $container->expects($this->any())
        ->method('get')
        ->willReturnMap($map);

      $sut = PluginBasedExchangeRateProviderForm::create($container);
      $this->assertInstanceOf(PluginBasedExchangeRateProviderForm::class, $sut);
    }

    /**
     * @covers ::getFormId
     */
    public function testGetFormId() {
      $this->assertSame('currency_exchange_rate_provider', $this->sut->getFormId());
    }

    /**
     * @covers ::buildForm
     */
    public function testBuildForm() {
      $plugin_id_a = $this->randomMachineName();
      $plugin_id_b = $this->randomMachineName();
      $plugin_id_c = $this->randomMachineName();

      $plugin_definitions = [
        $plugin_id_a => [
          'description' => NULL,
          'label' => $this->randomMachineName(),
          'operations' => [],
        ],
        $plugin_id_b => [
          'description' => $this->randomMachineName(),
          'label' => $this->randomMachineName(),
          'operations' => [],
        ],
        $plugin_id_c => [
          'description' => $this->randomMachineName(),
          'label' => $this->randomMachineName(),
          'operations' => [
            [
              'href' => $this->randomMachineName(),
              'title' => $this->randomMachineName(),
            ],
          ],
        ],
      ];

      $configuration = [
        $plugin_id_a => TRUE,
        $plugin_id_b => FALSE,
        $plugin_id_c => TRUE,
      ];

      $this->exchangeRateProvider->expects($this->atLeastOnce())
        ->method('loadConfiguration')
        ->willReturn($configuration);

      $this->currencyExchangeRateProviderManager->expects($this->atLeastOnce())
        ->method('getDefinitions')
        ->willReturn($plugin_definitions);

      $form = [];
      $form_state = new FormState();

      $build = $this->sut->buildForm($form, $form_state);

      foreach ([$plugin_id_a, $plugin_id_b, $plugin_id_c] as $weight => $plugin_id) {
        $this->assertInternalType('array', $build['exchange_rate_providers'][$plugin_id]['weight']);
        $this->assertInternalType('array', $build['exchange_rate_providers'][$plugin_id]['label']);
        $this->assertSame($plugin_definitions[$plugin_id]['label'], $build['exchange_rate_providers'][$plugin_id]['label']['#markup']);
        $this->assertInternalType('array', $build['exchange_rate_providers'][$plugin_id]['weight']);
        $this->assertSame($weight + 1, $build['exchange_rate_providers'][$plugin_id]['weight']['#default_value']);
      }
      $this->assertInternalType('array', $build['actions']);
    }

    /**
     * @covers ::submitForm
     */
    public function testSubmitForm() {
      $plugin_id_a = $this->randomMachineName();
      $plugin_enabled_a = (bool) mt_rand(0, 1);
      $plugin_id_b = $this->randomMachineName();
      $plugin_enabled_b = (bool) mt_rand(0, 1);
      $plugin_id_c = $this->randomMachineName();
      $plugin_enabled_c = (bool) mt_rand(0, 1);

      $configuration = [
        $plugin_id_c => $plugin_enabled_c,
        $plugin_id_a => $plugin_enabled_a,
        $plugin_id_b => $plugin_enabled_b,
      ];

      $values = [
        'exchange_rate_providers' => [
          $plugin_id_c => [
            'enabled' => $plugin_enabled_c,
            'weight' => mt_rand(9, 99),
          ],
          $plugin_id_a => [
            'enabled' => $plugin_enabled_a,
            'weight' => mt_rand(999, 9999),
          ],
          $plugin_id_b => [
            'enabled' => $plugin_enabled_b,
            'weight' => mt_rand(99999, 999999),
          ],
        ],
      ];

      $form = [];
      $form_state = new FormState();
      $form_state->setValues($values);

      $this->exchangeRateProvider->expects($this->once())
        ->method('saveConfiguration')
        ->with(new \PHPUnit_Framework_Constraint_IsIdentical($configuration));

      $this->sut->submitForm($form, $form_state);
    }

  }

}

namespace {

  if (!function_exists('drupal_set_message')) {
    function drupal_set_message() {
    }
  }

}
