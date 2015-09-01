ssh root@saxid.zih.tu-dresden.de rm -rf /srv/www/htdocs/saxid-ldap-proxy/*
pscp -r -p * root@SaxID-LDAP-Proxy:/srv/www/htdocs/saxid-ldap-proxy/
pscp .htaccess root@SaxID-LDAP-Proxy:/srv/www/htdocs/saxid-ldap-proxy/
ssh root@saxid.zih.tu-dresden.de chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy