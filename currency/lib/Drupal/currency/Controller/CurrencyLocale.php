<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyLocale.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for currency_locale entity UI routes.
 */
class CurrencyLocale extends ControllerBase implements ContainerInjectionInterface {

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
   *   The entity manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   *   The translation manager.
   */
  public function __construct(EntityManager $entity_manager, TranslationInterface $translation_manager) {
    $this->entityManager = $entity_manager;
    $this->translationManager = $translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'), $container->get('string_translation'));
  }

  /**
   * Builds a currency add form.
   *
   * @return array
   *   A renderable array.
   */
  public function add() {
    $currency_locale = $this->entityManager->getStorageController('currency_locale')->create(array());

    return $this->entityManager->getForm($currency_locale);
  }

  /**
   * Returns the title for a currency locale edit page.
   *
   * @param \Drupal\currency\Entity\CurrencyLocaleInterface $currency_locale
   *
   * @return string
   */
  public function editTitle(CurrencyLocaleInterface $currency_locale) {
    return $this->t('Edit @label', array(
      '@label' => $currency_locale->label(),
    ));
  }
}
