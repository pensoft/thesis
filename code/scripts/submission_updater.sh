#!/bin/bash
cd /var/www/pensoft/production1.pmt/code/scripts
DOCUMENT_ROOT=/var/www/pensoft/production1.pmt/code/pjs/
export DOCUMENT_ROOT

php submission_updater.php
