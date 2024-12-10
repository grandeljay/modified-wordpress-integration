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
$_GET['post'] = $post_translations[$x_default_lng] ?? $post->getId();

$x_default_link = xtc_href_link(
      basename($PHP_SELF),
      [...]
```

And to add the blog parameters to the list of allowed (and expected) url
parameters:

```php
$_GET['post'] = $post_translations[$key] ?? $post->getId();

$x_default_link = xtc_href_link(
    basename($PHP_SELF),
    xtc_get_all_get_params_include(
        array(
            [...]

            'post',
            'tag_id',
            'category_id',
        )
    )
    .'language='.$x_default_lng.$page_param,
    'NONSSL',
    false
);
```
