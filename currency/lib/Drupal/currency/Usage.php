<?php

/**
 * @file
 * Contains \Drupal\currency\Usage.
 */

namespace Drupal\currency;

/**
 * Describes a currency's usage in a country.
 */
class Usage implements UsageInterface {

  /**
   * The ISO 8601 datetime of the moment this usage started.
   *
   * @var string
   */
  protected $start;

  /**
   * The ISO 8601 datetime of the moment this usage ended.
   *
   * @var string
   */
  protected $end;

  /**
   * An ISO 3166-1 alpha-1 country code.
   *
   * @var string
   */
  protected $countryCode;

  /**
   * {@inheritdoc}
   */
  public function getStart() {
    return $this->start;
  }

  /**
   * {@inheritdoc}
   */
  public function setStart($datetime) {
    $this->start = $datetime;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getEnd() {
    return $this->end;
  }

  /**
   * {@inheritdoc}
   */
  public function setEnd($datetime) {
    $this->end = $datetime;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryCode() {
    return $this->countryCode;
  }

  /**
   * {@inheritdoc}
   */
  public function setCountryCode($country_code) {
    $this->countryCode = $country_code;

    return $this;
  }
}
