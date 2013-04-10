<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyLocalePatternUI.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\ControllerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for currency_locale_pattern entity UI routes.
 */
class CurrencyLocalePatternUI implements ControllerInterface {

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
    $locale_pattern = $this->entityManager->getStorageController('currency_locale_pattern')->create(array());

    return entity_get_form($locale_pattern);
  }

  /**
   * Builds a currency edit form.
   *
   * @param EntityInterface $locale_pattern
   *
   * @return array
   *   A renderable array.
   */
  public function edit(EntityInterface $currency_locale_pattern) {
    drupal_set_title(t('Edit @label', array(
      '@label' => $currency_locale_pattern->label(),
    )));

    return entity_get_form($currency_locale_pattern);
  }

  /**
   * Lists all entities.
   *
   * @return array
   *   A renderable array.
   */
  public function listing() {
    return $this->entityManager->getListController('currency_locale_pattern')->render();
  }
}
