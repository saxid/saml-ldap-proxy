<?php

echo "<h1>AHHHH</h1>";

$ldap = ldap_connect('ldap://127.0.0.1', 389) or die('geht nicht');

ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);

$bind = ldap_bind($ldap, 'cn=admin,dc=sax-id,dc=de', 'saxidsaxid');

echo "<pre>";

var_dump(array('error' => ldap_error($ldap)));

var_dump(array('Connection' => $ldap, 'Bind' => $bind));

$person    = 'Hesse';

$dn        = "o=tu-dresden.de,dc=sax-id,dc=de";
$filter    = "(|(sn={$person}*))";
$justthese = array();

$sr        = ldap_search($ldap, $dn, $filter, $justthese);

if(false !== $sr) {
	$info      = ldap_get_entries($ldap, $sr);
} else {
	var_dump('nothing found');
}

print (int) $info["count"]." gefundene Einträge<p>";


