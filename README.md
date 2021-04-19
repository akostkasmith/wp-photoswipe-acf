# presenty-box

## Presenty Box with ACF

Presenty Box can be used with ACF. Simply use the following code below where the name of the ACF field is called gallery. Ensure that the images return an array in the ACF settings.

```php
$images = get_field('gallery');
if( $images ):
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
