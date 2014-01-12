<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\ExchangeDelegatorForm.
 */

namespace Drupal\currency\Controller;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\currency\ExchangeRateProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the configuration form for \Drupal\currency\ExchangeDelegator.
 */
class ExchangeDelegatorForm implements FormInterface, ContainerInjectionInterface {

  /**
   * The currency exchange rate provider.
   *
   * @var \Drupal\currency\ExchangeRateProvider
   */
  protected $exchangeRateProvider;

  /**
   * A currency exchanger plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $currencyExchangerManager;

  /**
   * Constructor.
   *
   * @param \Drupal\currency\ExchangeRateProvider $exchange_rate_provider
   *   The currency exchange rate provider.
   * @param \Drupal\Component\Plugin\PluginManagerInterface $currency_exchanger_manager
   *   A currency exchanger plugin manager.
   */
  public function __construct(ExchangeRateProvider $exchange_rate_provider, PluginManagerInterface $currency_exchanger_manager) {
    $this->exchangeRateProvider = $exchange_rate_provider;
    $this->currencyExchangerManager = $currency_exchanger_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('currency.exchange_rate_provider'), $container->get('plugin.manager.currency.exchange_rate_provider'));
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'currency_delegator';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {
    $definitions = $this->currencyExchangerManager->getDefinitions();
    $configuration = $this->exchangeRateProvider->loadConfiguration();

    $form['exchangers'] = array(
      '#header' => array(t('Title'), t('Enabled'), t('Weight'), t('Operations')),
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
        '#title' => t('Title'),
        '#title_display' => 'invisible',
        '#type' => 'item',
      );
      $form['exchangers'][$plugin_name]['enabled'] = array(
        '#default_value' => $enabled,
        '#title' => t('Enabled'),
        '#title_display' => 'invisible',
        '#type' => 'checkbox',
      );
      $form['exchangers'][$plugin_name]['weight'] = array(
        '#default_value' => $weight,
        '#title' => t('Weight'),
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
        '#title' => t('Operations'),
        '#type' => 'operations',
      );
    }
    $form['actions'] = array(
      '#type' => 'actions',
    );
    $form['actions']['save'] = array(
      '#button_type' => 'primary',
      '#type' => 'submit',
      '#value' => t('Save'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {
    uasort($form_state['values']['exchangers'], 'drupal_sort_weight');
    $configuration = array();
    foreach ($form_state['values']['exchangers'] as $plugin_name => $exchanger_configuration) {
      $configuration[$plugin_name] = (bool) $exchanger_configuration['enabled'];
    }
    $this->exchangeRateProvider->saveConfiguration($configuration);
    drupal_set_message(t('The configuration options have been saved.'));
  }
}
