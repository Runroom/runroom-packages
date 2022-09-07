RunroomCookiesBundle
====================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/cookies-bundle/v/stable)](https://packagist.org/packages/runroom-packages/cookies-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/cookies-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/cookies-bundle)
[![License](https://poser.pugx.org/runroom-packages/cookies-bundle/license)](https://packagist.org/packages/runroom-packages/cookies-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/cookies-bundle/downloads)](https://packagist.org/packages/runroom-packages/cookies-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/cookies-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/cookies-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/cookies-bundle/d/daily)](https://packagist.org/packages/runroom-packages/cookies-bundle)

This bundle allows you to manage your cookies with GDPR compliance.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/cookies-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\CookiesBundle\RunroomCookiesBundle::class => ['all' => true],
];
```

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
