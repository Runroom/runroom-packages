RunroomSortableBehaviorBundle
========================

[![Latest Stable Version](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/v/stable)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Latest Unstable Version](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/v/unstable)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![License](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/license)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)

[![Total Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/downloads)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Monthly Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/d/monthly)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)
[![Daily Downloads](https://poser.pugx.org/runroom-packages/sortable-behavior-bundle/d/daily)](https://packagist.org/packages/runroom-packages/sortable-behavior-bundle)

This bundle gives the ability to define sortable entities and to be able to sort the using Sonata Backoffice. It is inspired on: [pixSortableBehaviorBundle](https://github.com/pix-digital/pixSortableBehaviorBundle).

## Installation

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```
composer require runroom-packages/sortable-behavior-bundle
```

### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Runroom\SortableBehaviorBundle\RunroomSortableBehaviorBundle::class => ['all' => true],
];
```

## Usage

```php

namespace App\Admin;

use Runroom\SortableBehaviorBundle\Admin\AbstractSortableAdmin;

class ExampleAdmin extends AbstractSortableAdmin
{
    // you can use SortableAdminTrait instead AbstractSortableAdmin

    protected function configureListFields(ListMapper $list): void
    {
        $list
        // ... some fields
            ->add('_action', null, [
                'actions' => [
                    // ... some actions 
                    'move'   => ['template' => '@RunroomSortableBehavior/sort.html.twig'],
                ],
            ]);
    }
}
```

### Configuration
```yaml
# app/config/config.yml
runroom_sortable_behavior:
    db_driver: orm # possible values: orm, mongodb 
    position_field:
        default: position
        entities:
            AppBundle\Entity\Foobar: order
            AppBundle\Entity\Baz: rang
    sortable_groups:
        entities:
            AppBundle\Entity\Baz: [ group ]
            
```

### Use a draggable list instead of up/down buttons

In order to use a draggable list instead of up/down buttons, change the template in the `move` action to `@RunroomSortableBehavior/sort.html.twig`.

```php
<?php

    // ClientAdmin.php
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('_action', null, array(
                'actions' => array(
                    'move' => array(
                        'template' => '@RunroomSortableBehavior/sort_drag_drop.html.twig',
                        'enable_top_bottom_buttons' => true, //optional
                    ),
                ),
            ))
        ;
    }
```
## Contribute

The sources of this package are contained in the Runroom monorepo. We welcome contributions for this package on [runroom/runroom-packages](https://github.com/Runroom/runroom-packages).

## License

This bundle is under the [MIT license](LICENSE).
