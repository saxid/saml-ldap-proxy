#!/bin/bash
echo "Creating Archive ..."
rm -f saxidprox_depl.tar.bz2
tar cjf saxidprox_depl.tar.bz2 *
#bzip2 saxidprox_depl.tar
#tar uf saxidprox_depl.tar *
echo "Deploying Archive to VM ..."
echo "... deleting old content"
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/*
# -r recursive and -p preserve -> time...
echo "... copy new"
scp saxidprox_depl.tar.bz2 root@saxid:/srv/www/htdocs/saxid-ldap-proxy
echo "Configuring VM ..."
echo "... extracting"
ssh root@saxid.zih.tu-dresden.de tar xjf /srv/www/htdocs/saxid-ldap-proxy/saxidprox_depl.tar.bz2 -C /srv/www/htdocs/saxid-ldap-proxy
echo "... removing Archive on Server"
ssh root@saxid.zih.tu-dresden.de rm -f /srv/www/htdocs/saxid-ldap-proxy/saxidprox_depl.tar.bz2
echo "... removing .git"
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/.git
echo "... set owner to wwwrun:www"
ssh root@saxid.zih.tu-dresden.de chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy
ssh root@saxid.zih.tu-dresden.de chmod u+x /srv/www/htdocs/saxid-ldap-proxy/app/console
echo "... removing logs and cache"
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/app/logs/*
ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/app/cache/*
echo "... installing assets & clearing cache for prod + setting user wwwrun:www"
ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console assets:install --symlink -- /srv/www/htdocs/saxid-ldap-proxy/web
ssh root@saxid.zih.tu-dresden.de php /srv/www/htdocs/saxid-ldap-proxy/app/console cache:clear --env=prod
ssh root@saxid.zih.tu-dresden.de chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy/
echo "ALL Done!"

#HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
#setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
#setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
