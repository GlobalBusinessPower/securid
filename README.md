# securid PHP library

A quick and dirty implementation of the RSA Authentication Manager API in PHP.

## Installation

The preferred method of installation is via [Packagist][] and [Composer][]. Run the following command to install the package and add it as a requirement to your project's `composer.json`:

```bash
composer require dangoscomb/securid
```


## Example (Original)

```php

$sess = new \SecurID\Session('AGENT_ID','https://rsa.yourdomain.com:5555','ACCESS_KEY', [ 'verify' => false ] );
$sess->init('USERNAME');
if($sess->verify('PIN+KEY')) {
        echo "\nAUTHED\n";
}
else {
        echo "\nFAIL :(\n";
}

```

## Example (Updated July 2024 by Jason Mediavilla)

```php
$username = $this->input->post('username'); //User Name used in LDAP Protocol or Domain User ID
$otp = $this->input->post('otp'); //OTP Number from Mobile Phone (Token Number Codes)

$AGENT_ID = "<IP Address of the Server>";
$ACCESS_KEY = "<Access Key Generated in RSA API Key Management>"; 
$URL="<URL of the MFA Authenticator Server>";

$sess = new \SecurID\Session($AGENT_ID,$URL,$ACCESS_KEY, [ 'verify' => false ] ); //Call new Session in RSA MFA
$sess->init($username); //Initialized User Name

if($sess->verify($otp)) //Verify the OTP Code from Mobile Phone
{
  // RSA MFA Authenticated -> Proceed to Login Page;
}

```
