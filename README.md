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

Die Anpassungen an der Apache-Konfiguration befinden sich in der Datei
        /etc/apache2/httpd.conf.local

1. Virtual Host einrichten: die Environment-Variablen `SIMPLESAMLPHP_*_DIR`/`SAXIDLDAPPROXY_LOG_DIR` mit Verweis auf die entsprechenden Konfigurationsdateien für SimpleSAMLphp sind obligatorisch.

        Alias /simplesaml /srv/www/htdocs/saxid-ldap-proxy/vendor/simplesamlphp/simplesamlphp/www
        <VirtualHost /n*:443>
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

1. /etc/openldap/ldap.conf

        host    localhost
        base    dc=sax-id,dc=de
        binddn  cn=admin,dc=sax-id,dc=de

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

# Apache-Config

Die Anpassungen an der Apache-Konfiguration finden sich in der Datei `/etc/apache2/httpd.conf.local`

# SSH-Zugang

Eigenes SSH-Zertifikat muss an Matthias Jurenz geschickt werden, um Zugriff auf die SaxID-LDAP-Proxy-VM zu erhalten sowie Info bzgl. Freischaltung IP

# Deployment

Ich (Moritz) habe stets lokal entwickelt, und dann per (P)SCP auf die VM übertragen. Hierfür habe ich 3 verschiedene Shell-Scripte verwendet:

* `deploy-vm.sh`: Deployment gesamter Folder
* `deploy-vm-src.sh`: Deployment nur `src/`-Folder
* `deploy-vm-config.sh`: Deployment nur `app/config/`-Folder

Ich (Jan) habe ein neues deploy-Script geschrieben, bei der das gesamte Verzeichnis vor der Übertragung komprimiert wird und auf dem Server wieder entpackt wird.

* `deploy.sh` : Deployment zipped (bz2) Folder

# Composer

Paket- und Dependencymanagement für PHP. Ist auf VM unter `/opt/composer` installiert. Nach clonen des git-Repositories müssen die externen Pakete mit `composer install` hinzugefügt werden.

# Assets

Roh-Assets sind in `src/Saxid/SaxidLdapProxyBundle/Resource/public` und werden per Befehl

    php app/console assets:install --symlink

in den Order `web/bundles/...` compiliert. Wenn die Assets ohne `--symlinks` installiert werden, muss der Befehl nach jeder Änderung in `src/` aufgerufen werden.

# Cache

Prüfen ob der Cache vom Webserver beschreibbar ist und setzen des "Webserver-Users"!

    chown -R wwwrun:www app/cache


## reg-app (bwIDM) Wildfly 8.2.1 SSL https Config ##

check URL for making and installing reg-app -> https://git.scc.kit.edu/simon/reg-app/wikis/pages

Generate a Certificate Signing Request (CSR)

    $ openssl req -new -newkey rsa:2048 -nodes -keyout m016.zih.tu-dresden.de.key -out m016.zih.tu-dresden.de.csr

Generate a Self-Signed SSL Certificate

    $ openssl x509 -req -days 365 -in m016.zih.tu-dresden.de.csr -signkey m016.zih.tu-dresden.de.key -out m016.zih.tu-dresden.de.crt

Once you have these you need to create a Java keystore file. This is a two step process. First creating a pkcs12 file from your SSL certificate and then importing that into a keystore file.

    $ openssl pkcs12 -export -in m016.zih.tu-dresden.de.crt -inkey m016.zih.tu-dresden.de.key -out m016.zih.tu-dresden.de.p12 -name default -caname root
    $ keytool -importkeystore -deststorepass saxid -destkeypass saxid -destkeystore m016.zih.tu-dresden.de.jks -srckeystore m016.zih.tu-dresden.de.p12 -srcstoretype PKCS12 -srcstorepass saxid -alias default

Copy the new keystore file to the your Wildfly configuration directory

    sudo cp yourdomain.com.jks /usr/local/opt/wildfly-8.1.0.Final/standalone/configuration/

Insert the following into your standalone.xml in the security-realms section.

        <security-realm name="SslRealm">
          <server-identities>
            <ssl>
                <keystore path="yourdomain.com.jks" relative-to="jboss.server.config.dir" keystore-password="<secret password>"/>
            </ssl>
          </server-identities>
        </security-realm>

Add the following line to the server section of the standalone.xml file.

        <https-listener name="default-ssl" socket-binding="https" security-realm="SslRealm"/>

After restarting Wildfly you should now have functioning HTTPS.
