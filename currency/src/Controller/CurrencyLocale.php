<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\CurrencyLocale.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for currency_locale entity UI routes.
 */
class CurrencyLocale extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity manager.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   *   The translation manager.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityFormBuilderInterface $entity_form_builder, TranslationInterface $translation_manager) {
    $this->entityManager = $entity_manager;
    $this->entityFormBuilder = $entity_form_builder;
    $this->translationManager = $translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'), $container->get('entity.form_builder'), $container->get('string_translation'));
  }

  /**
   * Builds a currency add form.
   *
   * @return array
   *   A renderable array.
   */
  public function add() {
    $currency_locale = $this->entityManager->getStorage('currency_locale')->create(array());

    return $this->entityFormBuilder->getForm($currency_locale);
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
