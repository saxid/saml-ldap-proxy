<?php

/**
 * SAML 2.0 remote IdP metadata for simpleSAMLphp.
 *
 * Remember to remove the IdPs you don't use from this file.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-idp-remote 
 */

/*
 * Guest IdP. allows users to sign up and register. Great for testing!
 */
$metadata['https://openidp.feide.no'] = array(
	'name' => array(
		'en' => 'Feide OpenIdP - guest users',
		'no' => 'Feide Gjestebrukere',
	),
	'description'          => 'Here you can login with your account on Feide RnD OpenID. If you do not already have an account on this identity provider, you can create a new one by following the create new account link and follow the instructions.',

	'SingleSignOnService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SSOService.php',
	'SingleLogoutService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SingleLogoutService.php',
	'certFingerprint'      => 'c9ed4dfb07caf13fc21e0fec1572047eb8a7a4cb'
);

$metadata['https://idp2-test.tu-dresden.de/idp/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Dresden (Test)',
	),
	'description'          => 'Dies ist der Test-Shibboleth-IdP der Technischen Universität Dresden.',
	'SingleSignOnService'  => 'https://idp2-test.tu-dresden.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHBjCCBe6gAwIBAgIHGOrIuCF8XDANBgkqhkiG9w0BAQsFADCBhTELMAkGA1UE BhMCREUxKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0YWV0IERyZXNkZW4x DDAKBgNVBAsTA1pJSDEcMBoGA1UEAxMTVFUgRHJlc2RlbiBDQSAtIEcwMjEgMB4G CSqGSIb3DQEJARYRcGtpQHR1LWRyZXNkZW4uZGUwHhcNMTUwMTMwMDkxMzEyWhcN MTgwMTI5MDkxMzEyWjCBizELMAkGA1UEBhMCREUxEDAOBgNVBAgTB1NhY2hzZW4x EDAOBgNVBAcTB0RyZXNkZW4xKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0 YWV0IERyZXNkZW4xDDAKBgNVBAsTA1pJSDEgMB4GA1UEAxMXaWRwMi10ZXN0LnR1 LWRyZXNkZW4uZGUwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQDKGPM5 fR92jvJYevU22X1YeIToJX1n3kQ0KLjkVrngBF+nXgJIRn08Rs2voz4t7GDuCb9T nzDk1w9t4EKZfEFKZq1+XVOEu1sxqfpYuTgOFLIXlfIyvJg+QuiqXOIVf+RpfevR uo8J+9A1c3lvcCeOm+kmHnGKCtJH716o2k+lvpVAf/AUO9CHDhFCVrw2Ic8ZrKwd ZmcoiqEqoMCqpKNjn7zfjnIfvWT64Jk+1Q9FFxrGX6hXRPZblsr06osLFrMoCzBl YlrJlmvSSSlSSl4/JQyPEnef1ViGv3ytCMu7N+zUOdIxCJKZPZJ4nCXvqn5GHCEl fxT3bxBzFybIcZAHQNI96pQi6Jn0G7aDk9lEbVkuuVMpHubfVqTPteR/rYartNIi IR1LALqwRalKadD5HdGB3iwVX8LZV9TTbxo10FHAbCfauMQ+5gP+CbJPDwl/4ZQP Q1WdRdgbKd26ocbnjhGNFz2m47mP0OHYmXl3pyxwjhMsH7wTqQ21/LIpZCJVpn74 YzWWXwQB9er4Ve9JQQPqncGN5FEw4TyqTGwilvFuHjkw+Nn4byDmk2YHytFb7pFt igyUJHgcpeOqAUeUVY/TwtBjPWdFM0HUWvW5S68BtK8F/BMtcznTb1dsYi0Woagr z3QLcZ5dVV34TfqVUVi/4u1Nff0czmZhHSYSTwIDAQABo4ICcTCCAm0wTwYDVR0g BEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYPKwYBBAGBrSGCLAIBBAMBMA8GDSsG AQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4wCQYDVR0TBAIwADALBgNVHQ8EBAMC BeAwNAYDVR0lBC0wKwYIKwYBBQUHAwIGCCsGAQUFBwMBBgorBgEEAYI3CgMDBglg hkgBhvhCBAEwHQYDVR0OBBYEFOdsz7Ujx9TzOk7c2OV/pi6vIpAkMB8GA1UdIwQY MBaAFMUrU5MXg8n1RkLtQ2rftoCmR/LgMCIGA1UdEQQbMBmCF2lkcDItdGVzdC50 dS1kcmVzZGVuLmRlMIGLBgNVHR8EgYMwgYAwPqA8oDqGOGh0dHA6Ly9jZHAxLnBj YS5kZm4uZGUvdHUtZHJlc2Rlbi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMD6gPKA6 hjhodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWRyZXNkZW4tY2EvcHViL2NybC9n X2NhY3JsLmNybDCB2QYIKwYBBQUHAQEEgcwwgckwMwYIKwYBBQUHMAGGJ2h0dHA6 Ly9vY3NwLnBjYS5kZm4uZGUvT0NTUC1TZXJ2ZXIvT0NTUDBIBggrBgEFBQcwAoY8 aHR0cDovL2NkcDEucGNhLmRmbi5kZS90dS1kcmVzZGVuLWNhL3B1Yi9jYWNlcnQv Z19jYWNlcnQuY3J0MEgGCCsGAQUFBzAChjxodHRwOi8vY2RwMi5wY2EuZGZuLmRl L3R1LWRyZXNkZW4tY2EvcHViL2NhY2VydC9nX2NhY2VydC5jcnQwDQYJKoZIhvcN AQELBQADggEBACP9bSImK3ajFyxhTgqLPdFDFeK++yOUBwOVxlPPZyGcR+PwTMQm npldVGtWJPDNX7WSvKVS2hbktdEZnRnXIMyuZdpOSGuJ/DCqKMBFKWk6aRcF32cY GBFN8iRhhWoKP0AZpF2J3Du8HBl9Dr6rE+YiTEyXzVsa6deBbv4Fswko0tqV9ADo 9ZaXFXBRxrMXN77b9+rYd5Ue/0QXVG6u2tvah7LDpTi8fqCTuW85SGrustocAoiZ IAew/pI646xuS5i0wOW2D9l3FoY2P4od+JrPhAMT5OU1ORyFUA2xsAF1CflqH1L7 HejZk/7rC81HioNRw8I7SCrHCxt7pqyTgEQ=',
);

