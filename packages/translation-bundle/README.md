RunroomTranslationBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom/translation-bundle/v/stable)](https://packagist.org/packages/runroom/translation-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom/translation-bundle/v/unstable)](https://packagist.org/packages/runroom/translation-bundle)
[![License](https://poser.pugx.org/runroom/translation-bundle/license)](https://packagist.org/packages/runroom/translation-bundle)

[![Total Downloads](https://poser.pugx.org/runroom/translation-bundle/downloads)](https://packagist.org/packages/runroom/translation-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom/translation-bundle/d/monthly)](https://packagist.org/packages/runroom/translation-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom/translation-bundle/d/daily)](https://packagist.org/packages/runroom/translation-bundle)

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom/translation-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\TranslationBundle\TranslationBundle::class => ['all' => true],
];
```

### Update doctrine schema

Finally, execute doctrine schema update to create the new tables:

```
console doctrine:schema:update --force
```

## License

This bundle is under the [MIT license](LICENSE.md).
