Motivation
============

Use a REST API as a date storage.

Installation
============

Step 1: Download the Bundle
---------------------------

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require highco/api-consumer
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Enable the Bundle
-------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Highco\ApiConsumerBundle\HighcoApiConsumerBundle(),
        );

        // ...
    }

    // ...
}
```

Step 3: Bundle configuration
-------------------------
```yml
api_consumer:
    # (de)activate logging/profiler; default: %kernel.debug%
    logging: true

    clients:
        api_payment:
            base_url: "http://api.domain.tld"
            manager_class: "AppBundle\Manager\CustomManager"
            # guzzle client options (full description here: https://guzzle.readthedocs.org/en/latest/request-options.html)
            # NOTE: "headers" option is not accepted here as it is provided as described above.
            options:
                auth:
                    - acme     # login
                    - pa55w0rd # password

                headers:
                    Accept: "application/json"

                timeout: 30
            entities:
                - {'class': AppBundle\Entity\Payment, 'route_prefix': payment, 'repository_class': 'AppBundle\Repository\PaymentRepository'}
                - {'class': AppBundle\Entity\Payout, 'route_prefix': payout}

        api_crm:
            base_url: "http://api.crm.tld"
            headers:
                Accept: "application/json"
            entities:
                - {'class': AppBundle\Entity\User, 'route_prefix': users}

```
