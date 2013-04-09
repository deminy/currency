<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyUI.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\ControllerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * Implements \Drupal\Core\ControllerInterface::create().
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

    return entity_get_form($currency);
  }

  /**
   * Builds a currency edit form.
   *
   * @param EntityInterface $currency
   *
   * @return array
   *   A renderable array.
   */
  public function edit(EntityInterface $currency) {
    drupal_set_title(t('Edit @label', array(
      '@label' => $currency->label(),
    )));

    return entity_get_form($currency);
  }

  /**
   * Lists all entities.
   *
   * @return array
   *   A renderable array.
   */
  public function listing() {
    return $this->entityManager->getListController('currency')->render();
  }

  /**
   * Enables a currency.
   *
   * @return NULL
   */
  public function enable(EntityInterface $currency) {
    $currency->enable();
    $currency->save();
    drupal_goto('admin/config/regional/currency');
  }

  /**
   * Disables a currency.
   *
   * @return NULL
   */
  public function disable(EntityInterface $currency) {
    $currency->disable();
    $currency->save();
    drupal_goto('admin/config/regional/currency');
  }
}
