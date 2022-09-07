RunroomRenderEventBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/render-event-bundle/v/stable)](https://packagist.org/packages/runroom-packages/render-event-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/render-event-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/render-event-bundle)
[![License](https://poser.pugx.org/runroom-packages/render-event-bundle/license)](https://packagist.org/packages/runroom-packages/render-event-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/render-event-bundle/downloads)](https://packagist.org/packages/runroom-packages/render-event-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/render-event-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/render-event-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/render-event-bundle/d/daily)](https://packagist.org/packages/runroom-packages/render-event-bundle)

This bundle wraps the twig render to allow modifications of the parameters passed to it using the event dispatcher.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/render-event-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\RenderEventBundle\RunroomRenderEventBundle::class => ['all' => true],
];
```

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
