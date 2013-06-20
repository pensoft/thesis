#!/bin/bash
cd /var/www/webs/pensoft.eu/etalig/production.pmt/code/scripts
DOCUMENT_ROOT=/var/www/webs/pensoft.eu/etalig/production.pmt/code/adm/
export DOCUMENT_ROOT

php import_profile_log.php 2>&1 >> /tmp/import_profile_log.log


