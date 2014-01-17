<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyLocalePattern.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for currency_locale_pattern entity UI routes.
 */
class CurrencyLocalePattern implements ContainerInjectionInterface {

  /**
   * Stores the Entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * Constructs a new class instance.
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
      $container->get('entity.manager')
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

    return $this->entityManager->getForm($locale_pattern);
  }
}
