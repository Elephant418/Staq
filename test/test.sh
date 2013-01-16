mkdir -p vendor/pixel418/staq
rm -rf vendor/pixel418/staq/src
cp -r src vendor/pixel418/staq/src
php test/index.php