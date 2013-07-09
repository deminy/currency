<?php

/**
 * @file
 * Definition of Drupal\currency\Plugin\Core\Entity\AccessController.
 */

namespace Drupal\currency\Plugin\Core\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityAccessController;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;

/**
 * Checks access for Currency's entities.
 */
class AccessController extends EntityAccessController {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    debug('currency.' . $entity->entityType() . '.' . $operation);
    return user_access('currency.' . $entity->entityType() . '.' . $operation, $account);
  }
}
