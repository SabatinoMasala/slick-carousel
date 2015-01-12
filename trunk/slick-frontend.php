<?php
function slickc_shortcode($attributes, $content = null) {
    // Set default shortcode attributes
	$options = get_option('slickc_settings');
	if (!$options) {
		slickc_set_options();
		$options = get_option('slickc_settings');
	}
	$options['id'] = '';

	// Parse incomming $attributes into an array and merge it with $defaults
	$attributes = shortcode_atts($options, $attributes);

	return slickc_frontend($attributes);
}

function slickc_write_options($attributes) {
    $options = array();
    foreach ($attributes as $key => $value) {
        switch ($key) {
            /* int */
            case 'autoplaySpeed':
            case 'initialSlide':
            case 'slidesToShow':
            case 'slidesToScroll':
            case 'speed':
            case 'touchThreshold':
            case 'asNavFor':
            case 'centerPadding':
            case 'cssEase':
            case 'easing':
            case 'lazyLoad':
            case 'respondTo':
            case 'appendArrows':
            case 'prevArrow':
            case 'nextArrow':
            case 'id':
            case 'orderby':
            case 'order':
                if (!empty($value)) {
                    $options[$key] = $value;
                }
                break;
            /* boolean */
            default:
                $options[$key] = ('true' === $value) ? true : false;
                break;
        }
    }
    return json_encode($options);
}

function slickc_load_slick_dependencies() {
    wp_enqueue_style(
        'slick-carousel-styles',
        plugins_url('deps/slick/slick/slick.css', __FILE__)
    );
    wp_enqueue_script(
       'slick-carousel-script',
        plugins_url('deps/slick/slick/slick.min.js', __FILE__)
    );
}

function slickc_load_images($attributes) {
    $args = array(
		'post_type' => 'slickc',
		'posts_per_page' => '-1',
		'orderby' => $attributes['orderby'],
		'order' => $attributes['order']
	);
	if (!empty($attributes['category'])) {
		$args['carousel_category'] = $attributes['category'];
	}
	
	if (!empty($attributes['id'])) {
		$args['p'] = $attributes['id'];
	}

	$loop = new WP_Query($args);
	$images = array();
	$output = '';
	while ($loop->have_posts()) {
		$loop->the_post();
        $image = get_the_post_thumbnail(get_the_ID(), 'full');
		if (!empty($image)) {
			$post_id = get_the_ID();
			$title = get_the_title();
			$content = get_the_excerpt();;
			$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
			$image_src = $image_src[0];
			$url = get_post_meta(get_the_ID(), 'slickc_image_url');
            $url_openblank = get_post_meta(get_the_ID(), 'slickc_image_url_openblank');
			$images[] = array(
                'post_id' => $post_id,
                'title' => $title,
                'content' => $content,
                'image' => $image,
                'img_src' => $image_src,
                'url' => esc_url($url[0]),
                'open_blank' => $url_openblank[0],
            );
		}
	}
    return $images;
}

// Display carousel
function slickc_frontend($attributes) {
	$images = slickc_load_images($attributes);
	if (0 === count($images)) {
        return '';
    }
    $id = rand(0, 99999);
    slickc_load_slick_dependencies();
    ob_start();
    ?>
    <div id="slickc_<?php echo $id ?>">
        <?php foreach ($images as $key => $image) : ?>
            <div>
            <?php
            $linkstart = '';
            $linkend = '';
            if ($image['url']) {
                $linkstart = '<a href="' . $image['url'] . '" ';
                if ($image['open_blank']) {
                    $linkstart .= 'target="_blank" ';
                }
                $linkstart .= '>';
                $linkend = '</a>';
            }
            ?>
            <?php echo $linkstart ?>
            <img src="<?php echo $image['img_src'] ?>" title="<?php echo esc_html($image['title']) ?>" />
            <?php echo $linkend ?>
            </div>
        <?php endforeach ?>
    </div>
    <script type="text/javascript">
        var slickc_<?php echo $id ?>_options = JSON.parse(
            '<?php echo slickc_write_options($attributes) ?>'
        );
        jQuery(document).ready(function() {
            jQuery('#slickc_<?php echo $id ?>').slick(slickc_<?php echo $id ?>_options)
        })
    </script>
<?php
    $output = ob_get_contents();
    ob_end_clean();
	wp_reset_postdata();  
	return $output;
}

add_shortcode('slick-carousel', 'slickc_shortcode');