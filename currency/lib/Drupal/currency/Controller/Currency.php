<?php

/**
 * @file
 * Contains \Drupal\currency\Controller\Currency.
 */

namespace Drupal\currency\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Drupal\currency\Entity\CurrencyInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Returns responses for Currency entity UI routes.
 */
class Currency implements ContainerInjectionInterface {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * The URL generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Entity\EntityManager $entity_manager
   *   The entity manager.
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The URL generator.
   */
  public function __construct(EntityManager $entity_manager, UrlGeneratorInterface $url_generator) {
    $this->entityManager = $entity_manager;
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.manager'), $container->get('url_generator'));
  }

  /**
   * Builds a currency add form.
   *
   * @return array
   *   A renderable array.
   */
  public function add() {
    $currency = $this->entityManager->getStorageController('currency')->create(array());

    return $this->entityManager->getForm($currency);
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
}
