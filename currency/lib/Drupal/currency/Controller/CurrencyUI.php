<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyUI.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Controller\ControllerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Returns responses for Currency entity UI routes.
 */
class CurrencyUI implements ControllerInterface {

  /**
   * Stores the Entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Entity\EntityManager $entity_manager
   *   The Entity manager.
   */
  public function __construct(EntityManager $entity_manager) {
    $this->entityManager = $entity_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.entity')
    );
  }

  /**
   * Builds a currency add form.
   *
   * @return array
   *   A renderable array.
   */
  public function add() {
    $currency = $this->entityManager->getStorageController('currency')->create(array());

    return drupal_get_form($this->entityManager->getFormController('currency', 'default')->setEntity($currency));
  }

  /**
   * Enables a currency.
   *
   * @return NULL
   */
  public function enable(EntityInterface $currency) {
    $currency->enable();
    $currency->save();
    return new RedirectResponse(url('admin/config/regional/currency', array('absolute' => TRUE)));
  }

  /**
   * Disables a currency.
   *
   * @return NULL
   */
  public function disable(EntityInterface $currency) {
    $currency->disable();
    $currency->save();
    return new RedirectResponse(url('admin/config/regional/currency', array('absolute' => TRUE)));
  }
}
