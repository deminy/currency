<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\Currency\CurrencyDeleteFormUnitTest.
 */

namespace Drupal\currency\Tests\Entity\Currency {

use Drupal\currency\Entity\Currency\CurrencyDeleteForm;
use Drupal\Tests\UnitTestCase;

/**
 * @coversDefaultClass \Drupal\currency\Entity\Currency\CurrencyDeleteForm
 *
 * @group Currency
 */
class CurrencyDeleteFormUnitTest extends UnitTestCase {

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
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->currency = $this->getMockBuilder('\Drupal\currency\Entity\Currency')
      ->disableOriginalConstructor()
      ->getMock();

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $this->form = new CurrencyDeleteForm($this->stringTranslation);
    $this->form->setEntity($this->currency);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $container->expects($this->once())
      ->method('get')
      ->with('string_translation')
      ->will($this->returnValue($this->stringTranslation));

    $form = CurrencyDeleteForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Entity\Currency\CurrencyDeleteForm', $form);
  }

  /**
   * @covers ::getQuestion
   */
  function testGetQuestion() {
    $label = $this->randomMachineName();
    $string = 'Do you really want to delete %label?';

    $this->currency->expects($this->once())
      ->method('label')
      ->will($this->returnValue($label));

    $this->stringTranslation->expects($this->once())
      ->method('translate')
      ->with($string, array(
        '%label' => $label,
      ));

    $this->assertSame($string, $this->form->getQuestion());
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
    $this->assertInstanceOf('\Drupal\Core\Url', $url);
    $this->assertSame('currency.currency.list', $url->getRouteName());
  }

  /**
   * @covers ::submit
   */
  function testSubmit() {
    $this->currency->expects($this->once())
      ->method('delete');

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->once())
      ->method('setRedirect')
      ->with($this->isInstanceOf('\Drupal\Core\Url'));

    $this->form->submit($form, $form_state);
  }

}

}

namespace {

if (!function_exists('drupal_set_message')) {
  function drupal_set_message() {}
}

}
