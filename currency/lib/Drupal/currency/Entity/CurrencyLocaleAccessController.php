<?php

/**
 * @file
 * Contains \Drupal\currency\Entity\CurrencyLocaleAccessController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Entity\EntityControllerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\currency\LocaleDelegator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks access for currency_locale entities.
 */
class CurrencyLocaleAccessController extends EntityAccessController implements EntityControllerInterface {

  /**
   * The currency locale delegator.
   *
   * @var \Drupal\currency\LocaleDelegator
   */
  protected $localeDelegator;

  /**
   * Constructor
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_info
   *   The entity info for the entity type.
   * @param \Drupal\currency\LocaleDelegator $locale_delegator
   */
  public function __construct(EntityTypeInterface $entity_info, LocaleDelegator $locale_delegator) {
    parent::__construct($entity_info);
    $this->localeDelegator = $locale_delegator;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_info) {
    return new static($entity_info, $container->get('currency.locale_delegator'));
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    /** @var \Drupal\currency\Entity\CurrencyLocaleInterface $entity */
    $delegator = $this->localeDelegator;
    if ($entity->getLocale() == $delegator::DEFAULT_LOCALE && $operation == 'delete') {
      return FALSE;
    }
    return $account->hasPermission('currency.currency_locale.' . $operation);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return $account->hasPermission('currency.currency_locale.create');
  }
}
