<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\Currency\CurrencyFormUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\Currency {

  use Drupal\Core\Url;
  use Drupal\currency\Entity\Currency\CurrencyForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Entity\Currency\CurrencyForm
 *
 * @group Currency
 */
class CurrencyFormUnitTest extends UnitTestCase {

  /**
   * The currency.
   *
   * @var \Drupal\currency\Entity\CurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currency;

  /**
   * The currency storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyStorage;

  /**
   * The Currency input parser.
   *
   * @var \Drupal\currency\InputInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $inputParser;

  /**
   * The link generator.
   *
   * @var \Drupal\Core\Utility\LinkGeneratorInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $linkGenerator;

  /**
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * The form under test.
   *
   * @var \Drupal\currency\Entity\Currency\CurrencyForm
   */
  protected $form;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();

    $this->currencyStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->linkGenerator = $this->getMock('\Drupal\Core\Utility\LinkGeneratorInterface');

    $this->inputParser = $this->getMock('\Drupal\currency\InputInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $this->form = new CurrencyForm($this->stringTranslation, $this->linkGenerator, $this->currencyStorage, $this->inputParser);
    $this->form->setEntity($this->currency);
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
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('currency.input', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->inputParser),
      array('link_generator', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->linkGenerator),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = CurrencyForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Entity\Currency\CurrencyForm', $form);
  }

  /**
   * @covers ::copyFormValuesToEntity
   */
  public function testCopyFormValuesToEntity() {
    $currency_code = $this->randomMachineName();
    $currency_number = $this->randomMachineName();
    $currency_label = $this->randomMachineName();
    $currency_sign = $this->randomMachineName();
    $currency_subunits = mt_rand();
    $currency_rounding_step = mt_rand();
    $currency_status = TRUE;

    $this->currency->expects($this->once())
      ->method('setCurrencyCode')
      ->with($currency_code);
    $this->currency->expects($this->once())
      ->method('setCurrencyNumber')
      ->with($currency_number);
    $this->currency->expects($this->once())
      ->method('setLabel')
      ->with($currency_label);
    $this->currency->expects($this->once())
      ->method('setSign')
      ->with($currency_sign);
    $this->currency->expects($this->once())
      ->method('setSubunits')
      ->with($currency_subunits);
    $this->currency->expects($this->once())
      ->method('setRoundingStep')
      ->with($currency_rounding_step);
    $this->currency->expects($this->once())
      ->method('setStatus')
      ->with($currency_status);

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->atLeastOnce())
      ->method('getValues')
      ->willReturn(array(
        'currency_code' => $currency_code,
        'currency_number' => $currency_number,
        'label' => $currency_label,
        'sign' => $currency_sign,
        'subunits' => $currency_subunits,
        'rounding_step' => $currency_rounding_step,
        'status' => $currency_status,
      ));

    $method = new \ReflectionMethod($this->form, 'copyFormValuesToEntity');
    $method->setAccessible(TRUE);

    $method->invokeArgs($this->form, array($this->currency, $form, $form_state));
  }

  /**
   * @covers ::form
   */
  public function testForm() {
    $currency_code = $this->randomMachineName();
    $currency_number = $this->randomMachineName();
    $currency_label = $this->randomMachineName();
    $currency_sign = $this->randomMachineName();
    $currency_subunits = mt_rand();
    $currency_rounding_step = mt_rand();
    $currency_status = TRUE;

    $this->currency->expects($this->any())
      ->method('getCurrencyCode')
      ->will($this->returnValue($currency_code));
    $this->currency->expects($this->any())
      ->method('getCurrencyNumber')
      ->will($this->returnValue($currency_number));
    $this->currency->expects($this->any())
      ->method('label')
      ->will($this->returnValue($currency_label));
    $this->currency->expects($this->any())
      ->method('getSign')
      ->will($this->returnValue($currency_sign));
    $this->currency->expects($this->any())
      ->method('getSubunits')
      ->will($this->returnValue($currency_subunits));
    $this->currency->expects($this->any())
      ->method('getRoundingStep')
      ->will($this->returnValue($currency_rounding_step));
    $this->currency->expects($this->any())
      ->method('status')
      ->will($this->returnValue($currency_status));

    $language = $this->getMockBuilder('\Drupal\Core\Language\Language')
      ->disableOriginalConstructor()
      ->getMock();

    $this->currency->expects($this->any())
      ->method('language')
      ->will($this->returnValue($language));

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $expected = array(
      'currency_code' => array(
        '#default_value' => $currency_code,
        '#disabled' => TRUE,
        '#element_validate' => array(array($this->form, 'validateCurrencyCode')),
        '#maxlength' => 3,
        '#pattern' => '[a-zA-Z]{3}',
        '#placeholder' => 'XXX',
        '#required' => TRUE,
        '#size' => 3,
        '#title' => 'ISO 4217 code',
        '#type' => 'textfield',
      ),
      'currency_number' => array(
        '#default_value' => $currency_number,
        '#element_validate' => array(array($this->form, 'validateCurrencyNumber')),
        '#maxlength' => 3,
        '#pattern' => '[\d]{3}',
        '#placeholder' => '999',
        '#size' => 3,
        '#title' => 'ISO 4217 number',
        '#type' => 'textfield',
      ),
      'status' => array(
        '#default_value' => $currency_status,
        '#title' => 'Enabled',
        '#type' => 'checkbox',
      ),
      'label' => array(
        '#default_value' => $currency_label,
        '#maxlength' => 255,
        '#required' => TRUE,
        '#title' => 'Name',
        '#type' => 'textfield',
      ),
      'sign' => array(
        '#currency_code' => $currency_code,
        '#default_value' => $currency_sign,
        '#title' => 'Sign',
        '#type' => 'currency_sign',
      ),
      'subunits' => array(
        '#default_value' => $currency_subunits,
        '#min' => 0,
        '#required' => TRUE,
        '#title' => 'Number of subunits',
        '#type' => 'number',
      ),
      'rounding_step' => array(
        '#default_value' => $currency_rounding_step,
        '#element_validate' => array(array($this->form, 'validateRoundingStep')),
        '#required' => TRUE,
        '#title' => 'Rounding step',
        '#type' => 'textfield',
      ),
      '#after_build' => ['::afterBuild'],
    );
    $build = $this->form->form($form, $form_state);
    unset($build['langcode']);
    unset($build['#process']);
    $this->assertSame($expected, $build);
  }

  /**
   * @covers ::save
   */
  public function testSave() {
    $this->currency->expects($this->once())
      ->method('save');

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->once())
      ->method('setRedirect')
      ->with('currency.currency.list');

    $this->form->save($form, $form_state);
  }

  /**
   * @covers ::validateCurrencyCode
   * @dataProvider providerTestValidateCurrencyCode
   */
  public function testValidateCurrencyCode($valid, $currency_code, $currency_is_new, $currency_code_exists = FALSE) {
    $element = array(
      '#value' => $currency_code,
    );
    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $this->currency->expects($this->any())
      ->method('isNew')
      ->will($this->returnValue($currency_is_new));

    if (!$valid) {
      $form_state->expects($this->once())
        ->method('setError')
        ->with($element, 'The currency code must be three letters.');
    }
    elseif ($currency_code_exists) {
      $loaded_currency_label = $this->randomMachineName();
      $loaded_currency_url = new Url($this->randomMachineName());

      $loaded_currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
        ->disableOriginalConstructor()
        ->getMock();
      $loaded_currency->expects($this->any())
        ->method('label')
        ->will($this->returnValue($loaded_currency_label));
      $loaded_currency->expects($this->atLeastOnce())
        ->method('urlInfo')
        ->willReturn($loaded_currency_url);

      $this->currencyStorage->expects($this->once())
        ->method('load')
        ->with($currency_code)
        ->will($this->returnValue($loaded_currency));

      $form_state->expects($this->once())
        ->method('setError')
        ->with($element, 'The currency code is already in use by !link.');

      $this->linkGenerator->expects($this->once())
        ->method('generate')
        ->with($loaded_currency_label, $loaded_currency_url);
    }
    else {
      $this->currencyStorage->expects($this->once())
        ->method('load')
        ->with($currency_code)
        ->will($this->returnValue(FALSE));
      $form_state->expects($this->never())
        ->method('setError');
      $form_state->expects($this->never())
        ->method('setErrorByName');
    }

    $this->form->validateCurrencyCode($element, $form_state, $form);
  }

  /**
   * Provides data to self::testValidateCurrencyCode()
   */
  public function providerTestValidateCurrencyCode() {
    return array(
      // All valid values and no re-use of the currency code.
      array(TRUE, 'EUR', TRUE),
      // An invalid currency code.
      array(FALSE, 'AB1', TRUE),
      // A valid currency code, but it is being re-used.
      array(TRUE, 'NLG', TRUE, TRUE),
    );
  }

  /**
   * @covers ::validateCurrencyNumber
   * @dataProvider providerTestValidateCurrencyNumber
   */
  public function testValidateCurrencyNumber($valid, $currency_number, $currency_is_new, $currency_number_exists = FALSE) {
    $element = array(
      '#value' => $currency_number,
    );
    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $this->currency->expects($this->any())
      ->method('isNew')
      ->will($this->returnValue($currency_is_new));

    if (!$valid) {
      $form_state->expects($this->once())
        ->method('setError')
        ->with($element, 'The currency number must be three digits.');
    }
    elseif ($currency_number_exists) {
      $loaded_currency_code = $this->randomMachineName();
      $loaded_currency_label = $this->randomMachineName();
      $loaded_currency_url = new Url($this->randomMachineName());

      $loaded_currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
        ->disableOriginalConstructor()
        ->getMock();
      $loaded_currency->expects($this->any())
        ->method('id')
        ->will($this->returnValue($loaded_currency_code));
      $loaded_currency->expects($this->any())
        ->method('label')
        ->will($this->returnValue($loaded_currency_label));
      $loaded_currency->expects($this->atLeastOnce())
        ->method('urlInfo')
        ->willReturn($loaded_currency_url);

      $this->currencyStorage->expects($this->once())
        ->method('loadByProperties')
        ->with(array(
          'currencyNumber' => $currency_number,
        ))
        ->will($this->returnValue(array($loaded_currency)));

      $form_state->expects($this->once())
        ->method('setError')
        ->with($element, 'The currency number is already in use by !link.');

      $this->linkGenerator->expects($this->once())
        ->method('generate')
        ->with($loaded_currency_label, $loaded_currency_url);
    }
    else {
      $this->currencyStorage->expects($this->once())
        ->method('loadByProperties')
        ->with(array(
          'currencyNumber' => $currency_number,
        ))
        ->will($this->returnValue(FALSE));
      $form_state->expects($this->never())
        ->method('setError');
      $form_state->expects($this->never())
        ->method('setErrorByName');
    }

    $this->form->validateCurrencyNumber($element, $form_state, $form);
  }

  /**
   * Provides data to self::testValidateCurrencyNumber()
   */
  public function providerTestValidateCurrencyNumber() {
    return array(
      // All valid values and no re-use of the currency code.
      array(TRUE, '123', TRUE),
      // An invalid currency code.
      array(FALSE, '12A', TRUE),
      // A valid currency code, but it is being re-used.
      array(TRUE, '123', TRUE, TRUE),
    );
  }

  /**
   * @covers ::validateRoundingStep
   * @dataProvider providerTestValidateRoundingStep
   */
  public function testValidateRoundingStep($valid, $input_value, $parsed_value) {
    $element = array(
      '#value' => $input_value,
    );
    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $this->inputParser->expects($this->once())
      ->method('parseAmount')
      ->with($input_value)
      ->will($this->returnValue($parsed_value));

    if (!$valid) {
      $form_state->expects($this->once())
        ->method('setError')
        ->with($element, 'The rounding step is not numeric.');
    }
    else {
      $form_state->expects($this->never())
        ->method('setError');
      $form_state->expects($this->never())
        ->method('setErrorByName');
    }
    $form_state->expects($this->once())
      ->method('setValueForElement')
      ->with($element, $parsed_value);

    $this->form->validateRoundingStep($element, $form_state, $form);
  }

  /**
   * Provides data to self::testValidateRoundingStep()
   */
  public function providerTestValidateRoundingStep() {
    return array(
      array(TRUE, '0.05', 0.05),
      array(FALSE, '0.0g', FALSE),
    );
  }

}

}

namespace {

if (!function_exists('drupal_set_message')) {
  function drupal_set_message() {}
}

}
