<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\Currency\CurrencyDeleteFormTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\Currency {

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\StringTranslation\TranslatableMarkup;
  use Drupal\Core\Url;
  use Drupal\currency\Entity\Currency\CurrencyDeleteForm;
  use Drupal\currency\Entity\CurrencyInterface;
  use Drupal\Tests\UnitTestCase;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * @coversDefaultClass \Drupal\currency\Entity\Currency\CurrencyDeleteForm
   *
   * @group Currency
   */
  class CurrencyDeleteFormTest extends UnitTestCase {

    /**
     * The currency.
     *
     * @var \Drupal\currency\Entity\CurrencyInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $currency;

    /**
     * The string translator.
     *
     * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stringTranslation;

    /**
     * The class under test.
     *
     * @var \Drupal\currency\Entity\Currency\CurrencyDeleteForm
     */
    protected $sut;

    /**
     * {@inheritdoc}
     */
    public function setUp() {
      $this->currency = $this->getMock(CurrencyInterface::class);

      $this->stringTranslation = $this->getStringTranslationStub();

      $this->sut = new CurrencyDeleteForm($this->stringTranslation);
      $this->sut->setEntity($this->currency);
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

      $sut = CurrencyDeleteForm::create($container);
      $this->assertInstanceOf(CurrencyDeleteForm::class, $sut);
    }

    /**
     * @covers ::getQuestion
     */
    function testGetQuestion() {
      $this->assertInstanceOf(TranslatableMarkup::class, $this->sut->getQuestion());
    }

    /**
     * @covers ::getConfirmText
     */
    function testGetConfirmText() {
      $this->assertInstanceOf(TranslatableMarkup::class, $this->sut->getConfirmText());
    }

    /**
     * @covers ::getCancelUrl
     */
    function testGetCancelUrl() {
      $url = $this->sut->getCancelUrl();
      $this->assertInstanceOf(Url::class, $url);
      $this->assertSame('entity.currency.collection', $url->getRouteName());
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

      $this->sut->submitForm($form, $form_state);
    }

  }

}

namespace {

if (!function_exists('drupal_set_message')) {
  function drupal_set_message() {}
}

}
