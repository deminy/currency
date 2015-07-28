<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\CurrencyLocale\CurrencyLocaleDeleteFormTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\CurrencyLocale {

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Url;
  use Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleDeleteForm;
  use Drupal\currency\Entity\CurrencyLocaleInterface;
  use Drupal\Tests\UnitTestCase;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * @coversDefaultClass \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleDeleteForm
   *
   * @group Currency
   */
  class CurrencyLocaleDeleteFormTest extends UnitTestCase {

    /**
     * The currency.
     *
     * @var \Drupal\currency\Entity\CurrencyLocaleInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currency;

    /**
     * The string translation service.
     *
     * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringTranslation;

    /**
     * The form under test.
     *
     * @var \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleDeleteForm
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
      $this->currency = $this->getMock(CurrencyLocaleInterface::class);

      $this->stringTranslation = $this->getStringTranslationStub();

      $this->form = new CurrencyLocaleDeleteForm($this->stringTranslation);
      $this->form->setEntity($this->currency);
    }

    /**
     * @covers ::create
     * @covers ::__construct
     */
    function testCreate() {
      $container = $this->getMock(ContainerInterface::class);
      $container->expects($this->once())
        ->method('get')
        ->with('string_translation')
        ->willReturn($this->stringTranslation);

      $form = CurrencyLocaleDeleteForm::create($container);
      $this->assertInstanceOf(CurrencyLocaleDeleteForm::class, $form);
    }

    /**
     * @covers ::getQuestion
     */
    function testGetQuestion() {
      $this->assertInternalType('string', $this->form->getQuestion());
    }

    /**
     * @covers ::getConfirmText
     */
    function testGetConfirmText() {
      $string = 'Delete';

      $this->stringTranslation->expects($this->once())
        ->method('translate')
        ->with($string);

      $this->assertSame($string, $this->form->getConfirmText());
    }

    /**
     * @covers ::getCancelUrl
     */
    function testGetCancelUrl() {
      $url = $this->form->getCancelUrl();
      $this->assertInstanceOf(Url::class, $url);
      $this->assertSame('entity.currency_locale.collection', $url->getRouteName());
    }

    /**
     * @covers ::submitForm
     */
    function testSubmitForm() {
      $this->currency->expects($this->once())
        ->method('delete');

      $form = array();
      $form_state = $this->getMock(FormStateInterface::class);
      $form_state->expects($this->once())
        ->method('setRedirectUrl');

      $this->form->submitForm($form, $form_state);
    }

  }

}

namespace {

if (!function_exists('drupal_set_message')) {
  function drupal_set_message() {}
}

}
