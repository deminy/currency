<?php

/**
 * @file
 * Definition of Drupal\currency\Entity\Currency.
 */

namespace Drupal\currency\Entity;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\currency\Entity\CurrencyInterface;

/**
 * Defines a currency entity class.
 *
 * @EntityType(
 *   config_prefix = "currency.currency",
 *   controllers = {
 *     "access" = "Drupal\currency\Entity\AccessController",
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
 *   label = @Translation("Currency"),
 *   module = "currency"
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
   * This currency's usage.
   *
   * @var array
   *   An array of \Drupal\currency\Usage objects.
   */
  protected $usage = array();

  /**
   * The UUID for this entity.
   *
   * @var string
   */
  public $uuid = NULL;

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
   *
   * @see self::id()
   */
  public function setCurrencyCode($code) {
    $this->currencyCode = $code;

    return $this;
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
  public function setUsage(array $usage) {
    $this->usage = $usage;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUsage() {
    return $this->usage;
  }

  /**
   * Overrides parent::id().
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
    if ($this->subunits > 0) {
      $decimals = 1;
      while (pow(10, $decimals) < $this->subunits) {
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
    foreach (entity_load_multiple('currency') as $currency) {
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
  function format($amount) {
    return \Drupal::service('currency.locale_delegator')->getLocalePattern()->format($this, $amount);
  }

  /**
   * {@inheritdoc}
   */
  function getRoundingStep() {
    if (is_numeric($this->roundingStep)) {
      return $this->roundingStep;
    }
    // If a rounding step was not set explicitely, the rounding step is equal
    // to one subunit.
    elseif (is_numeric($this->subunits)) {
      return $this->subunits > 0 ? bcdiv(1, $this->subunits, CURRENCY_BCMATH_SCALE) : 1;
    }
  }

  /**
   * {@inheritdoc}
   */
  function roundAmount($amount) {
    $rounding_step = $this->getRoundingStep();
    $decimals = $this->getDecimals();

    return bcmul(round(bcdiv($amount, $rounding_step, CURRENCY_BCMATH_SCALE)), $rounding_step, $decimals);
  }

  /**
   * {@inheritdoc}
   */
  function isObsolete($reference = NULL) {
    // Without usage information, we cannot know if the currency is obsolete.
    if (!$this->usage) {
      return FALSE;
    }

    // Default to the current date and time.
    if (is_null($reference)) {
      $reference = time();
    }

    // Mark the currency obsolete if all usages have an end date before that
    // comes before $reference.
    $obsolete = 0;
    foreach ($this->usage as $usage) {
      if ($usage->usageTo) {
        $to = strtotime($usage->usageTo);
        if ($to !== FALSE && $to < $reference) {
          $obsolete++;
        }
      }
    }
    return $obsolete == count($this->usage);
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
    $properties['roundingStep'] = $this->getRoundingStep();
    $properties['sign'] = $this->getSign();
    $properties['subunits'] = $this->getSubunits();
    $properties['status'] = $this->status();
    $properties['usage'] = $this->getUsage();
    $properties['uuid'] = $this->uuid();

    return $properties;
  }
}
