<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Controller\CurrencyLocaleImportFormUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Controller {

use Drupal\currency\Controller\CurrencyLocaleImportForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Controller\CurrencyLocaleImportForm
 *
 * @group Currency
 */
class CurrencyLocaleImportFormUnitTest extends UnitTestCase {

  /**
   * The controller under test.
   *
   * @var \Drupal\currency\Controller\CurrencyLocaleImportForm
   */
  protected $controller;

  /**
   * The config importer.
   *
   * @var \Drupal\currency\ConfigImporterInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $configImporter;

  /**
   * The form helper.
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
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->configImporter = $this->getMock('\Drupal\currency\ConfigImporterInterface');

    $this->formHelper = $this->getMock('\Drupal\currency\FormHelperInterface');

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->controller = new CurrencyLocaleImportForm($this->stringTranslation, $this->configImporter, $this->formHelper);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $map = [
      ['currency.config_importer', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->configImporter],
      ['currency.form_helper', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->formHelper],
      ['string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation],
    ];
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = CurrencyLocaleImportForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Controller\CurrencyLocaleImportForm', $form);
  }

  /**
   * @covers ::getFormId
   */
  public function testGetFormId() {
    $this->assertInternalType('string', $this->controller->getFormId());
  }

  /**
   * @covers ::buildForm
   */
  public function testBuildFormWithoutImportableCurrencies() {
    $this->configImporter->expects($this->once())
      ->method('getImportableCurrencyLocales')
      ->willReturn([]);

    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $form = $this->controller->buildForm([], $form_state);

    // There should be one element and it must not be the currency selector or a
    // group of actions.
    $this->assertCount(1, $form);
    $this->assertArrayNotHasKey('actions', $form);
    $this->assertArrayNotHasKey('locale', $form);
  }

  /**
   * @covers ::buildForm
   */
  public function testBuildFormWithImportableCurrencyLocales() {
    $currency_locale_a = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');
    $currency_locale_b = $this->getMock('\Drupal\currency\Entity\CurrencyInterface');

    $this->configImporter->expects($this->once())
      ->method('getImportableCurrencyLocales')
      ->willReturn([$currency_locale_a, $currency_locale_b]);

    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $form = $this->controller->buildForm([], $form_state);

    // There should a currency selector and a group of actions.
    $this->assertArrayHasKey('locale', $form);
    $this->assertArrayHasKey('actions', $form);
    $this->assertArrayHasKey('import', $form['actions']);
    $this->assertArrayHasKey('import_edit', $form['actions']);
  }

  /**
   * @covers ::submitForm
   */
  public function testSubmitForm() {
    $locale = $this->randomMachineName();

    $currency_locale = $this->getMock('\Drupal\currency\Entity\CurrencyLocaleInterface');

    $this->configImporter->expects($this->once())
      ->method('importCurrencyLocale')
      ->with($locale)
      ->willReturn($currency_locale);

    $form = [];
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->atLeastOnce())
      ->method('getValues')
      ->willReturn([
        'locale' => $locale,
      ]);
    $form_state->expects($this->atLeastOnce())
      ->method('setRedirectUrl');

    $this->controller->submitForm($form, $form_state);
  }

}

}

namespace {

  if (!function_exists('drupal_set_message')) {
    function drupal_set_message() {}
  }

}
