ssh root@192.168.56.102 rm -rf /srv/www/htdocs/saxid-ldap-proxy/*
pscp -r -p * root@SaxID-LDAP-Proxy-locale-VM:/srv/www/htdocs/saxid-ldap-proxy/
pscp .htaccess root@SaxID-LDAP-Proxy-locale-VM:/srv/www/htdocs/saxid-ldap-proxy/
ssh root@192.168.56.102 chown -R wwwrun:www /srv/www/htdocs/saxid-ldap-proxy