#!/bin/bash
cd /var/www/pensoft/production.pmt/code/scripts
DOCUMENT_ROOT=/var/www/pensoft/production.pmt/code/pjs/
export DOCUMENT_ROOT

php email_messenger.php
