# WordPress Integration

## Installation

If the boxes are not auto loaded in your template, add the following code:

`/templates/tpl_modified_responsive_6/source/boxes.php`

```php
/**
 * WordPress Integration
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */
include_once DIR_FS_BOXES . 'grandeljay_wordpress_integration.php';
/** */
```

### SEO

In `/includes/modules/metatags.php` add the following snippets.

For the alternate default language insert:

```php
$canonical_flag = true;
$x_default_flag = true;
$x_default_lng = ((defined('MODULE_MULTILANG_X_DEFAULT')) ? MODULE_MULTILANG_X_DEFAULT : 'en');
$x_default_link = xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params_include(array('products_id', 'cPath', 'manufacturers_id', 'coID')).'language='.$x_default_lng.$page_param, 'NONSSL', false);

/**
 * WordPress Integration
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */
include DIR_FS_EXTERNAL . 'grandeljay/wordpress_integration/metatags_x_default.php';
/** */
```

And to add the blog parameters to the list of allowed (and expected) url parameters:

```php
$alternate_link = xtc_href_link(basename($PHP_SELF), xtc_get_all_get_params_include(array('products_id', 'cPath', 'manufacturers_id', 'coID')).'language='.$key.$page_param, 'NONSSL', false);

/**
 * WordPress Integration
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 */
include DIR_FS_EXTERNAL . 'grandeljay/wordpress_integration/metatags_alternate.php';
/** */
```
