RunroomRedirectionBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom/redirection-bundle/v/stable)](https://packagist.org/packages/runroom/redirection-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom/redirection-bundle/v/unstable)](https://packagist.org/packages/runroom/redirection-bundle)
[![License](https://poser.pugx.org/runroom/redirection-bundle/license)](https://packagist.org/packages/runroom/redirection-bundle)

[![Total Downloads](https://poser.pugx.org/runroom/redirection-bundle/downloads)](https://packagist.org/packages/runroom/redirection-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom/redirection-bundle/d/monthly)](https://packagist.org/packages/runroom/redirection-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom/redirection-bundle/d/daily)](https://packagist.org/packages/runroom/redirection-bundle)

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom/redirection-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\RedirectionBundle\RunroomRedirectionBundle::class => ['all' => true],
];
```

### Update doctrine schema

Finally, execute doctrine schema update to create the new tables:

```
console doctrine:schema:update --force
```

## License

This bundle is under the [MIT license](LICENSE.md).
