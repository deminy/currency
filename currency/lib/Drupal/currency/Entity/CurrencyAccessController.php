<?php

/**
 * @file
 * Contains \Drupal\currency\Entity\CurrencyAccessController.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;

/**
 * Checks access for currency entities.
 */
class CurrencyAccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    return $account->hasPermission('currency.currency.' . $operation);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return $account->hasPermission('currency.currency.create');
  }
}
