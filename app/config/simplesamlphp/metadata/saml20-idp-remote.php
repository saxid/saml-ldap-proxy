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
		'de' => 'Shibboleth-IdP TU Dresden (Test v2)',
	),
	'description'          => 'Dies ist der v2 Test-Shibboleth-IdP der Technischen Universität Dresden.',
	'SingleSignOnService'  => 'https://idp2-test.tu-dresden.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHBjCCBe6gAwIBAgIHGOrIuCF8XDANBgkqhkiG9w0BAQsFADCBhTELMAkGA1UE BhMCREUxKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0YWV0IERyZXNkZW4x DDAKBgNVBAsTA1pJSDEcMBoGA1UEAxMTVFUgRHJlc2RlbiBDQSAtIEcwMjEgMB4G CSqGSIb3DQEJARYRcGtpQHR1LWRyZXNkZW4uZGUwHhcNMTUwMTMwMDkxMzEyWhcN MTgwMTI5MDkxMzEyWjCBizELMAkGA1UEBhMCREUxEDAOBgNVBAgTB1NhY2hzZW4x EDAOBgNVBAcTB0RyZXNkZW4xKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0 YWV0IERyZXNkZW4xDDAKBgNVBAsTA1pJSDEgMB4GA1UEAxMXaWRwMi10ZXN0LnR1 LWRyZXNkZW4uZGUwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQDKGPM5 fR92jvJYevU22X1YeIToJX1n3kQ0KLjkVrngBF+nXgJIRn08Rs2voz4t7GDuCb9T nzDk1w9t4EKZfEFKZq1+XVOEu1sxqfpYuTgOFLIXlfIyvJg+QuiqXOIVf+RpfevR uo8J+9A1c3lvcCeOm+kmHnGKCtJH716o2k+lvpVAf/AUO9CHDhFCVrw2Ic8ZrKwd ZmcoiqEqoMCqpKNjn7zfjnIfvWT64Jk+1Q9FFxrGX6hXRPZblsr06osLFrMoCzBl YlrJlmvSSSlSSl4/JQyPEnef1ViGv3ytCMu7N+zUOdIxCJKZPZJ4nCXvqn5GHCEl fxT3bxBzFybIcZAHQNI96pQi6Jn0G7aDk9lEbVkuuVMpHubfVqTPteR/rYartNIi IR1LALqwRalKadD5HdGB3iwVX8LZV9TTbxo10FHAbCfauMQ+5gP+CbJPDwl/4ZQP Q1WdRdgbKd26ocbnjhGNFz2m47mP0OHYmXl3pyxwjhMsH7wTqQ21/LIpZCJVpn74 YzWWXwQB9er4Ve9JQQPqncGN5FEw4TyqTGwilvFuHjkw+Nn4byDmk2YHytFb7pFt igyUJHgcpeOqAUeUVY/TwtBjPWdFM0HUWvW5S68BtK8F/BMtcznTb1dsYi0Woagr z3QLcZ5dVV34TfqVUVi/4u1Nff0czmZhHSYSTwIDAQABo4ICcTCCAm0wTwYDVR0g BEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYPKwYBBAGBrSGCLAIBBAMBMA8GDSsG AQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4wCQYDVR0TBAIwADALBgNVHQ8EBAMC BeAwNAYDVR0lBC0wKwYIKwYBBQUHAwIGCCsGAQUFBwMBBgorBgEEAYI3CgMDBglg hkgBhvhCBAEwHQYDVR0OBBYEFOdsz7Ujx9TzOk7c2OV/pi6vIpAkMB8GA1UdIwQY MBaAFMUrU5MXg8n1RkLtQ2rftoCmR/LgMCIGA1UdEQQbMBmCF2lkcDItdGVzdC50 dS1kcmVzZGVuLmRlMIGLBgNVHR8EgYMwgYAwPqA8oDqGOGh0dHA6Ly9jZHAxLnBj YS5kZm4uZGUvdHUtZHJlc2Rlbi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMD6gPKA6 hjhodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWRyZXNkZW4tY2EvcHViL2NybC9n X2NhY3JsLmNybDCB2QYIKwYBBQUHAQEEgcwwgckwMwYIKwYBBQUHMAGGJ2h0dHA6 Ly9vY3NwLnBjYS5kZm4uZGUvT0NTUC1TZXJ2ZXIvT0NTUDBIBggrBgEFBQcwAoY8 aHR0cDovL2NkcDEucGNhLmRmbi5kZS90dS1kcmVzZGVuLWNhL3B1Yi9jYWNlcnQv Z19jYWNlcnQuY3J0MEgGCCsGAQUFBzAChjxodHRwOi8vY2RwMi5wY2EuZGZuLmRl L3R1LWRyZXNkZW4tY2EvcHViL2NhY2VydC9nX2NhY2VydC5jcnQwDQYJKoZIhvcN AQELBQADggEBACP9bSImK3ajFyxhTgqLPdFDFeK++yOUBwOVxlPPZyGcR+PwTMQm npldVGtWJPDNX7WSvKVS2hbktdEZnRnXIMyuZdpOSGuJ/DCqKMBFKWk6aRcF32cY GBFN8iRhhWoKP0AZpF2J3Du8HBl9Dr6rE+YiTEyXzVsa6deBbv4Fswko0tqV9ADo 9ZaXFXBRxrMXN77b9+rYd5Ue/0QXVG6u2tvah7LDpTi8fqCTuW85SGrustocAoiZ IAew/pI646xuS5i0wOW2D9l3FoY2P4od+JrPhAMT5OU1ORyFUA2xsAF1CflqH1L7 HejZk/7rC81HioNRw8I7SCrHCxt7pqyTgEQ=',
);

$metadata['https://idp3-test.tu-dresden.de/idp/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Dresden (Test v3)',
	),
	'description'          => 'Dies ist der v3 Test-Shibboleth-IdP der Technischen Universität Dresden.',
	'SingleSignOnService'  => 'https://idp3-test.tu-dresden.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHBjCCBe6gAwIBAgIHGhifNq+jtDANBgkqhkiG9w0BAQsFADCBhTELMAkGA1UE BhMCREUxKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0YWV0IERyZXNkZW4x DDAKBgNVBAsTA1pJSDEcMBoGA1UEAxMTVFUgRHJlc2RlbiBDQSAtIEcwMjEgMB4G CSqGSIb3DQEJARYRcGtpQHR1LWRyZXNkZW4uZGUwHhcNMTUwOTE2MDgwMDM5WhcN MTgwOTE1MDgwMDM5WjCBizELMAkGA1UEBhMCREUxEDAOBgNVBAgMB1NhY2hzZW4x EDAOBgNVBAcMB0RyZXNkZW4xKDAmBgNVBAoMH1RlY2huaXNjaGUgVW5pdmVyc2l0 YWV0IERyZXNkZW4xDDAKBgNVBAsMA1pJSDEgMB4GA1UEAwwXaWRwMy10ZXN0LnR1 LWRyZXNkZW4uZGUwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQDAAV1+ DSgvgl7fQnGoU72IGne7KsLkVrYDV0stAK+s6/RQ/Hrpy24dIax7r/FhRHAFlvHO 9x+luZ4xq84cQZ86S9+GMkOgHfmZDb3CuMSG9W3DIx7/7q1xYedtoUbPobPw7x/u vaxqzf36/NuX42o/8cdi4L4c7gIOgHek8MLMUWev89p2tk9WUbh5Jxw2LAvzLbSc v7Uu2QSqiiqhiyfcei1dyPUnmKAwOC/Q6Nae5+upZ2cVaLm/qD27kRU4TAWAnR2Y zwIu0vB6GVRgbZ3AStedYdK3OIULTW3/hyooBEhsPWp5KuyhQ5T33jljC4gyKw1M uIFNJ9W1mkHs2uMVDSzvD9TaTg6a9V8Noagu7pKuG6DPyfitXnvCXYu9HC2g++F8 4cleeJO6uky8PmJsyLPU0wE6WHmChw1jQGecER9mcGsWJvJAtfvQwvgwUGnWMFj8 U/lRlT6XFGoLKufH9e7d5Hk0xTeBb/Dv7Gt3rquJ/KJtAhJhbeSRb6wzhJ4P1Zlk PQXYXXy+FmDGAxAb4t+HJOJEJkaMkr47OusTaO9fuEQ/CDCJ4N3RzC+J1NRrBWms gHqL2xdTmpi0h576aFr7fcA9Nnhbme+3EgwkEMySMA752b5d2ZVzl85l6g/5yYlc fWEp9784qPxUIT5wWm+u/iErmtKfM+D4PYYG+QIDAQABo4ICcTCCAm0wTwYDVR0g BEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYPKwYBBAGBrSGCLAIBBAMBMA8GDSsG AQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4wCQYDVR0TBAIwADALBgNVHQ8EBAMC BeAwNAYDVR0lBC0wKwYIKwYBBQUHAwIGCCsGAQUFBwMBBgorBgEEAYI3CgMDBglg hkgBhvhCBAEwHQYDVR0OBBYEFFdIQqO86A2QTVxSUvwsAWcddf5ZMB8GA1UdIwQY MBaAFMUrU5MXg8n1RkLtQ2rftoCmR/LgMCIGA1UdEQQbMBmCF2lkcDMtdGVzdC50 dS1kcmVzZGVuLmRlMIGLBgNVHR8EgYMwgYAwPqA8oDqGOGh0dHA6Ly9jZHAxLnBj YS5kZm4uZGUvdHUtZHJlc2Rlbi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMD6gPKA6 hjhodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWRyZXNkZW4tY2EvcHViL2NybC9n X2NhY3JsLmNybDCB2QYIKwYBBQUHAQEEgcwwgckwMwYIKwYBBQUHMAGGJ2h0dHA6 Ly9vY3NwLnBjYS5kZm4uZGUvT0NTUC1TZXJ2ZXIvT0NTUDBIBggrBgEFBQcwAoY8 aHR0cDovL2NkcDEucGNhLmRmbi5kZS90dS1kcmVzZGVuLWNhL3B1Yi9jYWNlcnQv Z19jYWNlcnQuY3J0MEgGCCsGAQUFBzAChjxodHRwOi8vY2RwMi5wY2EuZGZuLmRl L3R1LWRyZXNkZW4tY2EvcHViL2NhY2VydC9nX2NhY2VydC5jcnQwDQYJKoZIhvcN AQELBQADggEBAK1xTD3O7xNlJJ2FnbfTGNRET3XZuqRO6BcaVS0GE5gsVm6PO0oJ PcVTvgzk7KwPYF6egn7EIETTFgQ3dlzIdNUnIOK3FoxVTZTdCunykzHI08eQDd1b KRMU8z/DGgtxcb2MbtZTnqqEUoApxDRWdZGfdOO2TYfwbG01qw8jxMyFjYF+YtRF 9fkEESG6e87KLnCsW/E48+HcYiP4BqBsPzCroAy2OhPIGFptye3AQME+z39Jm6L0 MUuYHc7NlYByAtT3QdXwE0TCuoAK9LYs4rBOa82/PRIT3+dItXeVa3i7GGCkHQ2N DUdIIKxIk3ElqB7mzbebaY1oLPveWe6Oi4w=',
);

$metadata['https://test-idp.hrz.tu-chemnitz.de/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Chemnitz (Test v2)',
	),
	'description'          => 'Dies ist der v2 Test-Shibboleth-IdP der Technischen Universität Chemnitz.',
	'SingleSignOnService'  => 'https://test-idp.hrz.tu-chemnitz.de/krb/saml2/idp/SSOService.php',
	'certData'             => 'MIIGQzCCBSugAwIBAgIHGZYTHYvG9TANBgkqhkiG9w0BAQsFADCBvTELMAkGA1UE BhMCREUxKTAnBgNVBAoTIFRlY2huaXNjaGUgVW5pdmVyc2l0YWV0IENoZW1uaXR6 MSMwIQYDVQQLExpVbml2ZXJzaXRhZXRzcmVjaGVuemVudHJ1bTE8MDoGA1UEAxMz VFUgQ2hlbW5pdHogQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkgLSBUVUMvVVJaIENB IEczMSAwHgYJKoZIhvcNAQkBFhFjYUB0dS1jaGVtbml0ei5kZTAeFw0xNTA2MDkw NzI4MTNaFw0xODA5MDUwNzI4MTNaMIGoMQswCQYDVQQGEwJERTEQMA4GA1UECAwH U2FjaHNlbjERMA8GA1UEBwwIQ2hlbW5pdHoxKTAnBgNVBAoMIFRlY2huaXNjaGUg VW5pdmVyc2l0YWV0IENoZW1uaXR6MSMwIQYDVQQLDBpVbml2ZXJzaXRhZXRzcmVj aGVuemVudHJ1bTEkMCIGA1UEAwwbdGVzdC1pZHAuaHJ6LnR1LWNoZW1uaXR6LmRl MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiqPC4MsKL+tJ6H2AClzF sOjaavyYHHLOdzNYyXKUQXick/FOTZeQdM9/gO61iQSdy7dog2EJ19G8umoMrCdt FVaLwFpIqqU2g3KCinwt0eSW8MedRR4PrLxgp7/r/+lT+u7fy9CNPzdTjGFeW1j1 UINxZxJbtOnhEFjqFL6JW5J++Flc5zQR7a5IB8yNya11P3++DIWZNaagqdt6KLKs qaPiaXMfnHZVAYFXk7ue4K+6vS83/rvxjZURV7ARNmUVCDGkf6R6x4CmKllZ5VAc ZmcfyO4dCB2rDKGHQJzvJCBP3tbEDXHYVYfJo1zZVx3QkaBz9CWF97VGrUF2gNEN fwIDAQABo4ICWTCCAlUwTwYDVR0gBEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYP KwYBBAGBrSGCLAIBBAMBMA8GDSsGAQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4w CQYDVR0TBAIwADALBgNVHQ8EBAMCBeAwHQYDVR0lBBYwFAYIKwYBBQUHAwIGCCsG AQUFBwMBMB0GA1UdDgQWBBRNjdGKP4xo/HaYDoNMTDrnlGTruDAfBgNVHSMEGDAW gBTo2rjyR96ZJH1nQIknZ3ENY9ijjjAmBgNVHREEHzAdght0ZXN0LWlkcC5ocnou dHUtY2hlbW5pdHouZGUwgYgGA1UdHwSBgDB+MD2gO6A5hjdodHRwOi8vY2RwMS5w Y2EuZGZuLmRlL3R1LWNoZW1uaXR6LWNhL3B1Yi9jcmwvY2FjcmwuY3JsMD2gO6A5 hjdodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWNoZW1uaXR6LWNhL3B1Yi9jcmwv Y2FjcmwuY3JsMIHXBggrBgEFBQcBAQSByjCBxzAzBggrBgEFBQcwAYYnaHR0cDov L29jc3AucGNhLmRmbi5kZS9PQ1NQLVNlcnZlci9PQ1NQMEcGCCsGAQUFBzAChjto dHRwOi8vY2RwMS5wY2EuZGZuLmRlL3R1LWNoZW1uaXR6LWNhL3B1Yi9jYWNlcnQv Y2FjZXJ0LmNydDBHBggrBgEFBQcwAoY7aHR0cDovL2NkcDIucGNhLmRmbi5kZS90 dS1jaGVtbml0ei1jYS9wdWIvY2FjZXJ0L2NhY2VydC5jcnQwDQYJKoZIhvcNAQEL BQADggEBAFfupAsu2EJdR0e1R0U4B1Qqk6qFCaOAbkkNbxshPfaSO+6188uwXrz+ YxuUa6dSU7E4ZeYr+RxfCjTLQf4nCdHsSey1hfXcRVLd65TSby26x6E8IFTXssKM heE40/LHBFioqbNBEBlOBi+a2Oo2DWz0M88I4RB5EgkyF/ffJG+XyVW91/qK1qrW EpO6m1xIBByElURZTVnGHMGARktLpOhb/7GJGfKkOQSow/D4kNoVDlrJsgiLS+vD GZz0j9awFqt04CsL091JOnUWBVd7mX9isddp4xM2uvX3PPP8GPn8VNlRjErw8cZv /AbLASenClYg24Od2p7wrATqBqqFNf0=',
);

$metadata['https://idptest.hrz.tu-freiberg.de/idp/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Freiberg (Test v2)',
	),
	'description'          => 'Dies ist der v2 Test-Shibboleth-IdP der Technischen Universität Freiberg.',
	'SingleSignOnService'  => 'https://idptest.hrz.tu-freiberg.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHazCCBlOgAwIBAgIHGo+hDhtTBDANBgkqhkiG9w0BAQsFADCBwzELMAkGA1UE BhMCREUxNjA0BgNVBAoTLVRlY2huaXNjaGUgVW5pdmVyc2l0YWV0IEJlcmdha2Fk ZW1pZSBGcmVpYmVyZzEjMCEGA1UECxMaVW5pdmVyc2l0YWV0c3JlY2hlbnplbnRy dW0xLzAtBgNVBAMTJlRVIEJlcmdha2FkZW1pZSBGcmVpYmVyZyBDQSAoVFVCQUYt Q0EpMSYwJAYJKoZIhvcNAQkBFhd0dWJhZi1jYUB0dS1mcmVpYmVyZy5kZTAeFw0x NTEyMTUxNDI4MTVaFw0xOTAzMTMxNDI4MTVaMIG0MQswCQYDVQQGEwJERTEQMA4G A1UECAwHU2FjaHNlbjERMA8GA1UEBwwIRnJlaWJlcmcxNjA0BgNVBAoMLVRlY2hu aXNjaGUgVW5pdmVyc2l0YWV0IEJlcmdha2FkZW1pZSBGcmVpYmVyZzEjMCEGA1UE CwwaVW5pdmVyc2l0YWV0c3JlY2hlbnplbnRydW0xIzAhBgNVBAMMGmlkcHRlc3Qu aHJ6LnR1LWZyZWliZXJnLmRlMIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKC AgEA2uynWJYjQAT0Gko7kfOImf/4UygOc8rzevxAlxn9ye1KFSby5Cfu5ETXYWFF Swr3gsmrz8o2u2938SM5tUsr0rAjw9kQh31/rF2mJ3aVoLjV+1CT3Ho7dJ0QfAxl 32+da+nQEmPinp1W/EyfuTDIANMS0x5jP30XYPGgLpgF9uQRGQrjKOM/Xq/IRWrj E7VQJSpJkbuu4561HNnsj5xxM0CP73TfHat2J8wDWA82bxClmxIYackgJ3Dpce50 RQlHCKOTwmVD1wS5VriN39N0IW4zyyr0acmRYfgSp5KuwB28M4Gpo0UnoFwhStvd ZF1jzlJQ9mHN62vh2txR7U/VDG4oWn+tZhzCBgk9EBx6lyk0HgZU1kNcQqEcAcM8 G4I4esGBvm3t0+4ZX0KaHtFUiRJv8v+Mp44R9D5shrpYqC1TrhGJqyVq3rVT+p3j 51SyAbLljkb2g3a5utVUVOkXUL1U5mpOrpitUlayEY0Vs394R7yF8jbSD8Iv8WEg FT/W7zZjmt+jxenIsG0N2O4GdBacLRgk58xiLCiKzA+xaEwA0c8g0Gl9IfSbme5b cjtOcDnLMLWHexbhuaCiLTIxRoD1WB+j92fhcQjBc0ri2HOQCZ1o8AgWgD8V7xjN NxdmKn/fAbLOO58Dhqga9+rm/Ph6x++8MLj1rKUK49fGpS0CAwEAAaOCAm8wggJr MFkGA1UdIARSMFAwEQYPKwYBBAGBrSGCLAEBBAMEMBEGDysGAQQBga0hgiwCAQQD ATAPBg0rBgEEAYGtIYIsAQEEMA0GCysGAQQBga0hgiweMAgGBmeBDAECAjAJBgNV HRMEAjAAMAsGA1UdDwQEAwIF4DAdBgNVHSUEFjAUBggrBgEFBQcDAgYIKwYBBQUH AwEwHQYDVR0OBBYEFM7setFV2ZqysvyyiJViP1XKhrrQMB8GA1UdIwQYMBaAFJ9R kPeYCvQ/FQ8Otgqpf7c1HR0tMCUGA1UdEQQeMByCGmlkcHRlc3QuaHJ6LnR1LWZy ZWliZXJnLmRlMIGPBgNVHR8EgYcwgYQwQKA+oDyGOmh0dHA6Ly9jZHAxLnBjYS5k Zm4uZGUvdHUtYmEtZnJlaWJlcmctY2EvcHViL2NybC9jYWNybC5jcmwwQKA+oDyG Omh0dHA6Ly9jZHAyLnBjYS5kZm4uZGUvdHUtYmEtZnJlaWJlcmctY2EvcHViL2Ny bC9jYWNybC5jcmwwgd0GCCsGAQUFBwEBBIHQMIHNMDMGCCsGAQUFBzABhidodHRw Oi8vb2NzcC5wY2EuZGZuLmRlL09DU1AtU2VydmVyL09DU1AwSgYIKwYBBQUHMAKG Pmh0dHA6Ly9jZHAxLnBjYS5kZm4uZGUvdHUtYmEtZnJlaWJlcmctY2EvcHViL2Nh Y2VydC9jYWNlcnQuY3J0MEoGCCsGAQUFBzAChj5odHRwOi8vY2RwMi5wY2EuZGZu LmRlL3R1LWJhLWZyZWliZXJnLWNhL3B1Yi9jYWNlcnQvY2FjZXJ0LmNydDANBgkq hkiG9w0BAQsFAAOCAQEARsPpz0yhiz3rCUI7A3FN7tSDOUop8VM8BLMVRYfQFG2s an2tAl4BFM2AqO3Z2bgkKA1aD4iMK9a4Rq4BnvZdf2VIz5TefI9Hs1FTesMRrGbR 3er6jODSS2o8w52EB8MDAo8mr0O1a8bir7iWEl994jRcHje8jaT2k8VFUGIkc1dz bsmYvH9l50bTQJsX6pR5KTFT1UTBbyRXV5FwXHfThjNsFcP6lwPmRxQtbrqqK+ak GFJEtKRwdZ2a5Z7qrfTvIkw4RApmBpjKU4tEVrVqtrM/UoVQbX9t86hiAt0r2Lz0 mS25YWbn59sdwAm+Bmp54L5FirQEKeRRi+aGTNiBdA==',
);
