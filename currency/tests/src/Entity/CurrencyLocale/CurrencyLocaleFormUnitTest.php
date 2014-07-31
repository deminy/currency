<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyLocale\CurrencyLocaleFormUnitTest.
 */

namespace Drupal\currency\Tests\Entity\CurrencyLocale {

use Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleForm;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleForm
 *
 * @group Currency
 */
class CurrencyLocaleFormUnitTest extends UnitTestCase {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $countryManager;

  /**
   * The currency locale.
   *
   * @var \Drupal\currency\Entity\CurrencyLocaleInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyLocale;

  /**
   * The currency locale storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $currencyLocaleStorage;

  /**
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $formBuilder;

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
   * @var \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleForm
   */
  protected $form;

  /**
   * {@inheritdoc}
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->countryManager = $this->getMock('\Drupal\Core\Locale\CountryManagerInterface');

    $this->currencyLocale = $this->getMockBuilder('\Drupal\currency\Entity\CurrencyLocale')
      ->disableOriginalConstructor()
      ->getMock();

    $this->currencyLocaleStorage = $this->getMock('\Drupal\Core\Entity\EntityStorageInterface');

    $this->formBuilder = $this->getMock('\Drupal\Core\Form\FormBuilderInterface');

    $this->linkGenerator = $this->getMock('\Drupal\Core\Utility\LinkGeneratorInterface');

    $this->stringTranslation = $this->getMock('\Drupal\Core\StringTranslation\TranslationInterface');
    $this->stringTranslation->expects($this->any())
      ->method('translate')
      ->will($this->returnArgument(0));

    $this->form = new CurrencyLocaleForm($this->stringTranslation, $this->formBuilder, $this->linkGenerator, $this->currencyLocaleStorage, $this->countryManager);
    $this->form->setEntity($this->currencyLocale);
  }

  /**
   * @covers ::create
   */
  function testCreate() {
    $entity_manager = $this->getMock('\Drupal\Core\Entity\EntityManagerInterface');
    $entity_manager->expects($this->once())
      ->method('getStorage')
      ->with('currency_locale')
      ->will($this->returnValue($this->currencyLocaleStorage));

    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');

    $map = array(
      array('country_manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->countryManager),
      array('entity.manager', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $entity_manager),
      array('form_builder', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->formBuilder),
      array('link_generator', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->linkGenerator),
      array('string_translation', ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE, $this->stringTranslation),
    );
    $container->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($map));

    $form = CurrencyLocaleForm::create($container);
    $this->assertInstanceOf('\Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleForm', $form);
  }

  /**
   * @covers ::copyFormValuesToEntity
   */
  public function testCopyFormValuesToEntity() {
    $language_code = $this->randomName();
    $country_code = $this->randomName();
    $pattern = $this->randomName();
    $decimal_separator = $this->randomName();
    $grouping_separator = $this->randomName();

    $this->currencyLocale->expects($this->once())
      ->method('setLocale')
      ->with($language_code, $country_code);
    $this->currencyLocale->expects($this->once())
      ->method('setPattern')
      ->with($pattern);
    $this->currencyLocale->expects($this->once())
      ->method('setDecimalSeparator')
      ->with($decimal_separator);
    $this->currencyLocale->expects($this->once())
      ->method('setGroupingSeparator')
      ->with($grouping_separator);

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->atLeastOnce())
      ->method('getValues')
      ->willReturn(array(
        'language_code' => $language_code,
        'country_code' => $country_code,
        'pattern' => $pattern,
        'decimal_separator' => $decimal_separator,
        'grouping_separator' => $grouping_separator,
      ));

    $method = new \ReflectionMethod($this->form, 'copyFormValuesToEntity');
    $method->setAccessible(TRUE);

    $method->invokeArgs($this->form, array($this->currencyLocale, $form, $form_state));
  }

  /**
   * @covers ::form
   */
  public function testForm() {
    $language_code = $this->randomName();
    $country_code = $this->randomName();
    $pattern = $this->randomName();
    $decimal_separator = $this->randomName();
    $grouping_separator = $this->randomName();

    $this->currencyLocale->expects($this->once())
      ->method('getLanguageCode')
      ->will($this->returnValue($language_code));
    $this->currencyLocale->expects($this->once())
      ->method('getCountryCode')
      ->will($this->returnValue($country_code));
    $this->currencyLocale->expects($this->once())
      ->method('getPattern')
      ->will($this->returnValue($pattern));
    $this->currencyLocale->expects($this->once())
      ->method('getDecimalSeparator')
      ->will($this->returnValue($decimal_separator));
    $this->currencyLocale->expects($this->once())
      ->method('getGroupingSeparator')
      ->will($this->returnValue($grouping_separator));

    $language = $this->getMockBuilder('\Drupal\Core\Language\Language')
      ->disableOriginalConstructor()
      ->getMock();

    $this->currencyLocale->expects($this->any())
      ->method('language')
      ->will($this->returnValue($language));

    $country_list = array(
      $this->randomName() => $this->randomName(),
    );
    $this->countryManager->expects($this->atLeastOnce())
      ->method('getList')
      ->will($this->returnValue($country_list));

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');

    $expected = array(
      'locale' => array(
        '#title' => 'Locale',
        '#type' => 'details',
        'language_code' => array(
          '#default_value' => $language_code,
          '#empty_value' => '',
          '#options' => array(
            'af' => 'Afrikaans',
                'sq' => 'Albanian',
                'am' => 'Amharic',
                'ar' => 'Arabic',
                'hy' => 'Armenian',
                'ast' => 'Asturian',
                'az' => 'Azerbaijani',
                'ms' => 'Bahasa Malaysia',
                'eu' => 'Basque',
                'be' => 'Belarusian',
                'bn' => 'Bengali',
                'bs' => 'Bosnian',
                'bg' => 'Bulgarian',
                'my' => 'Burmese',
                'ca' => 'Catalan',
                'zh-hans' => 'Chinese, Simplified',
                'zh-hant' => 'Chinese, Traditional',
                'hr' => 'Croatian',
                'cs' => 'Czech',
                'da' => 'Danish',
                'nl' => 'Dutch',
                'dz' => 'Dzongkha',
                'en' => 'English',
                'eo' => 'Esperanto',
                'et' => 'Estonian',
                'fo' => 'Faeroese',
                'fil' => 'Filipino',
                'fi' => 'Finnish',
                'fr' => 'French',
                'fy' => 'Frisian, Western',
                'gl' => 'Galician',
                'ka' => 'Georgian',
                'de' => 'German',
                'el' => 'Greek',
                'gu' => 'Gujarati',
                'ht' => 'Haitian Creole',
                'he' => 'Hebrew',
                'hi' => 'Hindi',
                'hu' => 'Hungarian',
                'is' => 'Icelandic',
                'id' => 'Indonesian',
                'ga' => 'Irish',
                'it' => 'Italian',
                'ja' => 'Japanese',
                'jv' => 'Javanese',
                'kn' => 'Kannada',
                'kk' => 'Kazakh',
                'km' => 'Khmer',
                'ko' => 'Korean',
                'ku' => 'Kurdish',
                'ky' => 'Kyrgyz',
                'lo' => 'Lao',
                'lv' => 'Latvian',
                'lt' => 'Lithuanian',
                'xx-lolspeak' => 'Lolspeak',
                'mk' => 'Macedonian',
                'mg' => 'Malagasy',
                'ml' => 'Malayalam',
                'mr' => 'Marathi',
                'mn' => 'Mongolian',
                'ne' => 'Nepali',
                'se' => 'Northern Sami',
                'nb' => 'Norwegian Bokmål',
                'nn' => 'Norwegian Nynorsk',
                'oc' => 'Occitan',
                'fa' => 'Persian, Farsi',
                'pl' => 'Polish',
                'pt-br' => 'Portuguese, Brazil',
                'pt-pt' => 'Portuguese, Portugal',
                'pa' => 'Punjabi',
                'ro' => 'Romanian',
                'ru' => 'Russian',
                'sco' => 'Scots',
                'gd' => 'Scots Gaelic',
                'sr' => 'Serbian',
                'si' => 'Sinhala',
                'sk' => 'Slovak',
                'sl' => 'Slovenian',
                'es' => 'Spanish',
                'sw' => 'Swahili',
                'sv' => 'Swedish',
                'gsw-berne' => 'Swiss German',
                'ta' => 'Tamil',
                'ta-lk' => 'Tamil, Sri Lanka',
                'te' => 'Telugu',
                'th' => 'Thai',
                'bo' => 'Tibetan',
                'tr' => 'Turkish',
                'tyv' => 'Tuvan',
                'uk' => 'Ukrainian',
                'ur' => 'Urdu',
                'ug' => 'Uyghur',
                'vi' => 'Vietnamese',
                'cy' => 'Welsh',
          ),
          '#required' => TRUE,
          '#title' => 'Language',
          '#type' => 'select',
        ),
        'country_code' => array(
          '#default_value' => $country_code,
          '#empty_value' => '',
          '#options' => $country_list,
          '#required' => TRUE,
          '#title' => 'Country',
          '#type' => 'select',
        ),
      ),
      'formatting' => array(
        '#title' => 'Formatting',
        '#type' => 'details',
        'decimal_separator' => array(
          '#default_value' => $decimal_separator,
          '#maxlength' => 255,
          '#required' => TRUE,
          '#size' => 3,
          '#title' => 'Decimal separator',
          '#type' => 'textfield',
        ),
        'grouping_separator' => array(
          '#default_value' => $grouping_separator,
          '#maxlength' => 255,
          '#size' => 3,
          '#title' => 'Group separator',
          '#type' => 'textfield',
        ),
        'pattern' => array(
          '#default_value' => $pattern,
          '#description' => 'A Unicode <abbr title="Common Locale Data Repository">CLDR</abbr> <a href="http://cldr.unicode.org/translation/number-patterns">currency number pattern</a>',
          '#maxlength' => 255,
          '#title' => 'Pattern',
          '#type' => 'textfield',
        ),
      ),
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
    $this->currencyLocale->expects($this->once())
      ->method('save');

    $form = array();
    $form_state = $this->getMock('\Drupal\Core\Form\FormStateInterface');
    $form_state->expects($this->once())
      ->method('setRedirect')
      ->with($this->isInstanceOf('\Drupal\Core\Url'));

    $this->form->save($form, $form_state);
  }

  /**
   * @covers ::validate
   * @dataProvider providerTestValidate
   */
  public function testValidate($input_value_language_code, $input_value_country_code, $locale, $currency_locale_is_new, $locale_is_used) {
    $form = array(
      'locale' => array(
        '#foo' => $this->randomName(),
      ),
    );
    // @todo Use FormStateInterface once EntityForm no longer uses ArrayAccess.
    $form_state = $this->getMockBuilder('\Drupal\Core\Form\FormState')
      ->disableOriginalConstructor()
      ->getMock();
    $form_state->expects($this->any())
      ->method('getValues')
      ->willReturn(array(
        'country_code' => $input_value_country_code,
        'language_code' => $input_value_language_code,
      ));

    $this->currencyLocale->expects($this->atLeastOnce())
      ->method('isNew')
      ->will($this->returnValue($currency_locale_is_new));

    if ($currency_locale_is_new) {
      if ($locale_is_used) {
        $loaded_currency_locale = $this->getMockBuilder('\Drupal\currency\Entity\CurrencyLocale')
          ->disableOriginalConstructor()
          ->getMock();

        $this->currencyLocaleStorage->expects($this->once())
          ->method('load')
          ->with($locale)
          ->will($this->returnValue($loaded_currency_locale));

        $this->formBuilder->expects($this->once())
          ->method('setError')
          ->with($form['locale'], $form_state, 'A pattern for this locale already exists.');
      }
      else {
        $this->currencyLocaleStorage->expects($this->once())
          ->method('load')
          ->with($locale)
          ->will($this->returnValue(FALSE));

        $this->formBuilder->expects($this->never())
          ->method('setError');
      }
    }
    else {
      $this->currencyLocaleStorage->expects($this->never())
        ->method('load');

      $this->formBuilder->expects($this->never())
        ->method('setError');
    }

    $this->form->validate($form, $form_state);
  }

  /**
   * Provides data to self::testValidate()
   */
  public function providerTestValidate() {
    return array(
      array('Nl', 'nL', 'nl_NL', FALSE, FALSE),
      array('UK', 'ua', 'uk_UA', TRUE, FALSE),
      array('nl', 'NL', 'nl_NL', TRUE, TRUE),
      array('uk', 'UA', 'uk_UA', FALSE, TRUE),
    );
  }

}

}

namespace {

  if (!function_exists('drupal_set_message')) {
    function drupal_set_message() {}
  }
  if (!function_exists('form_execute_handlers')) {
    function form_execute_handlers() {}
  }

}
