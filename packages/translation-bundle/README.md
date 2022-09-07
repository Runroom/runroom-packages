RunroomTranslationBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/translation-bundle/v/stable)](https://packagist.org/packages/runroom-packages/translation-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/translation-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/translation-bundle)
[![License](https://poser.pugx.org/runroom-packages/translation-bundle/license)](https://packagist.org/packages/runroom-packages/translation-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/translation-bundle/downloads)](https://packagist.org/packages/runroom-packages/translation-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/translation-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/translation-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/translation-bundle/d/daily)](https://packagist.org/packages/runroom-packages/translation-bundle)

This bundle gives the ability to define and use translations directly on the Sonata Backoffice as a replacement for `yaml` translations of Symfony.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/translation-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\TranslationBundle\RunroomTranslationBundle::class => ['all' => true],
];
```

### Update doctrine schema

Finally, execute doctrine schema update to create the new tables:

```
console doctrine:schema:update --force
```

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
