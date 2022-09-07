RunroomFormHandlerBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/form-handler-bundle/v/stable)](https://packagist.org/packages/runroom-packages/form-handler-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/form-handler-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/form-handler-bundle)
[![License](https://poser.pugx.org/runroom-packages/form-handler-bundle/license)](https://packagist.org/packages/runroom-packages/form-handler-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/form-handler-bundle/downloads)](https://packagist.org/packages/runroom-packages/form-handler-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/form-handler-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/form-handler-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/form-handler-bundle/d/daily)](https://packagist.org/packages/runroom-packages/form-handler-bundle)

This bundle gives a tool to handle with Forms on Symfony without having to rewrite a lot of logic that it is always the same.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/form-handler-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\RenderEventBundle\RunroomFormHandlerBundle::class => ['all' => true],
];
```

## Contribute
The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
