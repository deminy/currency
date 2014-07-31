<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\ExchangeRateProviderForm.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\currency\ExchangeRateProviderInterface;
use Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the configuration form for \Drupal\currency\ExchangeRateProvider.
 */
class ExchangeRateProviderForm extends FormBase implements ContainerInjectionInterface {

  /**
   * The currency exchange rate provider.
   *
   * @var \Drupal\currency\ExchangeRateProviderInterface
   */
  protected $exchangeRateProvider;

  /**
   * The currency exchange rate provider manager.
   *
   * @var \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface
   */
  protected $currencyExchangeRateProviderManager;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   *   The translation manager.
   * @param \Drupal\currency\ExchangeRateProviderInterface $exchange_rate_provider
   *   The currency exchange rate provider.
   * @param \Drupal\currency\Plugin\Currency\ExchangeRateProvider\ExchangeRateProviderManagerInterface $currency_exchange_rate_provider_manager
   *   The currency exchange rate provider manager.
   */
  public function __construct(TranslationInterface $translation_manager, ExchangeRateProviderInterface $exchange_rate_provider, ExchangeRateProviderManagerInterface $currency_exchange_rate_provider_manager) {
    $this->exchangeRateProvider = $exchange_rate_provider;
    $this->currencyExchangeRateProviderManager = $currency_exchange_rate_provider_manager;
    $this->translationManager = $translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('string_translation'), $container->get('currency.exchange_rate_provider'), $container->get('plugin.manager.currency.exchange_rate_provider'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'currency_exchange_rate_provider';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $definitions = $this->currencyExchangeRateProviderManager->getDefinitions();
    $configuration = $this->exchangeRateProvider->loadConfiguration();

    $form['exchangers'] = array(
      '#header' => array($this->t('Title'), $this->t('Enabled'), $this->t('Weight'), $this->t('Operations')),
      '#tabledrag' => array(array(
        'action' => 'order',
        'relationship' => 'sibling',
        'group' => 'form-select',
      )),
      '#type' => 'table',
    );
    $weight = 0;
    foreach ($configuration as $plugin_name => $enabled) {
      $weight++;
      $plugin_definition = $definitions[$plugin_name];

      $form['exchangers'][$plugin_name] = array(
        '#attributes' => array(
          'class' => array('draggable'),
        ),
        '#weight' => $weight,
      );
      $form['exchangers'][$plugin_name]['title'] = array(
        '#description' => $plugin_definition['description'],
        '#markup' => $plugin_definition['label'],
        '#title' => $this->t('Title'),
        '#title_display' => 'invisible',
        '#type' => 'item',
      );
      $form['exchangers'][$plugin_name]['enabled'] = array(
        '#default_value' => $enabled,
        '#title' => $this->t('Enabled'),
        '#title_display' => 'invisible',
        '#type' => 'checkbox',
      );
      $form['exchangers'][$plugin_name]['weight'] = array(
        '#default_value' => $weight,
        '#title' => $this->t('Weight'),
        '#title_display' => 'invisible',
        '#type' => 'weight',
      );
      $links = array();
      foreach ($plugin_definition['operations'] as $path => $title) {
        $links[] = array(
          'href' => $path,
          'title' => $title,
        );
      }
      $form['exchangers'][$plugin_name]['operations'] = array(
        '#links' => $links,
        '#title' => $this->t('Operations'),
        '#type' => 'operations',
      );
    }
    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['save'] = array(
      '#button_type' => 'primary',
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    uasort($values['exchangers'], '\Drupal\Component\Utility\SortArray::sortByWeightElement');
    $configuration = array();
    foreach ($values['exchangers'] as $plugin_name => $exchanger_configuration) {
      $configuration[$plugin_name] = (bool) $exchanger_configuration['enabled'];
    }
    $this->exchangeRateProvider->saveConfiguration($configuration);
    drupal_set_message($this->t('The configuration options have been saved.'));
  }
}
