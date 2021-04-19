=== Presenty Box ===
Contributors: FirstPage Marketing
Donate link:
Tags:
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 0.1

A gallery plugin for displaying images in a variety of formats.

== Description ==

A gallery plugin for displaying images in a variety of formats.

== Installation ==

## Presenty Box with ACF

Presenty Box can be used with ACF. Simply use the following code below where the name of the ACF field is called gallery.

```php
$images = get_field('gallery');
if( $images ): ?>
        <?php /*               
    <ul class="grid">
        <?php foreach( $images as $image ): ?>
            <li class="grid-item">
                <a href="<?php echo $image['url']; ?>" class="group1">
                        <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
                        <?php print_r($image); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    */ ?>
    <?php 
        $i = 0;
        $t = '';
        foreach ($images as $val) {
            if ($i == 1) {
                $t .= ',' . $val['ID'];
            } else {
                $t .= $val['ID'];
                $i = 1;
            }
        }
    
    echo do_shortcode('[gallery ids="' . $t .'"]');
    
endif; 
```


== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 0.1 =
- Initial Revision
