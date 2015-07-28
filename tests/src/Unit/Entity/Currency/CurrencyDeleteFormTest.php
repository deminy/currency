<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\Currency\CurrencyDeleteFormTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\Currency {

  use Drupal\Core\Form\FormStateInterface;
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
   * The string translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $stringTranslation;

  /**
   * The form under test.
   *
   * @var \Drupal\currency\Entity\Currency\CurrencyDeleteForm
   */
  protected $form;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->currency = $this->getMock(CurrencyInterface::class);

    $this->stringTranslation = $this->getStringTranslationStub();

    $this->form = new CurrencyDeleteForm($this->stringTranslation);
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
      ->will($this->returnValue($this->stringTranslation));

    $form = CurrencyDeleteForm::create($container);
    $this->assertInstanceOf(CurrencyDeleteForm::class, $form);
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
    $this->assertInternalType('string', $this->form->getConfirmText());
  }

  /**
   * @covers ::getCancelUrl
   */
  function testGetCancelUrl() {
    $url = $this->form->getCancelUrl();
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

    $this->form->submitForm($form, $form_state);
  }

}

}

namespace {

if (!function_exists('drupal_set_message')) {
  function drupal_set_message() {}
}

}
