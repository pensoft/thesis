DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
export DOCUMENT_ROOT=$DIR/../pwt
/usr/bin/php $DIR/generate_article_cache.php  ${1} 2>&1 >> /tmp/article_cache.log