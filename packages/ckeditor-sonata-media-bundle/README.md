RunroomCkeditorSonataMediaBundle
================================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/ckeditor-sonata-media-bundle/v/stable)](https://packagist.org/packages/runroom-packages/ckeditor-sonata-media-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/ckeditor-sonata-media-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/ckeditor-sonata-media-bundle)
[![License](https://poser.pugx.org/runroom-packages/ckeditor-sonata-media-bundle/license)](https://packagist.org/packages/runroom-packages/ckeditor-sonata-media-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/ckeditor-sonata-media-bundle/downloads)](https://packagist.org/packages/runroom-packages/ckeditor-sonata-media-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/ckeditor-sonata-media-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/ckeditor-sonata-media-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/ckeditor-sonata-media-bundle/d/daily)](https://packagist.org/packages/runroom-packages/ckeditor-sonata-media-bundle)

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/ckeditor-sonata-media-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\RenderEventBundle\RunroomCkeditorSonataMediaBundle::class => ['all' => true],
];
```

## License

This bundle is under the [MIT license](LICENSE).
