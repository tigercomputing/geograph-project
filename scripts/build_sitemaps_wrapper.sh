#!/bin/sh
#the following is based on one used on geograph.org.uk - ideally could do with making general. 


sudo -u www-data rm -f /var/www/geograph_live/public_html/sitemap/root/sitemap*.xml.gz

sudo -u www-data /usr/bin/php /var/www/geograph_live/scripts/build_sitemap.php --dir=/var/www/geograph_live

GET http://www.google.com/webmasters/tools/ping?sitemap=http%3A%2F%2Fwww.geograph.org.uk%2Fsitemap.xml


sudo -u www-data rm -f /var/www/geograph_live/public_html/kml/sitemap*.xml.gz

sudo -u www-data /usr/bin/php /var/www/geograph_live/scripts/build_kmlsitemap.php --dir=/var/www/geograph_live

GET http://www.google.com/webmasters/tools/ping?sitemap=http%3A%2F%2Fkml.geograph.org.uk%2Fkml%2Fsitemap.xml


sudo -u www-data rm -f /var/www/geograph_live/public_html/sitemap/sitemap*.xml.gz

sudo -u www-data /usr/bin/php /var/www/geograph_live/scripts/build_htmlsitemap.php --dir=/var/www/geograph_live

GET http://www.google.com/webmasters/tools/ping?sitemap=http%3A%2F%2Fwww.geograph.org.uk%2Fsitemap%2Fsitemap.xml

echo ""
echo ""
echo ""
