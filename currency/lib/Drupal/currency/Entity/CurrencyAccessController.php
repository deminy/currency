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
  protected function checkAccess(EntityInterface $currency, $operation, $langcode, AccountInterface $account) {
    /** @var \Drupal\currency\Entity\CurrencyInterface $currency */

    // Don't let the default currency be deleted.
    if ($currency->getCurrencyCode() == 'XXX' && $operation == 'delete') {
      return FALSE;
    }

    // The "enable" and "disable" operations are aliases for "update", but with
    // extra checks.
    if ($operation == 'enable') {
      return $currency->status() ? FALSE : $currency->access('update', $account);
    }
    if ($operation == 'disable') {
      return $currency->status() ? $currency->access('update', $account) : FALSE;
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
