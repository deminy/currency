<?php

use Drupal\Component\Utility\NestedArray;
use Symfony\Component\Yaml\Yaml;

require_once '/opt/local/apache2/htdocs/code/drupal/core/8/core/vendor/autoload.php';

$filenames = glob(__DIR__ . '/config/default/currency.currency.*.yml');

$rates = [];

foreach ($filenames as $filename) {
  $currency_data = Yaml::parse(file_get_contents($filename));
  $rates = NestedArray::mergeDeep($rates, [
    $currency_data['currencyCode'] => $currency_data['exchangeRates'],
  ]);
  ksort($rates[$currency_data['currencyCode']]);
  unset($currency_data['exchangeRates']);
  file_put_contents($filename, Yaml::dump($currency_data));
}
ksort($rates);
file_put_contents(__DIR__ . '/historical_exchange_rates.yml', Yaml::dump(array_filter($rates)));
