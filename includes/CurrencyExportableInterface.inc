<?php

/**
 * @file
 * Contains interface CurrencyExportableInterface.
 */

/**
 * Adds functionality to Currency's Ctools exportable objects.
 */
interface CurrencyExportableInterface {

  /**
   * Validates the exportable.
   *
   * @throws Exception
   */
  function validate();
}