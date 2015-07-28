<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\FixedRatesFormUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Controller {

  use Drupal\currency\Controller\FixedRatesForm;
  use Drupal\Tests\UnitTestCase;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
 * @coversDefaultClass \Drupal\currency\Controller\FixedRatesForm
 *
 * @group Currency
 */
class FixedRatesFormUnitTest extends UnitTestCase {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configFactory;

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\FixedRatesForm
   */
  protected $controller;

  /**
   * The currency exchange rate provider manager.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The form helper
   *
   * @var \Drupal\currency\FormHelperInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $formHelper;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->configFactory = $this->getMock('\Drupal\Core\Config\ConfigFactoryInterface');

    $this->currencyExchangeRateProviderManager = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface');

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->formHelper = $this->getMock('\Drupal\currency\FormHelperInterface');

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->controller = new FixedRatesForm($this->configFactory, $this->stringTranslation, $this->currencyStorage, $this->currencyExchangeRateProviderManager, $this->formHelper);
  }

  /**
   * @covers ::create
   * @covers ::__construct
   */
  function testCreate() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency')
      ->will($this->returnValue($this->currencyStorage));

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = array(
      array('config.factory', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->configFactory),
      array('currency.form_helper', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->formHelper),
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('plugin.manager.currency.exchange_rate_provider', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->currencyExchangeRateProviderManager),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = FixedRatesForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\FixedRatesForm', $form);
  }

  /**
   * @covers ::getFormId
   */
  public function testGetFormId() {
    $this->assertSame('currency_exchange_rate_provider_fixed_rates', $this->controller->getFormId());
  }

  /**
   * @covers ::buildForm
   *
   * @dataProvider providerTestBuildForm
   */
  public function testBuildForm($rate_rate) {
    $currency_code_from = $this->randomMachineName();
    $currency_code_to = $this->randomMachineName();

    $rate = NULL;
    if (!is_null($rate_rate)) {
      $rate = $this->getMock('\Drupal\currency\ExchangeRateInterface');
      $rate->expects($this->once())
        ->method('getRate')
        ->willReturn($rate_rate);
    }

    $plugin = $this->getMock('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderInterface');
    $plugin->expects($this->once())
      ->method('load')
      ->with($currency_code_from, $currency_code_to)
      ->willReturn($rate);

    $this->currencyExchangeRateProviderManager->expects($this->once())
      ->method('createInstance')
      ->with('currency_fixed_rates')
      ->willReturn($plugin);

    $currency_options = array(
      'XXX' => $this->randomMachineName(),
      $this->randomMachineName() => $this->randomMachineName(),
    );

    $this->formHelper->expects($this->once())
      ->method('getCurrencyOptions')
      ->willReturn($currency_options);

    unset($currency_options['XXX']);
    $expected_build['currency_code_from'] = array(
      '#default_value' => $currency_code_from,
      '#disabled' => !is_null($rate_rate),
      '#empty_value' => '',
      '#options' => $currency_options,
      '#required' => TRUE,
      '#title' => 'Source currency',
      '#type' => 'select',
    );
    $expected_build['currency_code_to'] = array(
      '#default_value' => $currency_code_to,
      '#disabled' => !is_null($rate_rate),
      '#empty_value' => '',
      '#options' => $currency_options,
      '#required' => TRUE,
      '#title' => 'Destination currency',
      '#type' => 'select',
    );
    $expected_build['rate'] = array(
      '#limit_currency_codes' => array($currency_code_to),
      '#default_value' => array(
        'amount' => $rate_rate,
        'currency_code' => $currency_code_to,
      ),
      '#required' => TRUE,
      '#title' => 'Exchange rate',
      '#type' => 'currency_amount',
    );
    $expected_build['actions'] = array(
      '#type' => 'actions',
    );
    $expected_build['actions']['save'] = array(
      '#button_type' => 'primary',
      '#name' => 'save',
      '#type' => 'submit',
      '#value' => 'Save',
    );
    if (!is_null($rate_rate)) {
      $expected_build['actions']['delete'] = array(
        '#button_type' => 'danger',
        '#limit_validation_errors' => array(array('currency_code_from'), array('currency_code_to')),
        '#name' => 'delete',
        '#type' => 'submit',
        '#value' => 'Delete',
      );
    }

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $build = $this->controller->buildForm($form, $form_state, $currency_code_from, $currency_code_to);
    $this->assertSame($expected_build, $build);
  }

  /**
   * Provides data to self::testBuildForm().
   */
  public function providerTestBuildForm() {
    return array(
      array(NULL),
      array(mt_rand()),
    );
  }

  /**
   * @covers ::validateForm
   */
  public function testValidateForm() {
    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $this->controller->validateForm($form, $form_state);
  }

  /**
   * @covers ::submitForm
   */
  public function testSubmitFormWithSave() {
    $currency_code_from = $this->randomMachineName();
    $currency_code_to = $this->randomMachineName();
    $rate = mt_rand();

    $values = [
      'currency_code_from' => $currency_code_from,
      'currency_code_to' => $currency_code_to,
      'rate' => [
        'amount' => $rate,
      ],
    ];

    $form = [
      'actions' => [
        'save' => [
          '#name' => 'save',
          '#foo' => $this->randomMachineName(),
        ],
        'delete' => [
          '#name' => 'delete',
          '#foo' => $this->randomMachineName(),
        ],
      ],
    ];

    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->atLeastOnce())
      ->method('getTriggeringElement')
      ->willReturn($form['actions']['save']);
    $form_state->expects($this->atLeastOnce())
      ->method('getValues')
      ->willReturn($values);
    $form_state->expects($this->atLeastOnce())
      ->method('setRedirect')
      ->with('currency.exchange_rate_provider.fixed_rates.overview');

    $exchange_rate_provider = $this->getMockBuilder('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates')
      ->disableOriginalConstructor()
      ->getMock();
    $exchange_rate_provider->expects($this->once())
      ->method('save')
      ->with($currency_code_from, $currency_code_to, $rate);

    $this->currencyExchangeRateProviderManager->expects($this->once())
      ->method('createInstance')
      ->with('currency_fixed_rates')
      ->willReturn($exchange_rate_provider);

    $currency_from = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_to = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

    $map = [
      [$currency_code_from, $currency_from],
      [$currency_code_to, $currency_to],
    ];
    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('load')
      ->willReturnMap($map);

    $this->controller->submitForm($form, $form_state);
  }

  /**
   * @covers ::submitForm
   */
  public function testSubmitFormWitDelete() {
    $currency_code_from = $this->randomMachineName();
    $currency_code_to = $this->randomMachineName();

    $values = [
      'currency_code_from' => $currency_code_from,
      'currency_code_to' => $currency_code_to,
    ];

    $form = [
      'actions' => [
        'save' => [
          '#name' => 'save',
          '#foo' => $this->randomMachineName(),
        ],
        'delete' => [
          '#name' => 'delete',
          '#foo' => $this->randomMachineName(),
        ],
      ],
    ];

    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->atLeastOnce())
      ->method('getTriggeringElement')
      ->willReturn($form['actions']['delete']);
    $form_state->expects($this->atLeastOnce())
      ->method('getValues')
      ->willReturn($values);
    $form_state->expects($this->atLeastOnce())
      ->method('setRedirect')
      ->with('currency.exchange_rate_provider.fixed_rates.overview');

    $exchange_rate_provider = $this->getMockBuilder('\Drupal\currency\Plugin\Currency\ExchangeRateProvider\FixedRates')
      ->disableOriginalConstructor()
      ->getMock();
    $exchange_rate_provider->expects($this->once())
      ->method('delete')
      ->with($currency_code_from, $currency_code_to);

    $this->currencyExchangeRateProviderManager->expects($this->once())
      ->method('createInstance')
      ->with('currency_fixed_rates')
      ->willReturn($exchange_rate_provider);

    $currency_from = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_to = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

    $map = [
      [$currency_code_from, $currency_from],
      [$currency_code_to, $currency_to],
    ];
    $this->currencyStorage->expects($this->atLeastOnce())
      ->method('load')
      ->willReturnMap($map);

    $this->controller->submitForm($form, $form_state);
  }

}

}

namespace {

  if (!function_exists('drupal_set_message')) {
    function drupal_set_message() {}
  }

}
