<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\AddCurrencyLocale.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles the "add currency locale" route.
 */
class AddCurrencyLocale extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Constructs a new instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityFormBuilderInterface $entity_form_builder) {
    $this->entityManager = $entity_manager;
    $this->entityFormBuilder = $entity_form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'), $container->get('entity.form_builder'));
  }

  /**
   * Builds a currency add form.
   *
   * @return array
   *   A renderable array.
   */
  public function execute() {
    $currency_locale = $this->entityManager->getStorage('currency_locale')->create(array());

    return $this->entityFormBuilder->getForm($currency_locale);
  }

}
