<?php

/**
 * @file
 * Contains \Drupal\currency\Tests\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandlerUnitTest.
 */

namespace Drupal\currency\Tests\Entity\CurrencyLocale;

use Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandler;
use Drupal\currency\LocaleDelegatorInterface;
use Drupal\Tests\UnitTestCase;

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
   *
   * @covers ::__construct
   */
  public function setUp() {
    $this->entityType = $this->getMock('\Drupal\Core\Entity\EntityTypeInterface');

    $this->moduleHandler = $this->getMock('\Drupal\Core\Extension\ModuleHandlerInterface');

    $this->access = new CurrencyLocaleAccessControlHandler($this->entityType, $this->moduleHandler);
  }

  /**
   * @covers ::createInstance
   */
  function testCreateInstance() {
    $container = $this->getMock('\Symfony\Component\DependencyInjection\ContainerInterface');
    $container->expects($this->once())
      ->method('get')
      ->with('module_handler')
      ->will($this->returnValue($this->moduleHandler));

    $access = CurrencyLocaleAccessControlHandler::createInstance($container, $this->entityType);
    $this->assertInstanceOf('\Drupal\currency\Entity\CurrencyLocale\CurrencyLocaleAccessControlHandler', $access);
  }

  /**
   * @covers ::checkAccess
   *
   * @dataProvider providerTestCheckAccess
   */
  function testCheckAccess($expected_value, $operation, $has_permission, $permission, $locale = NULL) {
    $account = $this->getMock('\Drupal\Core\Session\AccountInterface');
    $account->expects($this->any())
      ->method('hasPermission')
      ->with($permission)
      ->will($this->returnValue((bool) $has_permission));

    $currency_locale = $this->getMockBuilder('\Drupal\currency\Entity\CurrencyLocale')
      ->disableOriginalConstructor()
      ->getMock();
    $currency_locale->expects($this->any())
      ->method('getLocale')
      ->will($this->returnValue($locale));

    $this->moduleHandler->expects($this->any())
      ->method('invokeAll')
      ->will($this->returnValue(array()));

    $method = new \ReflectionMethod($this->access, 'checkAccess');
    $method->setAccessible(TRUE);

    $language_code = $this->randomMachineName();

    $this->assertSame($expected_value, $method->invoke($this->access, $currency_locale, $operation, $language_code, $account));
  }

  /**
   * Provides data to self::testCheckAccess().
   */
  function providerTestCheckAccess() {
    return array(
      // The default currency locale cannot be deleted, even with permission.
      array(FALSE, 'delete', TRUE, 'currency.currency_locale.delete', LocaleDelegatorInterface::DEFAULT_LOCALE),
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
    $account = $this->getMock('\Drupal\Core\Session\AccountInterface');
    $account->expects($this->once())
      ->method('hasPermission')
      ->with('currency.currency_locale.create')
      ->will($this->returnValue($has_permission));
    $context = array();

    $method = new \ReflectionMethod($this->access, 'checkCreateAccess');
    $method->setAccessible(TRUE);

    $this->assertSame($expected_value, $method->invoke($this->access, $account, $context));
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
