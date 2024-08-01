RunroomDoctrineTranslatableBundle
=================================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/doctrine-translatable-bundle/v/stable)](https://packagist.org/packages/runroom-packages/doctrine-translatable-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/doctrine-translatable-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/doctrine-translatable-bundle)
[![License](https://poser.pugx.org/runroom-packages/doctrine-translatable-bundle/license)](https://packagist.org/packages/runroom-packages/doctrine-translatable-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/doctrine-translatable-bundle/downloads)](https://packagist.org/packages/runroom-packages/doctrine-translatable-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/doctrine-translatable-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/doctrine-translatable-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/doctrine-translatable-bundle/d/daily)](https://packagist.org/packages/runroom-packages/doctrine-translatable-bundle)

This bundle gives the ability to define and use translations directly on the Sonata Backoffice as a replacement for `yaml` translations of Symfony.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
composer require runroom-packages/doctrine-translatable-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\DoctrineTranslatableBundle\RunroomDoctrineTranslatableBundle::class => ['all' => true],
];
```

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
