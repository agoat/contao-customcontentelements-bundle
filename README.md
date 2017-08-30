# Contao Content Elements extension
Contao 4 bundle

___

## ATTENTION
There will be a database redesign (see [#140](https://github.com/agoat/contao-contentblocks-bundle/issues/140)). The new database scheme will be used in version 2.0+.
This may result in a more difficult upgrade process to the 2.0 version.

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
            new new Agoat\ContentElementsBundle\AgoatContentElementsBundle(),
        ];
    }
}
```
