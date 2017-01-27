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
 *
*$metadata['https://openidp.feide.no'] = array(
*	'name' => array(
*		'en' => 'Feide OpenIdP - guest users',
*		'no' => 'Feide Gjestebrukere',
*	),
*	'description'          => 'Here you can login with your account on Feide RnD OpenID. If you do not already have an account on this identity provider, you can create a new one by following the create new account link and follow the instructions.',
*
*	'SingleSignOnService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SSOService.php',
*	'SingleLogoutService'  => 'https://openidp.feide.no/simplesaml/saml2/idp/SingleLogoutService.php',
*	'certFingerprint'      => 'c9ed4dfb07caf13fc21e0fec1572047eb8a7a4cb'
);
*/

$metadata['https://idp3-test.tu-dresden.de/idp/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Dresden (Test v3)',
	),
	'description'          => 'Dies ist der v3 Test-Shibboleth-IdP der Technischen Universität Dresden.',
	'SingleSignOnService'  => 'https://idp3-test.tu-dresden.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHBjCCBe6gAwIBAgIHGhifNq+jtDANBgkqhkiG9w0BAQsFADCBhTELMAkGA1UE BhMCREUxKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0YWV0IERyZXNkZW4x DDAKBgNVBAsTA1pJSDEcMBoGA1UEAxMTVFUgRHJlc2RlbiBDQSAtIEcwMjEgMB4G CSqGSIb3DQEJARYRcGtpQHR1LWRyZXNkZW4uZGUwHhcNMTUwOTE2MDgwMDM5WhcN MTgwOTE1MDgwMDM5WjCBizELMAkGA1UEBhMCREUxEDAOBgNVBAgMB1NhY2hzZW4x EDAOBgNVBAcMB0RyZXNkZW4xKDAmBgNVBAoMH1RlY2huaXNjaGUgVW5pdmVyc2l0 YWV0IERyZXNkZW4xDDAKBgNVBAsMA1pJSDEgMB4GA1UEAwwXaWRwMy10ZXN0LnR1 LWRyZXNkZW4uZGUwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQDAAV1+ DSgvgl7fQnGoU72IGne7KsLkVrYDV0stAK+s6/RQ/Hrpy24dIax7r/FhRHAFlvHO 9x+luZ4xq84cQZ86S9+GMkOgHfmZDb3CuMSG9W3DIx7/7q1xYedtoUbPobPw7x/u vaxqzf36/NuX42o/8cdi4L4c7gIOgHek8MLMUWev89p2tk9WUbh5Jxw2LAvzLbSc v7Uu2QSqiiqhiyfcei1dyPUnmKAwOC/Q6Nae5+upZ2cVaLm/qD27kRU4TAWAnR2Y zwIu0vB6GVRgbZ3AStedYdK3OIULTW3/hyooBEhsPWp5KuyhQ5T33jljC4gyKw1M uIFNJ9W1mkHs2uMVDSzvD9TaTg6a9V8Noagu7pKuG6DPyfitXnvCXYu9HC2g++F8 4cleeJO6uky8PmJsyLPU0wE6WHmChw1jQGecER9mcGsWJvJAtfvQwvgwUGnWMFj8 U/lRlT6XFGoLKufH9e7d5Hk0xTeBb/Dv7Gt3rquJ/KJtAhJhbeSRb6wzhJ4P1Zlk PQXYXXy+FmDGAxAb4t+HJOJEJkaMkr47OusTaO9fuEQ/CDCJ4N3RzC+J1NRrBWms gHqL2xdTmpi0h576aFr7fcA9Nnhbme+3EgwkEMySMA752b5d2ZVzl85l6g/5yYlc fWEp9784qPxUIT5wWm+u/iErmtKfM+D4PYYG+QIDAQABo4ICcTCCAm0wTwYDVR0g BEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYPKwYBBAGBrSGCLAIBBAMBMA8GDSsG AQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4wCQYDVR0TBAIwADALBgNVHQ8EBAMC BeAwNAYDVR0lBC0wKwYIKwYBBQUHAwIGCCsGAQUFBwMBBgorBgEEAYI3CgMDBglg hkgBhvhCBAEwHQYDVR0OBBYEFFdIQqO86A2QTVxSUvwsAWcddf5ZMB8GA1UdIwQY MBaAFMUrU5MXg8n1RkLtQ2rftoCmR/LgMCIGA1UdEQQbMBmCF2lkcDMtdGVzdC50 dS1kcmVzZGVuLmRlMIGLBgNVHR8EgYMwgYAwPqA8oDqGOGh0dHA6Ly9jZHAxLnBj YS5kZm4uZGUvdHUtZHJlc2Rlbi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMD6gPKA6 hjhodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWRyZXNkZW4tY2EvcHViL2NybC9n X2NhY3JsLmNybDCB2QYIKwYBBQUHAQEEgcwwgckwMwYIKwYBBQUHMAGGJ2h0dHA6 Ly9vY3NwLnBjYS5kZm4uZGUvT0NTUC1TZXJ2ZXIvT0NTUDBIBggrBgEFBQcwAoY8 aHR0cDovL2NkcDEucGNhLmRmbi5kZS90dS1kcmVzZGVuLWNhL3B1Yi9jYWNlcnQv Z19jYWNlcnQuY3J0MEgGCCsGAQUFBzAChjxodHRwOi8vY2RwMi5wY2EuZGZuLmRl L3R1LWRyZXNkZW4tY2EvcHViL2NhY2VydC9nX2NhY2VydC5jcnQwDQYJKoZIhvcN AQELBQADggEBAK1xTD3O7xNlJJ2FnbfTGNRET3XZuqRO6BcaVS0GE5gsVm6PO0oJ PcVTvgzk7KwPYF6egn7EIETTFgQ3dlzIdNUnIOK3FoxVTZTdCunykzHI08eQDd1b KRMU8z/DGgtxcb2MbtZTnqqEUoApxDRWdZGfdOO2TYfwbG01qw8jxMyFjYF+YtRF 9fkEESG6e87KLnCsW/E48+HcYiP4BqBsPzCroAy2OhPIGFptye3AQME+z39Jm6L0 MUuYHc7NlYByAtT3QdXwE0TCuoAK9LYs4rBOa82/PRIT3+dItXeVa3i7GGCkHQ2N DUdIIKxIk3ElqB7mzbebaY1oLPveWe6Oi4w=',
);

$metadata['https://idp3.tu-dresden.de/idp/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Dresden (v3)',
	),
	'description'          => 'Technischen Universität Dresden.',
	'SingleSignOnService'  => 'https://idp2.tu-dresden.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHBjCCBe6gAwIBAgIHGhifNq+jtDANBgkqhkiG9w0BAQsFADCBhTELMAkGA1UE BhMCREUxKDAmBgNVBAoTH1RlY2huaXNjaGUgVW5pdmVyc2l0YWV0IERyZXNkZW4x DDAKBgNVBAsTA1pJSDEcMBoGA1UEAxMTVFUgRHJlc2RlbiBDQSAtIEcwMjEgMB4G CSqGSIb3DQEJARYRcGtpQHR1LWRyZXNkZW4uZGUwHhcNMTUwOTE2MDgwMDM5WhcN MTgwOTE1MDgwMDM5WjCBizELMAkGA1UEBhMCREUxEDAOBgNVBAgMB1NhY2hzZW4x EDAOBgNVBAcMB0RyZXNkZW4xKDAmBgNVBAoMH1RlY2huaXNjaGUgVW5pdmVyc2l0 YWV0IERyZXNkZW4xDDAKBgNVBAsMA1pJSDEgMB4GA1UEAwwXaWRwMy10ZXN0LnR1 LWRyZXNkZW4uZGUwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQDAAV1+ DSgvgl7fQnGoU72IGne7KsLkVrYDV0stAK+s6/RQ/Hrpy24dIax7r/FhRHAFlvHO 9x+luZ4xq84cQZ86S9+GMkOgHfmZDb3CuMSG9W3DIx7/7q1xYedtoUbPobPw7x/u vaxqzf36/NuX42o/8cdi4L4c7gIOgHek8MLMUWev89p2tk9WUbh5Jxw2LAvzLbSc v7Uu2QSqiiqhiyfcei1dyPUnmKAwOC/Q6Nae5+upZ2cVaLm/qD27kRU4TAWAnR2Y zwIu0vB6GVRgbZ3AStedYdK3OIULTW3/hyooBEhsPWp5KuyhQ5T33jljC4gyKw1M uIFNJ9W1mkHs2uMVDSzvD9TaTg6a9V8Noagu7pKuG6DPyfitXnvCXYu9HC2g++F8 4cleeJO6uky8PmJsyLPU0wE6WHmChw1jQGecER9mcGsWJvJAtfvQwvgwUGnWMFj8 U/lRlT6XFGoLKufH9e7d5Hk0xTeBb/Dv7Gt3rquJ/KJtAhJhbeSRb6wzhJ4P1Zlk PQXYXXy+FmDGAxAb4t+HJOJEJkaMkr47OusTaO9fuEQ/CDCJ4N3RzC+J1NRrBWms gHqL2xdTmpi0h576aFr7fcA9Nnhbme+3EgwkEMySMA752b5d2ZVzl85l6g/5yYlc fWEp9784qPxUIT5wWm+u/iErmtKfM+D4PYYG+QIDAQABo4ICcTCCAm0wTwYDVR0g BEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYPKwYBBAGBrSGCLAIBBAMBMA8GDSsG AQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4wCQYDVR0TBAIwADALBgNVHQ8EBAMC BeAwNAYDVR0lBC0wKwYIKwYBBQUHAwIGCCsGAQUFBwMBBgorBgEEAYI3CgMDBglg hkgBhvhCBAEwHQYDVR0OBBYEFFdIQqO86A2QTVxSUvwsAWcddf5ZMB8GA1UdIwQY MBaAFMUrU5MXg8n1RkLtQ2rftoCmR/LgMCIGA1UdEQQbMBmCF2lkcDMtdGVzdC50 dS1kcmVzZGVuLmRlMIGLBgNVHR8EgYMwgYAwPqA8oDqGOGh0dHA6Ly9jZHAxLnBj YS5kZm4uZGUvdHUtZHJlc2Rlbi1jYS9wdWIvY3JsL2dfY2FjcmwuY3JsMD6gPKA6 hjhodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWRyZXNkZW4tY2EvcHViL2NybC9n X2NhY3JsLmNybDCB2QYIKwYBBQUHAQEEgcwwgckwMwYIKwYBBQUHMAGGJ2h0dHA6 Ly9vY3NwLnBjYS5kZm4uZGUvT0NTUC1TZXJ2ZXIvT0NTUDBIBggrBgEFBQcwAoY8 aHR0cDovL2NkcDEucGNhLmRmbi5kZS90dS1kcmVzZGVuLWNhL3B1Yi9jYWNlcnQv Z19jYWNlcnQuY3J0MEgGCCsGAQUFBzAChjxodHRwOi8vY2RwMi5wY2EuZGZuLmRl L3R1LWRyZXNkZW4tY2EvcHViL2NhY2VydC9nX2NhY2VydC5jcnQwDQYJKoZIhvcN AQELBQADggEBAK1xTD3O7xNlJJ2FnbfTGNRET3XZuqRO6BcaVS0GE5gsVm6PO0oJ PcVTvgzk7KwPYF6egn7EIETTFgQ3dlzIdNUnIOK3FoxVTZTdCunykzHI08eQDd1b KRMU8z/DGgtxcb2MbtZTnqqEUoApxDRWdZGfdOO2TYfwbG01qw8jxMyFjYF+YtRF 9fkEESG6e87KLnCsW/E48+HcYiP4BqBsPzCroAy2OhPIGFptye3AQME+z39Jm6L0 MUuYHc7NlYByAtT3QdXwE0TCuoAK9LYs4rBOa82/PRIT3+dItXeVa3i7GGCkHQ2N DUdIIKxIk3ElqB7mzbebaY1oLPveWe6Oi4w=',
);

$metadata['https://test-idp.hrz.tu-chemnitz.de/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Chemnitz (Test SAML1)',
	),
	'description'          => 'Technischen Universität Chemnitz',
	'SingleSignOnService'  => 'https://test-idp.hrz.tu-chemnitz.de/krb/saml2/idp/SSOService.php',
	'certData'             => 'MIIGQzCCBSugAwIBAgIHGZYTHYvG9TANBgkqhkiG9w0BAQsFADCBvTELMAkGA1UE BhMCREUxKTAnBgNVBAoTIFRlY2huaXNjaGUgVW5pdmVyc2l0YWV0IENoZW1uaXR6 MSMwIQYDVQQLExpVbml2ZXJzaXRhZXRzcmVjaGVuemVudHJ1bTE8MDoGA1UEAxMz VFUgQ2hlbW5pdHogQ2VydGlmaWNhdGlvbiBBdXRob3JpdHkgLSBUVUMvVVJaIENB IEczMSAwHgYJKoZIhvcNAQkBFhFjYUB0dS1jaGVtbml0ei5kZTAeFw0xNTA2MDkw NzI4MTNaFw0xODA5MDUwNzI4MTNaMIGoMQswCQYDVQQGEwJERTEQMA4GA1UECAwH U2FjaHNlbjERMA8GA1UEBwwIQ2hlbW5pdHoxKTAnBgNVBAoMIFRlY2huaXNjaGUg VW5pdmVyc2l0YWV0IENoZW1uaXR6MSMwIQYDVQQLDBpVbml2ZXJzaXRhZXRzcmVj aGVuemVudHJ1bTEkMCIGA1UEAwwbdGVzdC1pZHAuaHJ6LnR1LWNoZW1uaXR6LmRl MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAiqPC4MsKL+tJ6H2AClzF sOjaavyYHHLOdzNYyXKUQXick/FOTZeQdM9/gO61iQSdy7dog2EJ19G8umoMrCdt FVaLwFpIqqU2g3KCinwt0eSW8MedRR4PrLxgp7/r/+lT+u7fy9CNPzdTjGFeW1j1 UINxZxJbtOnhEFjqFL6JW5J++Flc5zQR7a5IB8yNya11P3++DIWZNaagqdt6KLKs qaPiaXMfnHZVAYFXk7ue4K+6vS83/rvxjZURV7ARNmUVCDGkf6R6x4CmKllZ5VAc ZmcfyO4dCB2rDKGHQJzvJCBP3tbEDXHYVYfJo1zZVx3QkaBz9CWF97VGrUF2gNEN fwIDAQABo4ICWTCCAlUwTwYDVR0gBEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYP KwYBBAGBrSGCLAIBBAMBMA8GDSsGAQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4w CQYDVR0TBAIwADALBgNVHQ8EBAMCBeAwHQYDVR0lBBYwFAYIKwYBBQUHAwIGCCsG AQUFBwMBMB0GA1UdDgQWBBRNjdGKP4xo/HaYDoNMTDrnlGTruDAfBgNVHSMEGDAW gBTo2rjyR96ZJH1nQIknZ3ENY9ijjjAmBgNVHREEHzAdght0ZXN0LWlkcC5ocnou dHUtY2hlbW5pdHouZGUwgYgGA1UdHwSBgDB+MD2gO6A5hjdodHRwOi8vY2RwMS5w Y2EuZGZuLmRlL3R1LWNoZW1uaXR6LWNhL3B1Yi9jcmwvY2FjcmwuY3JsMD2gO6A5 hjdodHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWNoZW1uaXR6LWNhL3B1Yi9jcmwv Y2FjcmwuY3JsMIHXBggrBgEFBQcBAQSByjCBxzAzBggrBgEFBQcwAYYnaHR0cDov L29jc3AucGNhLmRmbi5kZS9PQ1NQLVNlcnZlci9PQ1NQMEcGCCsGAQUFBzAChjto dHRwOi8vY2RwMS5wY2EuZGZuLmRlL3R1LWNoZW1uaXR6LWNhL3B1Yi9jYWNlcnQv Y2FjZXJ0LmNydDBHBggrBgEFBQcwAoY7aHR0cDovL2NkcDIucGNhLmRmbi5kZS90 dS1jaGVtbml0ei1jYS9wdWIvY2FjZXJ0L2NhY2VydC5jcnQwDQYJKoZIhvcNAQEL BQADggEBAFfupAsu2EJdR0e1R0U4B1Qqk6qFCaOAbkkNbxshPfaSO+6188uwXrz+ YxuUa6dSU7E4ZeYr+RxfCjTLQf4nCdHsSey1hfXcRVLd65TSby26x6E8IFTXssKM heE40/LHBFioqbNBEBlOBi+a2Oo2DWz0M88I4RB5EgkyF/ffJG+XyVW91/qK1qrW EpO6m1xIBByElURZTVnGHMGARktLpOhb/7GJGfKkOQSow/D4kNoVDlrJsgiLS+vD GZz0j9awFqt04CsL091JOnUWBVd7mX9isddp4xM2uvX3PPP8GPn8VNlRjErw8cZv /AbLASenClYg24Od2p7wrATqBqqFNf0=',
);

$metadata['https://idp.hrz.tu-freiberg.de/idp/shibboleth'] = array(
	'name' => array(
		'de' => 'Shibboleth-IdP TU Freiberg (SAML2)',
	),
	'description'          => 'Technischen Universität Freiberg',
	'SingleSignOnService'  => 'https://idp.hrz.tu-freiberg.de/idp/profile/SAML2/Redirect/SSO',
	'certData'             => 'MIIHWTCCBkGgAwIBAgIHGbHFboK0cDANBgkqhkiG9w0BAQsFADCBwzELMAkGA1UE BhMCREUxNjA0BgNVBAoTLVRlY2huaXNjaGUgVW5pdmVyc2l0YWV0IEJlcmdha2Fk ZW1pZSBGcmVpYmVyZzEjMCEGA1UECxMaVW5pdmVyc2l0YWV0c3JlY2hlbnplbnRy dW0xLzAtBgNVBAMTJlRVIEJlcmdha2FkZW1pZSBGcmVpYmVyZyBDQSAoVFVCQUYt Q0EpMSYwJAYJKoZIhvcNAQkBFhd0dWJhZi1jYUB0dS1mcmVpYmVyZy5kZTAeFw0x NTA2MzAwNzQwMTRaFw0xODA5MjYwNzQwMTRaMIGwMQswCQYDVQQGEwJERTEQMA4G A1UECAwHU2FjaHNlbjERMA8GA1UEBwwIRnJlaWJlcmcxNjA0BgNVBAoMLVRlY2hu aXNjaGUgVW5pdmVyc2l0YWV0IEJlcmdha2FkZW1pZSBGcmVpYmVyZzEjMCEGA1UE CwwaVW5pdmVyc2l0YWV0c3JlY2hlbnplbnRydW0xHzAdBgNVBAMMFmlkcC5ocnou dHUtZnJlaWJlcmcuZGUwggIiMA0GCSqGSIb3DQEBAQUAA4ICDwAwggIKAoICAQC7 5jPHerdCvissgxaRb1XxZGUCPlqNGdmf//zjLriH6i+oBirkrD5Jrl69voAyrBMs 9PaXxo8doyhfsNtHLXZKXqcqTKV0gMHOXH/x0Y8jR5T2+rludaf8VPEpZmr5cPh1 BWi7Uxa411EXnWzRA9WYUGZ6E33gQaGXztRzCmcKFZIM4fOKVEzav3uE7wedZNQh u0Gt4/ldc+Oz9DTf0ZEsrPnS2Bt5fABLAVe2+BdHjMhjzQR5Hfa7LNroZIpBNk80 dcIbQxxBuagEXG70OufPz3flHANfYEF/G4tcEqkBrIiRbMLYnrbRgw7qh4+eT50g oizGNtxc16PIzmH25meOwB+Mrb760TEl4M4gDgSoZncZEkPv0Ji07kKZuG+UvA6R v5dMuLLqb3XPj2ihrilryzSwoVpDE8zsWEQsFPIM+UqKn/xO4VEJrvtto5+mGIN2 hWyO0ebhhg74T2obPI9VeyLZWVvvRaklc+NA0dErGrxz2siWQNp27BcyTUPwV0/w fJlU64j5q6qUupQU3rrfJwnpE9BdnL+1LmO/gVGNNsi9oZDfe8YkWoBqa2lcgD4m QClzwpnl99/y4MT0ZDJJ54oRfmEEwCc9yFsYrWURXLUb5d3Lwkm3d+PMZHxNbPGI SHueiHWIVww6Rt6IhyyWYa+f767f3xvtvJSvTNh+HwIDAQABo4ICYTCCAl0wTwYD VR0gBEgwRjARBg8rBgEEAYGtIYIsAQEEAwMwEQYPKwYBBAGBrSGCLAIBBAMBMA8G DSsGAQQBga0hgiwBAQQwDQYLKwYBBAGBrSGCLB4wCQYDVR0TBAIwADALBgNVHQ8E BAMCBeAwHQYDVR0lBBYwFAYIKwYBBQUHAwIGCCsGAQUFBwMBMB0GA1UdDgQWBBQO Hpro9zNojuI+55ez100Ltf3KPjAfBgNVHSMEGDAWgBSfUZD3mAr0PxUPDrYKqX+3 NR0dLTAhBgNVHREEGjAYghZpZHAuaHJ6LnR1LWZyZWliZXJnLmRlMIGPBgNVHR8E gYcwgYQwQKA+oDyGOmh0dHA6Ly9jZHAxLnBjYS5kZm4uZGUvdHUtYmEtZnJlaWJl cmctY2EvcHViL2NybC9jYWNybC5jcmwwQKA+oDyGOmh0dHA6Ly9jZHAyLnBjYS5k Zm4uZGUvdHUtYmEtZnJlaWJlcmctY2EvcHViL2NybC9jYWNybC5jcmwwgd0GCCsG AQUFBwEBBIHQMIHNMDMGCCsGAQUFBzABhidodHRwOi8vb2NzcC5wY2EuZGZuLmRl L09DU1AtU2VydmVyL09DU1AwSgYIKwYBBQUHMAKGPmh0dHA6Ly9jZHAxLnBjYS5k Zm4uZGUvdHUtYmEtZnJlaWJlcmctY2EvcHViL2NhY2VydC9jYWNlcnQuY3J0MEoG CCsGAQUFBzAChj5odHRwOi8vY2RwMi5wY2EuZGZuLmRlL3R1LWJhLWZyZWliZXJn LWNhL3B1Yi9jYWNlcnQvY2FjZXJ0LmNydDANBgkqhkiG9w0BAQsFAAOCAQEAiL3X Jb1j/oM+U6OAUqtRmR7NGAU+cJmJ9RoyMK0vUfKM7875DPaoVdbfRxcvDsUPRkyb f5zFyLK1MOKO/z+weyZvHvQpXJSC2DrQ1rJpj3iLUUNSFkVMRhMm3BKgKo4xr9Gt SqbFEmQoRD6oPxUGdJzlFxrHh9usrztLb5TTsAMPdKSjnpNpoHg7GAgiLejil2rg Is+W7w4p+PqI3RqI9bZU8vwZjTtPv9J2iMp4t+q9PLW70mpmL6M3ZpUayXqLG8Z3 WnHZRQpuN9vhYpTbAq1dpMhDcGXYdG/USjPxMxcth99ETEPnxBRhqvga0G7go2A/ mEF9cF0ecUhR3uR0yw==',
);
