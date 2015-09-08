# SaxID-LDAP-Proxy #

Dieses Repository beinhaltet den Quellcode und Anleitungen zur Installation und Einrichtung eines LDAP-Proxies. Dieser agiert als Shibboleth ServiceProvider und schreibt Benutzerattribute in ein LDAP.

## Installation ##

Im folgenden wird als Hostsystem ein SLES 12.02 (x64) angenommen. Die Systemkonfiguration kann bei anderen Systemen entsprechend abweichen.

1. [Systemeinrichtung](#system)
1. [Installtion benötigter Pakete](#pakete)
1. [Apache-Konfiguration](#apache)
1. [LDAP-Konfiguration](#ldap)
1. [Installation PHP-Repository](#php)
1. [Optionale Installationen](#optional)

### <a name="system"></a>Systemeinrichtung ###

1. Wenn als VM
   1. Oracle [Virtual Box](https://www.virtualbox.org/) installieren
   1. SLES 12.02 installieren
   1. Netzwerk in Virtual Box einrichten (2 Adapter: 1x NAT, 1x Host only), dann in SLES einrichten (statische IP empfohlen)

### <a name="pakete"></a>Installation benötigter Pakete ###

1. Apache2 installieren: `zypper in apache2`
1. PHP5 installieren: `zypper in php5`
1. Apache2 als Service einrichten und Port(s) in Firewall freigeben
1. [`mod_rewrite` auf Apache2 aktivieren](http://askubuntu.com/questions/48362/how-to-enable-mod-rewrite-in-apache)
1. ZLIB-Library installieren: `zypper in php-zlib`
1. LDAP installieren: `zypper in openldap2`
1. OpenLDAP Pakete für PHP5 installieren: `zypper in php-ldap`

### <a name="apache"></a>Apache-Konfiguration ###

1. Virtual Host einrichten: die Environment-Variablen `SIMPLESAMLPHP_*_DIR`/`SAXIDLDAPPROXY_LOG_DIR` mit Verweis auf die entsprechenden Konfigurationsdateien für SimpleSAMLphp sind obligatorisch.

        Alias /simplesaml /srv/www/htdocs/saxid-ldap-proxy/vendor/simplesamlphp/simplesamlphp/www
        <VirtualHost *:443>
            ServerName saxid.zih.tu-dresden.de
            DocumentRoot /srv/www/htdocs/saxid-ldap-proxy
            
            SSLEngine on
            SSLCertificateKeyFile /etc/apache2/ssl.key/saxid.zih.tu-dresden.de.key.pem
            SSLCertificateFile /etc/apache2/ssl.crt/saxid.zih.tu-dresden.de.pem
            SSLCertificateChainFile /etc/apache2/ssl.crt/chain.txt

            SetEnv SIMPLESAMLPHP_CONFIG_DIR /var/simplesamlphp/config
            SetEnv SIMPLESAMLPHP_METADATA_DIR /var/simplesamlphp/metadata
            SetEnv SIMPLESAMLPHP_CERT_DIR /var/simplesamlphp/cert
            SetEnv SIMPLESAMLPHP_LOG_DIR /var/log/simplesamlphp
            SetEnv SAXIDLDAPPROXY_LOG_DIR /var/log/saxid-ldap-proxy
        </VirtualHost>

#### Hinweise ####

* Apache2 benötigt für die `SetEnv`-Anweisung das Modul [`mod_env`](http://httpd.apache.org/docs/2.2/mod/mod_env.html).
* Alternativ zu der `Alias`-Anweisung kann natürlich auch ein Symlink (`ln -s`) gesetzt werden.

### <a name="ldap"></a>LDAP-Konfiguration ###

Hinweis: Die LDIF-Dateien liegen im Repository-Folder `src/Saxid/SaxidLdapProxyBundle/Resources/schema`.

1. LDAP-Fremdschemata importieren ([mindestens `eduPerson`, `SCHAC`, `dfnEduPerson`](https://www.aai.dfn.de/der-dienst/attribute/))
        
        ldapadd -Q -Y EXTERNAL -H ldapi:/// -W -f file.ldif

1. SaxID-Hochschulen (= LDAP-Organisationen) importieren

        ldapadd -Q -Y EXTERNAL -H ldapi:/// -W -f saxid-organisations.ldif

### <a name="php"></a>Installation PHP-Repository ###

1. SaxID-LDAP-Proxy-Repository beziehen (z.B. gitlab@TUC) und installieren (`composer install`)
1. Shibboleth ServiceProvider beim lokalen Shibboleth IdP bekannt machen

### <a name="optional"></a>Optionale Installationen ###

[Apache Directory Studio](https://directory.apache.org/studio/) installieren

