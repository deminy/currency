<?php

/**
 * @file
 * Contains \Drupal\Tests\currency\Unit\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandlerUnitTest.
 */

namespace Drupal\Tests\currency\Unit\Entity\CurrencyLocale;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandler;
use Drupal\currency\Entity\CurrencyLocaleInterface;
use Drupal\currency\LocaleResolverInterface;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @coversDefaultClass Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandler
 *
 * @group Currency
 */
class CurrencyLocaleAccessControlHandlerUnitTest extends UnitTestCase {

  /**
   * The access handler under test.
   *
   * @var \Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandler
   */
  protected $access;

  /**
   * Information about the entity type.
   *
   * @var \Drupal\Core\Entity\EntityTypeInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $entityType;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface|\PHPUnit_Framework_MockObject_MockObject
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    $this->entityType = $this->getMock(EntityTypeInterface::class);

    $this->moduleHandler = $this->getMock(ModuleHandlerInterface::class);

    $this->access = new CurrencyLocaleAccessControlHandler($this->entityType, $this->moduleHandler);
  }

  /**
   * @covers ::createInstance
   * @covers ::__construct
   */
  function testCreateInstance() {
    $container = $this->getMock(ContainerInterface::class);
    $container->expects($this->once())
      ->method('get')
      ->with('module_handler')
      ->will($this->returnValue($this->moduleHandler));

    $access = CurrencyLocaleAccessControlHandler::createInstance($container, $this->entityType);
    $this->assertInstanceOf(CurrencyLocaleAccessControlHandler::class, $access);
  }

  /**
   * @covers ::checkAccess
   *
   * @dataProvider providerTestCheckAccess
   */
  function testCheckAccess($expected_value, $operation, $has_permission, $permission, $locale = NULL) {
    $account = $this->getMock(AccountInterface::class);
    $account->expects($this->any())
      ->method('hasPermission')
      ->with($permission)
      ->will($this->returnValue((bool) $has_permission));

    $currency_locale = $this->getMock(CurrencyLocaleInterface::class);
    $currency_locale->expects($this->any())
      ->method('getLocale')
      ->will($this->returnValue($locale));

    $this->moduleHandler->expects($this->any())
      ->method('invokeAll')
      ->will($this->returnValue(array()));

    $method = new \ReflectionMethod($this->access, 'checkAccess');
    $method->setAccessible(TRUE);

    $language_code = $this->randomMachineName();

    $this->assertSame($expected_value, $method->invoke($this->access, $currency_locale, $operation, $language_code, $account)->isAllowed());
  }

  /**
   * Provides data to self::testCheckAccess().
   */
  function providerTestCheckAccess() {
    return array(
      // The default currency locale cannot be deleted, even with permission.
      array(FALSE, 'delete', TRUE, 'currency.currency_locale.delete', LocaleResolverInterface::DEFAULT_LOCALE),
      // Any non-default currency locale can be deleted with permission.
      array(TRUE, 'delete', TRUE, 'currency.currency_locale.delete', $this->randomMachineName()),
      // No currency locale can be deleted without permission.
      array(FALSE, 'delete', FALSE, 'currency.currency_locale.delete', $this->randomMachineName()),
      // Any currency locale can be updated with permission.
      array(TRUE, 'update', TRUE, 'currency.currency_locale.update', $this->randomMachineName()),
      // No currency locale can be updated without permission.
      array(FALSE, 'update', FALSE, 'currency.currency_locale.update', $this->randomMachineName()),
    );
  }

  /**
   * @covers ::checkCreateAccess
   *
   * @dataProvider providerTestCheckCreateAccess
   */
  function testCheckCreateAccess($expected_value, $has_permission) {
    $account = $this->getMock(AccountInterface::class);
    $account->expects($this->once())
      ->method('hasPermission')
      ->with('currency.currency_locale.create')
      ->will($this->returnValue($has_permission));
    $context = array();

    $method = new \ReflectionMethod($this->access, 'checkCreateAccess');
    $method->setAccessible(TRUE);

    $this->assertSame($expected_value, $method->invoke($this->access, $account, $context)->isAllowed());
  }

  /**
   * Provides data to self::testCheckCreateAccess().
   */
  function providerTestCheckCreateAccess() {
    return array(
      array(TRUE, TRUE),
      array(FALSE, FALSE),
    );
  }

}
