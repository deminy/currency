<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\Currency.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\currency\Entity\CurrencyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Returns responses for Currency entity UI routes.
 */
class Currency extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Entity\EntityFormBuilderInterface $entity_form_builder
   *   The entity form builder.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   *   The translation manager.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   */
  public function __construct(EntityManagerInterface $entity_manager, EntityFormBuilderInterface $entity_form_builder, TranslationInterface $translation_manager, UrlGeneratorInterface $url_generator) {
    $this->entityManager = $entity_manager;
    $this->entityFormBuilder = $entity_form_builder;
    $this->translationManager = $translation_manager;
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'), $container->get('entity.form_builder'), $container->get('string_translation'), $container->get('url_generator'));
  }

  /**
   * Builds a currency add form.
   *
   * @return array
   *   A renderable array.
   */
  public function add() {
    $currency = $this->entityManager->getStorageController('currency')->create(array());

    return $this->entityFormBuilder->getForm($currency);
  }

  /**
   * Enables a currency.
   *
   * @param \Drupal\currency\Entity\CurrencyInterface $currency
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function enable(CurrencyInterface $currency) {
    $currency->enable();
    $currency->save();

    return new RedirectResponse($this->urlGenerator->generateFromRoute('currency.currency.list', array(
      'absolute' => TRUE,
    )));
  }

  /**
   * Disables a currency.
   *
   * @param \Drupal\currency\Entity\CurrencyInterface $currency
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function disable(CurrencyInterface $currency) {
    $currency->disable();
    $currency->save();

    return new RedirectResponse($this->urlGenerator->generateFromRoute('currency.currency.list', array(
      'absolute' => TRUE,
    )));
  }

  /**
   * Returns the title for a currency edit page.
   *
   * @param \Drupal\currency\Entity\CurrencyInterface $currency
   *
   * @return string
   */
  public function editTitle(CurrencyInterface $currency) {
    return $this->t('Edit @label', array(
      '@label' => $currency->label(),
    ));
  }
}
