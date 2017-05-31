# securid PHP library

A quick and dirty implementation of the RSA Authentication Manager API in PHP.

## Installation

The preferred method of installation is via [Packagist][] and [Composer][]. Run the following command to install the package and add it as a requirement to your project's `composer.json`:

```bash
composer require dangoscomb/securid
```


## Example

```php

$sess = new \SecurID\Session('AGENT_ID','https://rsa.yourdomain.com:5555','agentKey', [ 'verify' => false ] );
$sess->init('USERNAME');
if($sess->verify('PIN+KEY')) {
        echo "\nAUTHED\n";
}
else {
        echo "\nFAIL :(\n";
}

```