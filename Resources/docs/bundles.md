# As a Symfony bundle user

## What can I do ?

This module can natively use Symfony bundles into the Drupal application, but
you must ackowledge the fact that you cannot use the whole Symfony API:

 *  you may use ```*Bundle``` class, the ```Extension``` class, and provide
    services via the ```Resource/config``` folder;

 *  you may use Twig natively (the ```TwigBundle``` is automatically registered
    in the kernel if the classes are found) where template naming convention
    are the Symfony naming conventions;

 *  this module provide an abstract controller class
    ```MakinaCorpus\Drupal\Sf\Controller``` that you may extend in order to
    provide controllers, you must then now that your controllers won't behave
    like symfony components, but there return will only be used to fill in the
    page ```content``` Drupal region;

 *  you may use anything you want as long as you set it as composer dependencies
    case in which you might want to see the next chapter. Please note that it
    will only work to the packages you pulled extend.

## Bring the necessary dependencies

You must require a few packages for this to work, if you want twig you must:
```sh
composer require symfony/templating
composer require symfony/twig-bundle
```

You probably also want to add the ```twig/extensions``` package.

Alternatively, if you are not afraid and have beard, you could just:

```sh
composer require symfony/symfony
```

Which should work gracefully, note that we are *not* using the Symfony full
stack so most of its code won't be in use.

## Register one or more bundles

Bundle registration must happen before the ```sf_dic``` module ```hook_boot()```
implementation, this means that you have only one place where you can do it:
by implementing your own ```hook_boot()``` implementation and setting the
weight of your module under the ```sf_dic``` module's weight.

Once you did that, and I know will know how to do it, you may register your
bundles this way:

```php
/**
 * Implements hook_boot().
 */
function MYMODULE_boot() {
  \Drupal::registerBundles([
    new MyVendor\SomeBundle\MyVendorSomeBundle(),
    // [...]
  ]);
}
```

## Using the router

You can use the Symfony router, and build 100% Symfony compatible code, please see
[https://symfony.com/doc/current/book/routing.html](https://symfony.com/doc/current/book/routing.html)

### Register your bundle's services.yml file

First, you must use the ```symfony/symfony``` package as a dependency, then add
a few variables to your ```settings.php``` file:
```php
$conf['kernel.symfony_all_the_way'] = true;
$conf['kernel.symfony_router_enable'] = true;
```

Then add a ```sites/default/routing.yml``` file, containing:

```yaml
my_bundle:
    resource: "@MyVendorMyBundle/Resources/config/routing.yml"
    prefix: /
```