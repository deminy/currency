#!/usr/bin/env bash

php core/scripts/run-tests.sh --verbose --color --concurrency 4 --php `which php` --url http://localhost "Currency" "Currency Intl" | tee /tmp/test.txt
# Simpletest does not exit with code 0 on success, so we will need to analyze
# the output to ascertain whether the tests passed.
TEST_EXIT=${PIPESTATUS[0]}
TEST_SIMPLETEST=$(! egrep -i "([0-9]+ fails)|(PHP Fatal error)|([0-9]+ exceptions)" /tmp/test.txt > /dev/null)$?
if [ $TEST_EXIT -eq 0 ] && [ $TEST_SIMPLETEST -eq 0 ]; then exit 0; else exit 1; fi
