<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\AmountFormattingForm.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configures amount formatting.
 */
class AmountFormattingForm extends ConfigFormBase {

  /**
   * The currency amount formatter manager.
   *
   * @var \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface
   */
  protected $currencyAmountFormatterManager;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translator.
   * @param \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface $currency_amount_formatter_manager
   *   The currency amount formatter manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, TranslationInterface $string_translation, AmountFormatterManagerInterface $currency_amount_formatter_manager) {
    $this->setConfigFactory($config_factory);
    $this->currencyAmountFormatterManager = $currency_amount_formatter_manager;
    $this->stringTranslation = $string_translation;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('config.factory'), $container->get('string_translation'), $container->get('plugin.manager.currency.amount_formatter'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'currency_amount_formatting';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $config = $this->configFactory()->get('currency.amount_formatting');

    $options = array();
    foreach ($this->currencyAmountFormatterManager->getDefinitions() as $plugin_id => $plugin_definition) {
      $options[$plugin_id] = $plugin_definition['label'];
    }
    $form['default_plugin_id'] = array(
      '#default_value' => $config->get('plugin_id'),
      '#options' => $options,
      '#process' => array('form_process_radios', array($this, 'processPluginOptions')),
      '#title' => $this->t('Default amount formatter'),
      '#type' => 'radios',
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * Implements Form API #process callback.
   */
  public function processPluginOptions($element) {
    foreach ($this->currencyAmountFormatterManager->getDefinitions() as $plugin_id => $plugin_definition) {
      if (isset($plugin_definition['description'])) {
        $element[$plugin_id]['#description'] = $plugin_definition['description'];
      }
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    $this->configFactory()->get('currency.amount_formatting')
      ->set('plugin_id', $form_state['values']['default_plugin_id'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
