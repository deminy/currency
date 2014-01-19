<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\Currency.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines a currency entity class.
 *
 * @EntityType(
 *   config_prefix = "currency.currency",
 *   controllers = {
 *     "access" = "Drupal\currency\Entity\CurrencyAccessController",
 *     "form" = {
 *       "default" = "Drupal\currency\Entity\CurrencyFormController",
 *       "delete" = "Drupal\currency\Entity\CurrencyDeleteFormController"
 *     },
 *     "list" = "Drupal\currency\Entity\CurrencyListController",
 *     "storage" = "Drupal\currency\Entity\CurrencyStorageController",
 *   },
 *   entity_keys = {
 *     "id" = "currencyCode",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *     "status" = "status"
 *   },
 *   fieldable = FALSE,
 *   id = "currency",
 *   label = @Translation("Currency")
 * )
 */
class Currency extends ConfigEntityBase implements CurrencyInterface {

  /**
   * Alternative (non-official) currency signs.
   *
   * @var array
   *   An array of strings that are similar to self::sign.
   */
  protected $alternativeSigns = array();

  /**
   * ISO 4217 currency code.
   *
   * @var string
   */
  public $currencyCode = NULL;

  /**
   * ISO 4217 currency number.
   *
   * @var string
   */
  protected $currencyNumber = NULL;

  /**
   * Exchange rates to other currencies.
   *
   * @var array
   *   Keys are ISO 4217 codes, values are numeric strings.
   */
  protected $exchangeRates = array();

  /**
   * The human-readable name.
   *
   * @var string
   */
  public $label = NULL;

  /**
   * The number of subunits to round amounts in this currency to.
   *
   * @see Currency::getRoundingStep()
   *
   * @var integer
   */
  protected $roundingStep = NULL;

  /**
   * The currency's official sign, such as '€' or '$'.
   *
   * @var string
   */
  protected $sign = '¤';

  /**
   * The number of subunits this currency has.
   *
   * @var integer|null
   */
  protected $subunits = NULL;

  /**
   * This currency's usages.
   *
   * @var \Drupal\currency\UsageInterface[]
   */
  protected $usages = array();

  /**
   * The UUID for this entity.
   *
   * @var string
   */
  public $uuid = NULL;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type) {
    parent::__construct($values, $entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public function setAlternativeSigns(array $signs) {
    $this->alternativeSigns = $signs;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAlternativeSigns() {
    return $this->alternativeSigns;
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrencyCode($code) {
    $this->currencyCode = $code;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrencyCode() {
    return $this->currencyCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setCurrencyNumber($number) {
    $this->currencyNumber = $number;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCurrencyNumber() {
    return $this->currencyNumber;
  }

  /**
   * {@inheritdoc}
   */
  public function setExchangeRates(array $rates) {
    $this->exchangeRates = $rates;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getExchangeRates() {
    return $this->exchangeRates;
  }

  /**
   * {@inheritdoc}
   */
  public function setLabel($label) {
    $this->label = $label;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRoundingStep($step) {
    $this->roundingStep = $step;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setSign($sign) {
    $this->sign = $sign;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSign() {
    return $this->sign;
  }

  /**
   * {@inheritdoc}
   */
  public function setSubunits($subunits) {
    $this->subunits = $subunits;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSubunits() {
    return $this->subunits;
  }

  /**
   * {@inheritdoc}
   */
  public function setUsages(array $usages) {
    $this->usages = $usages;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUsages() {
    return $this->usages;
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->currencyCode;
  }

  /**
   * {@inheritdoc}
   */
  function uri() {
    $uri = array(
      'options' => array(
        'entity' => $this,
        'entity_type' => $this->entityType,
      ),
      'path' => 'admin/config/regional/currency/' . $this->id(),
    );

    return $uri;
  }

  /**
   * {@inheritdoc}
   */
  public function getDecimals() {
    $decimals = 0;
    if ($this->getSubunits() > 0) {
      $decimals = 1;
      while (pow(10, $decimals) < $this->getSubunits()) {
        $decimals++;
      }
    }

    return $decimals;
  }

  /**
   * {@inheritdoc}
   *
   * @todo Inject the entity manager service when
   * http://drupal.org/node/1863816 is fixed.
   */
  public static function options() {
    $options = array();
    /** @var \Drupal\currency\Entity\CurrencyInterface[] $currencies */
    $currencies = entity_load_multiple('currency');
    foreach ($currencies as $currency) {
      // Do not show disabled currencies.
      if ($currency->status()) {
        $options[$currency->id()] = t('@currency_title (@currency_code)', array(
          '@currency_title' => $currency->label(),
          '@currency_code' => $currency->id(),
        ));
      }
    }
    natcasesort($options);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  function formatAmount($amount, $use_currency_precision = TRUE) {
    if ($use_currency_precision && $this->getSubunits()) {
      // Round the amount according the currency's configuration.
      $amount = $this->getMath()->round($amount, $this->getRoundingStep());

      $decimal_mark_position = strpos($amount, '.');
      // The amount has no decimals yet, so add a decimal mark.
      if ($decimal_mark_position === FALSE) {
        $amount .= '.';
      }
      // Remove any existing trailing zeroes.
      $amount = rtrim($amount, '0');
      // Add the required number of trailing zeroes.
      $amount_decimals = strlen(substr($amount, $decimal_mark_position + 1));
      if ($amount_decimals < $this->getDecimals()) {
        $amount .= str_repeat('0', $this->getDecimals() - $amount_decimals);
      }
    }

    return $this->getCurrencyAmountFormatterManager()->getDefaultPlugin()->formatAmount($this, $amount);
  }

  /**
   * Gets the currency amount formatter manager.
   *
   * @return \Drupal\currency\Plugin\Currency\AmountFormatter\AmountFormatterManagerInterface
   */
  protected function getCurrencyAmountFormatterManager() {
    return \Drupal::service('plugin.manager.currency.amount_formatter');
  }

  /**
   * Gets the math service.
   *
   * @return \Drupal\currency\MathInterface
   */
  protected function getMath() {
    return \Drupal::service('currency.math');
  }

  /**
   * {@inheritdoc}
   */
  function getRoundingStep() {
    if (is_numeric($this->roundingStep)) {
      return $this->roundingStep;
    }
    // If a rounding step was not set explicitly, the rounding step is equal
    // to one subunit.
    elseif (is_numeric($this->getSubunits())) {
      return $this->getSubunits() > 0 ? $this->getMath()->divide(1, $this->getSubunits()) : 1;
    }
  }

  /**
   * {@inheritdoc}
   */
  function isObsolete($reference = NULL) {
    // Without usage information, we cannot know if the currency is obsolete.
    if (!$this->getUsages()) {
      return FALSE;
    }

    // Default to the current date and time.
    if (is_null($reference)) {
      $reference = time();
    }

    // Mark the currency obsolete if all usages have an end date that comes
    // before $reference.
    $obsolete = 0;
    foreach ($this->getUsages() as $usage) {
      if ($usage->getEnd()) {
        $to = strtotime($usage->getEnd());
        if ($to !== FALSE && $to < $reference) {
          $obsolete++;
        }
      }
    }
    return $obsolete == count($this->getUsages());
  }

  /**
   * {@inheritdoc}
   */
  public function getExportProperties() {
    $properties['alternativeSigns'] = $this->getAlternativeSigns();
    $properties['currencyCode'] = $this->id();
    $properties['currencyNumber'] = $this->getCurrencyNumber();
    $properties['exchangeRates'] = $this->getExchangeRates();
    $properties['label'] = $this->label();
    $properties['roundingStep'] = $this->roundingStep;
    $properties['sign'] = $this->getSign();
    $properties['subunits'] = $this->getSubunits();
    $properties['status'] = $this->status();
    $properties['usage'] = array();
    foreach ($this->getUsages() as $usage) {
      $properties['usage'][] = array(
        'start' => $usage->getStart(),
        'end' => $usage->getEnd(),
        'countryCode' => $usage->getCountryCode(),
      );
    }
    $properties['uuid'] = $this->uuid();

    return $properties;
  }
}
