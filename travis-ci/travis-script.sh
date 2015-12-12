#!/bin/bash

set -e $DRUPAL_TI_DEBUG

# Run PHPUnit tests and submit code coverage statistics.
drupal_ti_ensure_drupal
drupal_ti_ensure_module_linked
cd $DRUPAL_TI_DRUPAL_DIR
cd $DRUPAL_TI_MODULES_PATH
cd $DRUPAL_TI_MODULE_NAME
mkdir -p build/logs
$DRUPAL_TI_DRUPAL_DIR/vendor/bin/phpunit -c ./phpunit.xml.dist --bootstrap $DRUPAL_TI_DRUPAL_DIR/core/tests/bootstrap.php --verbose --debug --coverage-clover ./build/logs/clover.xml || exit 1
