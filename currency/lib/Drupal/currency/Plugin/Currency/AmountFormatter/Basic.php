<?php

/**
 * @file Contains \Drupal\currency\Plugin\Currency\AmountFormatter\Basic.
 */

namespace Drupal\currency\Plugin\Currency\AmountFormatter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\currency\Entity\CurrencyInterface;
use Drupal\currency\LocaleDelegator;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Formats amounts using string translation and number_format().
 *
 * @CurrencyAmountFormatter(
 *   description = @Translation("Formats amounts using a translatable string."),
 *   id = "currency_basic",
 *   label = @Translation("Basic")
 * )
 */
class Basic extends PluginBase implements AmountFormatterInterface, ContainerFactoryPluginInterface {

  /**
   * The locale delegator.
   *
   * @var \Drupal\currency\LocaleDelegator
   */
  protected $localeDelegator;

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation_manager
   *   The translation manager.
   * @param \Drupal\currency\LocaleDelegator $locale_delegator
   *   The locale delegator.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, TranslationInterface $translation_manager, LocaleDelegator $locale_delegator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->localeDelegator = $locale_delegator;
    $this->translationManager = $translation_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('string_translation'), $container->get('currency.locale_delegator'));
  }

  /**
   * {@inheritdoc}
   */
  public function formatAmount(CurrencyInterface $currency, $amount) {
    // Compute the number of decimals, so we can format all of them and no less
    // or more.
    $decimals = strlen($amount) - strpos($amount, '.') - 1;
    $currency_locale = $this->localeDelegator->getCurrencyLocale();
    $formatted_amount = number_format($amount, $decimals, $currency_locale->getDecimalSeparator(), $currency_locale->getGroupingSeparator());
    $arguments = array(
      '!currency_code' => $currency->getCurrencyCode(),
      '!currency_sign' => $currency->getSign(),
      '!amount' => $formatted_amount,
    );

    return $this->t('!currency_code !amount', $arguments);
  }
}
