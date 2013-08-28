DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
export DOCUMENT_ROOT=$DIR/../pwt
/usr/bin/php $DIR/gettaxonpreviewcache.php  ${1} 2>&1 >> /tmp/taxon_preview_cache.log
