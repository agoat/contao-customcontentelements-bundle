# Contao Content Elements extension
Contao 4 bundle

___

## ATTENTION
There is a complete database redesign in Version 2+. A direct upgrade from 1.x to 2.x is not possible.

___

## Installation


Run the following command in your project directory:

```bash
php composer.phar require agoat/contao-contentelements "^2.0"
```


## Activation


Adjust your `app/AppKernel.php` file:

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // somewhere after Contao\CoreBundle\ContaoCoreBundle(),
            // ...
            new Agoat\ContentElementsBundle\AgoatContentElementsBundle(),
        ];
    }
}
```
