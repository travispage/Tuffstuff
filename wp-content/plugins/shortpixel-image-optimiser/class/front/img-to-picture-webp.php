<?php
/**
 * Class ImgToPictureWebp - convert an <img> tag to a <picture> tag and add the webp versions of the images
 * thanks to the Responsify WP plugin for some of the code
 */

class ImgToPictureWebp {
 
    public static function convert($content) {
        // Don't do anything with the RSS feed.
        if ( is_feed() || is_admin() ) { return $content; }

        return preg_replace_callback('/<img[^>]*>/', function ($match) {
            // Do nothing with images that has the 'rwp-not-responsive' class.
            if ( strpos($match[0], 'sp-no-webp') ) { return $match[0]; }

            $img = self::get_attributes($match[0]);

            $src = (isset($img['src'])) ? $img['src'] : false;
            $srcset = (isset($img['srcset'])) ? $img['srcset'] : false;
            $sizes = (isset($img['sizes'])) ? $img['sizes'] : false;
            
            //check if there are webps
            $id = self::url_to_attachment_id( $src );
            if(!$id) { return $match[0]; }
            
            $imageBase = dirname(get_attached_file($id)) . '/';
            
            // We don't wanna have an src attribute on the <img>
            unset($img['src']);
            //unset($img['srcset']);
            //unset($img['sizes']);
            
            $srcsetWebP = '';
            if($srcset) {
                $defs = explode(",", $srcset);                
                foreach($defs as $item) {
                    $parts = preg_split('/\s+/', trim($item));
                    
                    //echo(" file: " . $parts[0] . " ext: " . pathinfo($parts[0], PATHINFO_EXTENSION) . " basename: " . wp_basename($parts[0], '.' . pathinfo($parts[0], PATHINFO_EXTENSION)));
                    
                    $fileWebP = $imageBase . wp_basename($parts[0], '.' . pathinfo($parts[0], PATHINFO_EXTENSION)) . '.webp';
                    if(file_exists($fileWebP)) {
                        $srcsetWebP .= (strlen($srcsetWebP) ? ',': '') 
                                    . preg_replace('/\.[a-zA-Z0-9]+$/', '.webp', $parts[0]) 
                                    . (isset($parts[1]) ? ' ' . $parts[1] : '');
                    }
                }
                //$srcsetWebP = preg_replace('/\.[a-zA-Z0-9]+\s+/', '.webp ', $srcset);
            } else {
                $srcset = trim($src);
                
//                die(var_dump($match));
                
                $fileWebP = $imageBase . wp_basename($srcset, '.' . pathinfo($srcset, PATHINFO_EXTENSION)) . '.webp';
                if(file_exists($fileWebP)) {
                    $srcsetWebP = preg_replace('/\.[a-zA-Z0-9]+$/', '.webp', $srcset);
                }
            }
            if(!strlen($srcsetWebP))  { return $match[0]; }
            
            return '<picture>'
                      .'<source srcset="' . $srcsetWebP . '"' . ($sizes ? ' sizes="' . $sizes . '"' : '') . ' type="image/webp">'
                      .'<source srcset="' . $srcset . '"' . ($sizes ? ' sizes="' . $sizes . '"' : '') . '>'
                      .'<img src="' . $src . '" ' . self::create_attributes($img) . '>'
                  .'</picture>';
        }, $content);
    }
    
    protected static function get_attributes( $image_node )
    {
        $image_node = mb_convert_encoding($image_node, 'HTML-ENTITIES', 'UTF-8');
        $dom = new DOMDocument();
        @$dom->loadHTML($image_node);
        $image = $dom->getElementsByTagName('img')->item(0);
        $attributes = array();
        foreach ( $image->attributes as $attr ) {
                $attributes[$attr->nodeName] = $attr->nodeValue;
        }
        return $attributes;
    }
    
    /**
     * Makes a string with all attributes.
     *
     * @param $attribute_array
     * @return string
     */
    protected static function create_attributes( $attribute_array )
    {
        $attributes = '';
        foreach ($attribute_array as $attribute => $value) {
            $attributes .= $attribute . '="' . $value . '" ';
        }
        // Removes the extra space after the last attribute
        return substr($attributes, 0, -1);
    }
    
    /**
     * @param $image_url
     * @return array
     */
    public static function url_to_attachment_id ( $image_url ) {
        // Thx to https://github.com/kylereicks/picturefill.js.wp/blob/master/inc/class-model-picturefill-wp.php
        global $wpdb;
        $original_image_url = $image_url;
        $image_url = preg_replace('/^(.+?)(-\d+x\d+)?\.(jpg|jpeg|png|gif)((?:\?|#).+)?$/i', '$1.$3', $image_url);
        $prefix = $wpdb->prefix;
        $attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $image_url ));
        if ( !empty($attachment_id) ) {
            return $attachment_id[0];
        } else {
            $attachment_id = $wpdb->get_col($wpdb->prepare("SELECT ID FROM " . $prefix . "posts" . " WHERE guid='%s';", $original_image_url ));
        }
        return !empty($attachment_id) ? $attachment_id[0] : false;
    }
    
}