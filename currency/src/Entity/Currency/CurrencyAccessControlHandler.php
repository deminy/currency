<?php

/**
 * @file
 * Contains \Drupal\currency\Entity\Currency\CurrencyAccessControlHandler.
 */

namespace Drupal\currency\Entity\Currency;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityControllerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Checks access for currency entities.
 */
class CurrencyAccessControlHandler extends EntityAccessControlHandler implements EntityControllerInterface {

  /**
   * Constructs a new class instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(EntityTypeInterface $entity_type, ModuleHandlerInterface $module_handler) {
    parent::__construct($entity_type);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static($entity_type, $container->get('module_handler'));
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $currency, $operation, $langcode, AccountInterface $account) {
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */

    // Don't let the default currency be deleted.
    if ($currency->getCurrencyCode() == 'XXX' && $operation == 'delete') {
      return FALSE;
    }

    // The "enable" and "disable" operations are aliases for "update", but with
    // extra checks.
    if ($operation == 'enable') {
      return $currency->status() ? FALSE : $this->access($currency, 'update', $langcode, $account);
    }
    if ($operation == 'disable') {
      return $currency->status() ? $this->access($currency, 'update', $langcode, $account) : FALSE;
    }

    return $account->hasPermission('currency.currency.' . $operation);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return $account->hasPermission('currency.currency.create');
  }
}
