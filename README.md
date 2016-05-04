LswGuzzleBundle
===============

![screenshot](http://www.leaseweblabs.com/wp-content/uploads/2014/03/guzzle_bundle.png)

The LswGuzzleBundle adds Guzzle API call functionality to your Symfony2 application.
It is easy to use from the code and is aimed to have full debugging capabilities.

[Read the LeaseWebLabs blog about LswGuzzleBundle](http://www.leaseweblabs.com/2014/03/guzzle-symfony2-bundle-curl-api-calling/)

[Read the Guzzle documentation](http://docs.guzzlephp.org/en/stable/)

[Guzzle API documentation](http://api.guzzlephp.org/)

## Requirements

* PHP 5.3 with cURL support
* Symfony 2.3

## Installation

Installation is broken down in the following steps:

1. Download LswGuzzleBundle using composer
2. Enable the Bundle
3. Make sure the cURL module in PHP is enabled

### Step 1: Download LswGuzzleBundle using composer

Tell composer to download the bundle by running the command:

``` bash
$ composer require leaseweb/guzzle-bundle
```

Composer will install the bundle to your project's `vendor/leaseweb` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Lsw\GuzzleBundle\LswGuzzleBundle(),
    );
}
```

### Step 3: Make sure the cURL module in PHP is enabled

On a Debian based distribution (like Ubuntu) the package is called "php5-curl" and
can be installed using the following commands:

``` bash
$ sudo apt-get install php5-curl
$ sudo service apache2 restart
```

On a RedHat based distribution (like CentOS) the package is called "php-curl" and
can be installed using the following commands:

``` bash
$ sudo yum install php-curl
$ sudo service httpd restart
```

To check this create and run a PHP file with the following contents:

``` php
<?php phpinfo() ?>
```

It should display that the option "cURL support" is set to "enabled".

This package should work on a Windows installation as well provided the CURL support
is enabled in PHP.

## Configuration

These is an example of Guzzle client based on a service description (including cURL options):


```
lsw_guzzle:
    clients:
        test:
            config:
                json_objects: true
                curl.options:
                    CURLOPT_CONNECTTIMEOUT: 10
                    CURLOPT_SSL_VERIFYPEER: false
                    CURLOPT_USERAGENT: "LeaseWeb API Caller"
                    CURLOPT_FOLLOWLOCATION: true
                    CURLOPT_SSLVERSION: 3
            description:
                baseUrl: "http://sf2testproject.dev"
                operations: 
                    test: 
                        httpMethod: "GET"
                        uri: "/app_dev.php/demo/?a=b"
                    json: 
                        httpMethod: "GET"
                        uri: "/app_dev.php/demo/json"
                    json_post: 
                        httpMethod: "POST"
                        uri: "/app_dev.php/demo/{action}"
                        parameters:
                            testvalue:
                                location: xml
                            action:
                                location: uri
```

## Usage

This is how to consume the described service commands:

```
    	$response = $this->get('guzzle.test')->getCommand('test')->execute();
    	$response = $this->get('guzzle.test')->getCommand('json')->execute();
    	$response = $this->get('guzzle.test')->getCommand('json_post',array('action'=>'json','testvalue'=>666))->execute();
```

## License

This bundle is under the MIT license.

