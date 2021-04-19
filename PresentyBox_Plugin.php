<?php


include_once('PresentyBox_LifeCycle.php');

class PresentyBox_Plugin extends PresentyBox_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            // 'ATextInput' => array(__('Enter in some text', 'my-awesome-plugin')),
            'HTMLCTA' => array(__('HTML content for hover CTA', 'my-awesome-plugin')),
            'Color' => array(__('Background Colour', 'my-awesome-plugin')),
            'ReplaceGallery' => array(__('Replace Wordpress media gallery', 'my-awesome-plugin'), 'false', 'true'),
            'GalleryDisplay' => array(__('Gradient Location', 'my-awesome-plugin'),
                                        'TopLeft', 'BottomRight', 'BottomLeft', 'TopRight')
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Presenty Box';
    }

    protected function getMainPluginFileName() {
        return 'presenty-box.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        add_action('admin_enqueue_styles', array(&$this, 'addColorPicker'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
               wp_enqueue_script('jquery');
               wp_enqueue_style('photoswipe', plugins_url('/css/photoswipe.css', __FILE__));
               wp_enqueue_style('photoswipe-default', plugins_url('/css/default-skin.css', __FILE__));
               
               wp_enqueue_script('photoswipe', plugins_url('/js/photoswipe.min.js', __FILE__));
               wp_enqueue_script('photoswipe-ui', plugins_url('/js/photoswipe-ui-default.min.js', __FILE__));
               wp_enqueue_script('photoswipe-support', plugins_url('/js/photoswipe-support.js', __FILE__));

               wp_enqueue_script('masonryjs', plugins_url('/js/masonry.pkgd.min.js', __FILE__));



        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39

        if ($this->getOption('ReplaceGallery') == 'true') {
            remove_shortcode('gallery');
            add_shortcode('gallery', array($this, 'masonrygallery_function'));
        } else {
            add_shortcode('masonrygallery', array($this, 'masonrygallery_function'));
        }
        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }

    public function addColorPicker() {
        // Add the color picker css file       
        wp_enqueue_style( 'wp-color-picker' ); 
         
        // Include our custom jQuery file with WordPress Color Picker dependency
        wp_enqueue_script( 'custom-script-handle', plugins_url( 'custom-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
    }

    public function masonrygallery_function($atts) {
            
            global $post;
            $pid = $post->ID;
            $gallery = "";

            if (empty($pid)) {$pid = $post['ID'];}

            if (!empty( $atts['ids'] ) ) {
                $atts['orderby'] = 'post__in';
                $atts['include'] = $atts['ids'];
            }

            extract(shortcode_atts(array('orderby' => 'menu_order ASC, ID ASC', 'include' => '', 'id' => $pid, 'itemtag' => 'dl', 'icontag' => 'dt', 'captiontag' => 'dd', 'columns' => 3, 'size' => 'large', 'link' => 'file'), $atts));
                
            $args = array('post_type' => 'attachment', 'post_status' => 'inherit', 'post_mime_type' => 'image', 'orderby' => $orderby);

            if (!empty($include)) {$args['include'] = $include;}
            else {
                $args['post_parent'] = $id;
                $args['numberposts'] = -1;
            }

            if ($args['include'] == "") { $args['orderby'] = 'date'; $args['order'] = 'asc';}
             
            wp_enqueue_style('presentybox', plugins_url('/css/presentybox.css', __FILE__));

            $images = get_posts($args);
            $galleryDisplay = $this->getOption('GalleryDisplay');
            
            $gallery = '<div class="masonry_gallery">';
            $htmlcta = $this->getOption('HTMLCTA');       
            $style = '';
            if (strlen($this->getOption('Color')) > 0) {
                $startColor = $this->getOption('Color');
                $startColor = $this->hex2rgba($startColor,0.65);
                $endColor = $this->getOption('Color');
                $endColor =  $this->hex2rgba($endColor,0.01);
                
                $style .= "
                <style>
                body .masonry_gallery .masonry-item figcaption.TopLeft:before {                                 
                    background: -moz-linear-gradient(-45deg, " . $startColor. " 0%, " . $endColor . " 100%);
                    background: -webkit-linear-gradient(-45deg, " . $startColor. " 0%, " . $endColor . " 100%);
                    background: linear-gradient(135deg,  " . $startColor. " 0%, " . $endColor . " 100%);
                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#a6000000', endColorstr='#00000000', GradientType=1);
                }
                body .masonry_gallery .masonry-item figcaption.BottomLeft:before {                                 
                    background: -moz-linear-gradient(45deg,  " . $startColor. " 0%, " . $endColor . " 100%);
                    background: -webkit-linear-gradient(45deg,  " . $startColor. " 0%, " . $endColor . " 100%);
                    background: linear-gradient(45deg,  " . $startColor. " 0%, " . $endColor . " 100%);
                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#a6000000', endColorstr='#00000000', GradientType=1);
                }
                body .masonry_gallery .masonry-item figcaption.TopRight:before {                                 
                    background: -moz-linear-gradient(45deg,  " . $endColor. " 0%, " . $startColor . " 100%);
                    background: -webkit-linear-gradient(45deg, r " . $endColor. " 0%, " . $startColor . " 100%);
                    background: linear-gradient(45deg,  " . $endColor. " 0%, " . $startColor . " 100%);
                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#a6000000', endColorstr='#00000000', GradientType=1);
                }
                body .masonry_gallery .masonry-item figcaption.BottomRight:before {                                 
                    background: -moz-linear-gradient(-45deg,  " . $endColor. " 0%, " . $startColor . " 100%);
                    background: -webkit-linear-gradient(-45deg,  " . $endColor. " 0%, " . $startColor . " 100%);
                    background: linear-gradient(135deg,  " . $endColor. " 0%, " . $startColor . " 100%);
                    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#a6000000', endColorstr='#00000000', GradientType=1);
                }
                </style>
                ";
            }      

            foreach ( $images as $image ) {
                //print_r($image); /*see available fields*/
                $thumbnail = wp_get_attachment_image_src($image->ID, 'large');
                
                $size = wp_get_attachment_metadata($image->ID);
                $thumbnail = $thumbnail[0];

                $gallery .= "

                    <figure class='masonry-item' >
                        <a href='".$thumbnail."' itemprop='contentUrl' data-size='"  . $size['width'] . "x" . $size['height'] . "' >
                             <img src='".$thumbnail."' >
                        </a>
                         
                        <figcaption itemprop='caption description' class='" . $galleryDisplay . "'>
                            <div class='img-title'>".$image->post_title."<div class='img-caption'>".$image->post_excerpt."</div></div>
                            <div class='cta-hover'>" . stripslashes($htmlcta) . "</div>    
                        </figcaption>
                    </figure>
                    ";
            }
            $gallery .= '</div>';
            $gallery .= '<!-- Root element of PhotoSwipe. Must have class pswp. -->
                        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">

                            <!-- Background of PhotoSwipe.   Its a separate element as animating opacity is faster than rgba(). -->
                            <div class="pswp__bg"></div>

                            <!-- Slides wrapper with overflow:hidden. -->
                            <div class="pswp__scroll-wrap">

                                <!-- Container that holds slides. 
                                    PhotoSwipe keeps only 3 of them in the DOM to save memory.
                                    Dont modify these 3 pswp__item elements, data is added later on. -->
                                <div class="pswp__container">
                                    <div class="pswp__item"></div>
                                    <div class="pswp__item"></div>
                                    <div class="pswp__item"></div>
                                </div>

                                <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
                                <div class="pswp__ui pswp__ui--hidden">

                                    <div class="pswp__top-bar">

                                        <!--  Controls are self-explanatory. Order can be changed. -->

                                        <div class="pswp__counter"></div>

                                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>

                                        <button class="pswp__button pswp__button--share" title="Share"></button>

                                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>

                                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>

                                        
                                        <!-- element will get class pswp__preloader--active when preloader is running -->
                                        <div class="pswp__preloader">
                                            <div class="pswp__preloader__icn">
                                            <div class="pswp__preloader__cut">
                                                <div class="pswp__preloader__donut"></div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                        <div class="pswp__share-tooltip"></div> 
                                    </div>

                                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
                                    </button>

                                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
                                    </button>

                                    <div class="pswp__caption">
                                        <div class="pswp__caption__center"></div>
                                    </div>

                                </div>

                            </div>

                        </div>';
                        $gallery .= $style;
                        

            add_action('wp_footer', function() {
            $k = '<script type="text/javascript">';
            $k .= "jQuery(document).ready(function($) {
                $(window).load(function() {
                $('.masonry_gallery').masonry({
                    // options
                    itemSelector: '.masonry-item',
                    columnWidth: '.masonry-item',
                    percentPosition: true,
                    gutter: 10
                    });
                    initPhotoSwipeFromDOM('.masonry_gallery');
                  });
                });";
            
            $k .= '</script>';

            echo $k;
        });

            return $gallery;
        }

        public function hex2rgba($color, $opacity = false) {
 
        $default = 'rgb(0,0,0)';
    
        //Return default if no color provided
        if(empty($color))
            return $default; 
    
        //Sanitize $color if "#" is provided 
            if ($color[0] == '#' ) {
                $color = substr( $color, 1 );
            }
    
            //Check if color has 6 or 3 characters and get values
            if (strlen($color) == 6) {
                    $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                    $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                    return $default;
            }
    
            //Convert hexadec to rgb
            $rgb =  array_map('hexdec', $hex);
    
            //Check if opacity is set(rgba or rgb)
            if($opacity){
                if(abs($opacity) > 1)
                    $opacity = 1.0;
                $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
            } else {
                $output = 'rgb('.implode(",",$rgb).')';
            }
    
            //Return rgb(a) color string
            return $output;
    }

}
