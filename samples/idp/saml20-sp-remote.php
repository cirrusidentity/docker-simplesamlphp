<?php
// Visit https://localhost/sample-idp/module.php/admin/federation/metadata-converter to convert XML
// metadata to php metadata and add it here.

$metadata['urn:federation:MicrosoftOnline'] = [
    'entityid' => 'urn:federation:MicrosoftOnline',
    'contacts' => [],
    'metadata-set' => 'saml20-sp-remote',
    'AssertionConsumerService' => [
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'https://login.microsoftonline.com/login.srf',
            'index' => 0,
            'isDefault' => true,
        ],
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST-SimpleSign',
            'Location' => 'https://login.microsoftonline.com/login.srf',
            'index' => 1,
        ],
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:PAOS',
            'Location' => 'https://login.microsoftonline.com/login.srf',
            'index' => 2,
        ],
    ],
    'SingleLogoutService' => [
        [
            'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
            'Location' => 'https://login.microsoftonline.com/login.srf',
        ],
    ],
    'NameIDFormat' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',
    'keys' => [
        [
            'encryption' => false,
            'signing' => true,
            'type' => 'X509Certificate',
            'X509Certificate' => 'MIIC/TCCAeWgAwIBAgIQbgDHfi3t1JNGVqwD5/7lmjANBgkqhkiG9w0BAQsFADApMScwJQYDVQQDEx5MaXZlIElEIFNUUyBTaWduaW5nIFB1YmxpYyBLZXkwHhcNMjAxMjIxMDAwMDAwWhcNMjUxMjIxMDAwMDAwWjApMScwJQYDVQQDEx5MaXZlIElEIFNUUyBTaWduaW5nIFB1YmxpYyBLZXkwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDFT0/0/2qQurnYa0LbJHF9YYozhEH6r9mCxVDBYbewSG4tGgrWpsewQ/96pcczGMQctMvU+h2eX38Hx/f9JAIDbuRQzQlsPhQS7DDZ6WlTXU+t8d/g2C7fpSoLs4KVdJih4xyjLUWj+BK/ijsRjBt4Riw9VbJH/DdWKyoSMbECEiE+s1RtLP/eYoMmNfxyQGqWirCNqVNBTlqzYQp4dgF0foYy4ktoxwmQOVoTcIMFYp1I4pFPI7CxuMLkfK0X7aTbM7YGphvMfJxJkjrQdyI7G5d1t4DNi3zkEbBT7FGAr6qPt3Kn9ralpqJKHdpEBA9N0vNwQo5XTYIhUbPQ16IRAgMBAAGjITAfMB0GA1UdDgQWBBRs7tPmfkksSr67KtElHjYZbeaCTjANBgkqhkiG9w0BAQsFAAOCAQEAJqwMZSjQJ36x+1sty6EeLKQLQewQwPaEC47Zut+8bXed6Q8jMZ0bfa/MM7XquEcabaMZLQuKLft44YXwXXQOfQrI2qjQr3eToJFlDT9hR0rfp9wQqttDxd6Aa6RWwDTgo5oKUQCTKLHhEy8uWzScK0eGt2d7TWTaDXjRSwNq6tM7fRhZs07tKBV3xfi9EQy/mlavAMFRBVm86NSo7AsOG1IOMq03U3ooCWAXh9PdvvHNfHhH19futAnC/HeOjwRF1Qc527aBMphYFQLdiThfmfmiE/AhQqCwZ2oE7uCJhBtR+Kb1ZGhjI35pHfsSqGiFa7Kr+5ave822PDcke89Mvg==',
        ],
        [
            'encryption' => false,
            'signing' => true,
            'type' => 'X509Certificate',
            'X509Certificate' => 'MIIC/TCCAeWgAwIBAgIQN/GPegnT8blP2EcSdMMbBzANBgkqhkiG9w0BAQsFADApMScwJQYDVQQDEx5MaXZlIElEIFNUUyBTaWduaW5nIFB1YmxpYyBLZXkwHhcNMjEwMjE4MDAwMDAwWhcNMjYwMjE4MDAwMDAwWjApMScwJQYDVQQDEx5MaXZlIElEIFNUUyBTaWduaW5nIFB1YmxpYyBLZXkwggEiMA0GCSqGSIb3DQEBAQUAA4IBDwAwggEKAoIBAQDXdLGU2Ll5RPdDUnKQ+f/HS5qiTay2cCh9U2AS6oDM6SOxVhYGtoeJ1VPebcLnpgLfhPxzrwWoVzXSEF+VRQbnYID2Jb4khjgyEeoThk3VqrThwhahpSbBg2vo06vIOp1TS2R1BiwHKTLoB1i1IJnaIFSC3BN6pY4flXWyLQt/5ABXElv2XZLqXM9Eefj6Ji40nLIsiW4dWw3BDa/ywWW0MsiW5ojGq4vovcAgENe/4NUbju70gHP/WS5D9bW5p+OIQi7/unrlWe/h3A6jtBbbRlXYXlN+Z22uTTyyCD/W8zeXaACLvHagwEMrQePDXBZqc/iX2kI+ooZr1sC/H39RAgMBAAGjITAfMB0GA1UdDgQWBBSrX2dm3LwT9jb/p+bAAdYQpE+/NjANBgkqhkiG9w0BAQsFAAOCAQEAeqJfYHnsA9qhGttXFfFpPW4DQLh5w6JCce7vGvWINr5fr1DnQdcOr+wwjQ/tqbckAL2v6z1AqjhS78kbfegnAQDwioJZ1olYYvLOxKoa6HF+b1/p0Mlub8Zukk2n1b2lKPBBOibOasSY7gQDwlIZi7tl9nMTxUfdYK+E5Axv7DVnmUCwcnnpV5/1SFdNyW2kWO4C68rrjMOvECfwrKkbfVJM8f9krEUBuoBF8dTDv7D2ZM4Q2buC70NbfaNWUX0yFvKI0IuTqk8RBfGTRQ4fZAbhMPaykEpBu6dNjTi5YOa0lNqFGS7Ax7leCh5x9lV8elcLkXs8ySo8AOQJk0hgIw==',
        ],
        [
            'encryption' => false,
            'signing' => true,
            'type' => 'X509Certificate',
            'X509Certificate' => 'MIIC9jCCAd6gAwIBAgIJAKQ96FDw4zyOMA0GCSqGSIb3DQEBCwUAMCkxJzAlBgNVBAMTHkxpdmUgSUQgU1RTIFNpZ25pbmcgUHVibGljIEtleTAeFw0yMzA3MTYxODU1NTVaFw0yODA3MTYxODU1NTVaMCkxJzAlBgNVBAMTHkxpdmUgSUQgU1RTIFNpZ25pbmcgUHVibGljIEtleTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALtm2+vZtNrL+2zAKVchYm14OpGgUlMoo2chdtpIrR8GMFZV36u+Y6/w9pgqmxiYcs4BoeByfSJpk/uQT+nAo+Xjx9fHPlL9iEZnSZSq/bxUHLdObFGyfTaWMWlpLB23hFFVCacpACFJ0qPUPyDRABc2PHuN6fuR6dTamst5wrbJzollDQnUtQKxg7UqqYpM01t7kApm5vGq0SME6g5atEXz2ipDY4g/CSEfNmCiiv90gZ+JbnA0O8RKf740l3HHQx4vyVogeyOdIeicibobKunlt9dHBhxJ4bHY26cRD+K+vFkD/o5OoROhr4u4kxRRpNKl82X58bW50SOW2TpT1oUCAwEAAaMhMB8wHQYDVR0OBBYEFG8M5pHShE1oYsZUvfRS9+hR9+xCMA0GCSqGSIb3DQEBCwUAA4IBAQBNE6JD5YikgyhJtJ2ebaOPEqO+5Ea9EkJNcdehUPa7fkeVnW//jnDW4ETHf0o+ZE63vI5+7BHoTJkgyKoeUMQjVRn+bueH81nUdGjuflYlIxENTqW36wsAeqYkLGoy+tVUN9cPNtfjuEH3HkKmf+o+IPmUtPU1paUhMzTTD5FllDyeQCeIMibJVzcT7eW4u1QahUdK3YOoOL7TQK2IiEpJ1vnDqxNGY3m+FinsnO9W3lMlQYRMy+VzaDSm43gyIud92B5/YtqjcWZa8PXjNsjmErDvT4fPqiM/zn8BzRr0TSTeG11EWMgyioGSH9y+5OSoZmo2622sm8jxbAVm+BSJ',
        ],
    ],
    'saml20.sign.assertion' => true,
];