#!/bin/bash

cd /var/www/pensoft/production.pmt/code/scripts
php GenerateTaxonCache.php  2>&1 >> /tmp/pmt_taxon_cache.log