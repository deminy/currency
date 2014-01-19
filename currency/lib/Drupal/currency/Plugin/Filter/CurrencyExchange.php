<?php

/**
 * @file
 * Contains \Drupal\currency\Plugin\Filter\CurrencyExchange.
 */

namespace Drupal\currency\Plugin\Filter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\currency\MathInterface;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to exchange currencies.
 *
 * @Filter(
 *   id = "currency_exchange",
 *   module = "currency",
 *   title = @Translation("Currency exchange"),
 *   type = FILTER_TYPE_MARKUP_LANGUAGE
 * )
 */
class CurrencyExchange extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The math service.
   *
   * @var \Drupal\currency\MathInterface
   */
  protected $math;

  /**
   * Constructs a new class instance
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\currency\MathInterface
   *   The Currency math service.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, MathInterface $math) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->math = $math;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, array $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('currency.math'));
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode, $cache, $cache_id) {
    return preg_replace_callback('/\[currency:([a-z]{3}):([a-z]{3})(.*?)\]/i', array($this, 'processCallback'), $text);
  }

  /**
   * Implements preg_replace_callback() callback.
   *
   * @see self::process()
   */
  function processCallback(array $matches) {
    $currency_code_from = $matches[1];
    $currency_code_to = $matches[2];
    $amount = str_replace(':', '', $matches[3]);
    if (strlen($amount) !== 0) {
      $amount = \Drupal::service('currency.input')->parseAmount($amount);
      // The amount is invalid, so return the token.
      if (!$amount) {
        return $matches[0];
      }
    }
    // The amount defaults to 1.
    else {
      $amount = 1;
    }

    /** @var \Drupal\currency\ExchangeRateProviderInterface $exchanger */
    $exchanger = \Drupal::service('currency.exchange_rate_provider');
    $exchange_rate = $exchanger->load($currency_code_from, $currency_code_to);
    if ($exchange_rate) {
      return $this->math->multiply($amount, $exchange_rate->getRate());
    }
    // The filter failed, so return the token.
    return $matches[0];
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return t('Use <code>[currency:from:to:amount]</code> to convert an amount of money from one currency to another. The <code>amount</code> parameter is optional and defaults to <code>1</code>. Example: <code>[currency:EUR:USD:100]</code>.');
  }

}