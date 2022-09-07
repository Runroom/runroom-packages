RunroomBasicPageBundle
======================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/basic-page-bundle/v/stable)](https://packagist.org/packages/runroom-packages/basic-page-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/basic-page-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/basic-page-bundle)
[![License](https://poser.pugx.org/runroom-packages/basic-page-bundle/license)](https://packagist.org/packages/runroom-packages/basic-page-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/basic-page-bundle/downloads)](https://packagist.org/packages/runroom-packages/basic-page-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/basic-page-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/basic-page-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/basic-page-bundle/d/daily)](https://packagist.org/packages/runroom-packages/basic-page-bundle)

This bundle allows you to generate pages on your website with just a title and a wysiwyg. Using Sonata as the backoffice to manage them.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/basic-page-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\RenderEventBundle\RunroomBasicPageBundle::class => ['all' => true],
];
```

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
