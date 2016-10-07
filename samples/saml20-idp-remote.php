<?php
/**
 * Preconfigured IdPs to use in docker samples
 */
$metadata['https://idp.testshib.org/idp/shibboleth'] = array (
  'entityid' => 'https://idp.testshib.org/idp/shibboleth',
  'description' => 
  array (
    'en' => 'TestShib Two Identity Provider',
  ),
  'OrganizationName' => 
  array (
    'en' => 'TestShib Two Identity Provider',
  ),
  'name' => 
  array (
    'en' => 'TestShib Test IdP',
  ),
  'OrganizationDisplayName' => 
  array (
    'en' => 'TestShib Two',
  ),
  'url' => 
  array (
    'en' => 'http://www.testshib.org/testshib-two/',
  ),
  'OrganizationURL' => 
  array (
    'en' => 'http://www.testshib.org/testshib-two/',
  ),
  'contacts' => 
  array (
    0 => 
    array (
      'contactType' => 'technical',
      'givenName' => 'Nate',
      'surName' => 'Klingenstein',
      'emailAddress' => 
      array (
        0 => 'ndk@internet2.edu',
      ),
    ),
  ),
  'metadata-set' => 'saml20-idp-remote',
  'SingleSignOnService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
      'Location' => 'https://idp.testshib.org/idp/profile/Shibboleth/SSO',
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
      'Location' => 'https://idp.testshib.org/idp/profile/SAML2/POST/SSO',
    ),
    2 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
      'Location' => 'https://idp.testshib.org/idp/profile/SAML2/Redirect/SSO',
    ),
    3 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://idp.testshib.org/idp/profile/SAML2/SOAP/ECP',
    ),
  ),
  'SingleLogoutService' => 
  array (
  ),
  'ArtifactResolutionService' => 
  array (
    0 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:1.0:bindings:SOAP-binding',
      'Location' => 'https://idp.testshib.org:8443/idp/profile/SAML1/SOAP/ArtifactResolution',
      'index' => 1,
    ),
    1 => 
    array (
      'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:SOAP',
      'Location' => 'https://idp.testshib.org:8443/idp/profile/SAML2/SOAP/ArtifactResolution',
      'index' => 2,
    ),
  ),
  'NameIDFormats' => 
  array (
    0 => 'urn:mace:shibboleth:1.0:nameIdentifier',
    1 => 'urn:oasis:names:tc:SAML:2.0:nameid-format:transient',
  ),
  'keys' => 
  array (
    0 => 
    array (
      'encryption' => true,
      'signing' => true,
      'type' => 'X509Certificate',
      'X509Certificate' => '
                            MIIDAzCCAeugAwIBAgIVAPX0G6LuoXnKS0Muei006mVSBXbvMA0GCSqGSIb3DQEB
                            CwUAMBsxGTAXBgNVBAMMEGlkcC50ZXN0c2hpYi5vcmcwHhcNMTYwODIzMjEyMDU0
                            WhcNMzYwODIzMjEyMDU0WjAbMRkwFwYDVQQDDBBpZHAudGVzdHNoaWIub3JnMIIB
                            IjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAg9C4J2DiRTEhJAWzPt1S3ryh
                            m3M2P3hPpwJwvt2q948vdTUxhhvNMuc3M3S4WNh6JYBs53R+YmjqJAII4ShMGNEm
                            lGnSVfHorex7IxikpuDPKV3SNf28mCAZbQrX+hWA+ann/uifVzqXktOjs6DdzdBn
                            xoVhniXgC8WCJwKcx6JO/hHsH1rG/0DSDeZFpTTcZHj4S9MlLNUtt5JxRzV/MmmB
                            3ObaX0CMqsSWUOQeE4nylSlp5RWHCnx70cs9kwz5WrflnbnzCeHU2sdbNotBEeTH
                            ot6a2cj/pXlRJIgPsrL/4VSicPZcGYMJMPoLTJ8mdy6mpR6nbCmP7dVbCIm/DQID
                            AQABoz4wPDAdBgNVHQ4EFgQUUfaDa2mPi24x09yWp1OFXmZ2GPswGwYDVR0RBBQw
                            EoIQaWRwLnRlc3RzaGliLm9yZzANBgkqhkiG9w0BAQsFAAOCAQEASKKgqTxhqBzR
                            OZ1eVy++si+eTTUQZU4+8UywSKLia2RattaAPMAcXUjO+3cYOQXLVASdlJtt+8QP
                            dRkfp8SiJemHPXC8BES83pogJPYEGJsKo19l4XFJHPnPy+Dsn3mlJyOfAa8RyWBS
                            80u5lrvAcr2TJXt9fXgkYs7BOCigxtZoR8flceGRlAZ4p5FPPxQR6NDYb645jtOT
                            MVr3zgfjP6Wh2dt+2p04LG7ENJn8/gEwtXVuXCsPoSCDx9Y0QmyXTJNdV1aB0AhO
                            RkWPlFYwp+zOyOIR+3m1+pqWFpn0eT/HrxpdKa74FA3R2kq4R7dXe4G0kUgXTdqX
                            MLRKhDgdmA==
                        ',
    ),
  ),
  'scope' => 
  array (
    0 => 'testshib.org',
  ),
  'UIInfo' => 
  array (
    'DisplayName' => 
    array (
      'en' => 'TestShib Test IdP',
    ),
    'Description' => 
    array (
      'en' => 'TestShib IdP. Use this as a source of attributes
                        for your test SP.',
    ),
    'InformationURL' => 
    array (
    ),
    'PrivacyStatementURL' => 
    array (
    ),
    'Logo' => 
    array (
      0 => 
      array (
        'url' => 'https://www.testshib.org/testshibtwo.jpg',
        'height' => 88,
        'width' => 253,
      ),
    ),
  ),
);