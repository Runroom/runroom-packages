RunroomRedirectionBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/redirection-bundle/v/stable)](https://packagist.org/packages/runroom-packages/redirection-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/redirection-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/redirection-bundle)
[![License](https://poser.pugx.org/runroom-packages/redirection-bundle/license)](https://packagist.org/packages/runroom-packages/redirection-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/redirection-bundle/downloads)](https://packagist.org/packages/runroom-packages/redirection-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/redirection-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/redirection-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/redirection-bundle/d/daily)](https://packagist.org/packages/runroom-packages/redirection-bundle)

This bundle gives the ability to manage redirections on a Sonata Backoffice.

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```bash
composer require runroom-packages/redirection-bundle
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

## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
