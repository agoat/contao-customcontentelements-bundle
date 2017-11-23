# Custom content elements extension for Contao 4

[![Version](https://img.shields.io/packagist/v/agoat/contao-customcontentelements.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-customcontentelements)
[![License](https://img.shields.io/packagist/l/agoat/contao-customcontentelements.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-customcontentelements)
[![Downloads](https://img.shields.io/packagist/dt/agoat/contao-customcontentelements.svg?style=flat-square)](http://packagist.org/packages/agoat/contao-customcontentelements) 
> (The download counter has been reset due to name change)<sub>

## About
Create your own **content elements** with individual input fields.

Almost any input mask for content elements can be created, from very simple text entries to complex input structures with a choice of images, pages and various options.

**Custom content elements** consist of a **templat**e and any number of **patterns** that provide the various input fields. The patterns can be configured in such a way that they give the content data to the template, where it can be output into any corresponding HTML structure.

This makes it possible to implement both simple widgets (such as 'hero images') and extensive information boards (like 'team pages' with names, e-mails, numbers and addresses of the employees). And all the content can be accessed in a simple and elegant way.

## Notice
There was a complete database redesign from Version `1.x` to `2.x`. A direct upgrade is not possible and all custom content elements must be re-created.

## Install
### Contao manager
Search for the package and install it
```bash
agoat/contao-customcontentelements
```

### Managed edition
Add the package
```bash
# Using the composer
composer require agoat/contao-customcontentelements
```
Registration and configuration is done by the manager-plugin automatically.

### Standard edition
Add the package
```bash
# Using the composer
composer require agoat/contao-customcontentelements
```
Register the bundle in the AppKernel
```php
# app/AppKernel.php
class AppKernel
{
    // ...
    public function registerBundles()
    {
        $bundles = [
            // ...
            // after Contao\CoreBundle\ContaoCoreBundle
            new Agoat\CustomContentElementsBundle\AgoatCustomContentElementsBundle (),
        ];
    }
}
```

