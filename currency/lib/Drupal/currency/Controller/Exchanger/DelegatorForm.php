<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\Exchanger\DelegatorForm.
 */

namespace Drupal\currency\Controller\Exchanger;

use Drupal\Core\ControllerInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Component\Plugin\PluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the currency_delegator exchanger configuration form.
 */
class DelegatorForm implements FormInterface, ControllerInterface {

  /**
   * A currency exchanger plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $manager;

  /**
   * Constructor.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $manager
   *   A currency exchanger plugin manager.
   */
  public function __construct(PluginManagerInterface $manager) {
    $this->manager = $manager;
  }

  /**
   * Implements \Drupal\Core\ControllerInterface::create().
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.currency.exchanger'));
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::getFormID().
   */
  public function getFormID() {
    return 'currency_exchanger_delegator';
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::buildForm().
   */
  public function buildForm(array $form, array &$form_state) {
    $definitions = $this->manager->getDefinitions();
    $delegator_plugin = $this->manager->createInstance('currency_delegator');
    $configuration = $delegator_plugin->loadConfiguration();

    $form['exchangers'] = array(
      '#header' => array(t('Title'), t('Enabled'), t('Weight'), t('Operations')),
      '#tabledrag' => array(array('order', 'sibling', 'form-select')),
      '#type' => 'table',
    );
    $weight = 0;
    foreach ($configuration as $plugin_name => $enabled) {
      $weight++;
      $plugin = $this->manager->createInstance($plugin_name);
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
   * Implements \Drupal\Core\Form\FormInterface::validateForm().
   */
  public function validateForm(array &$form, array &$form_state) {
  }

  /**
   * Implements \Drupal\Core\Form\FormInterface::validateForm().
   */
  public function submitForm(array &$form, array &$form_state) {
    $plugin = $this->manager->createInstance('currency_delegator');
    uasort($form_state['values']['exchangers'], 'drupal_sort_weight');
    $configuration = array();
    foreach ($form_state['values']['exchangers'] as $plugin_name => $exchanger_configuration) {
      $configuration[$plugin_name] = (bool) $exchanger_configuration['enabled'];
    }
    $plugin->saveConfiguration($configuration);
    drupal_set_message(t('The configuration options have been saved.'));
  }
}
