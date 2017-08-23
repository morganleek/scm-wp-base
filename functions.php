<?php
/**
* Author: Morgan Leek
* URL: morganleek.me
*/

$SCM_VERSION = "1.0.1";

/*------------------------------------*\
  Theme Support
\*------------------------------------*/

if (function_exists('add_theme_support')) {

	// Add Thumbnail Theme Support
	add_theme_support('post-thumbnails');
	// add_image_size('large', 700, '', true); // Large Thumbnail
	// add_image_size('medium', 250, '', true); // Medium Thumbnail
	// add_image_size('small', 120, '', true); // Small Thumbnail
	// add_image_size('custom-size', 700, 200, true); // Custom Thumbnail Size call using the_post_thumbnail('custom-size');

	// Enables post and comment RSS feed links to head
	add_theme_support('automatic-feed-links');

}

/*------------------------------------*\
  Functions
\*------------------------------------*/

// Navigation
function scm_nav($location = 'header-menu') {
	wp_nav_menu(
		array(
			'theme_location'  => $location,
			'menu'            => '',
			'container'       => 'div',
			'container_class' => 'menu-{menu slug}-container',
			'container_id'    => '',
			'menu_class'      => 'menu',
			'menu_id'         => '',
			'echo'            => true,
			'fallback_cb'     => 'wp_page_menu',
			'before'          => '',
			'after'           => '',
			'link_before'     => '',
			'link_after'      => '',
			'items_wrap'      => '<ul>%3$s</ul>',
			'depth'           => 0,
			'walker'          => ''
		)
	);
}

// Header Scripts
function scm_header_scripts() {
	global $SCM_VERSION;
	if ($GLOBALS['pagenow'] != 'wp-login.php' && !is_admin()) {
		// jQuery
		wp_deregister_script('jquery');
		wp_register_script('jquery', get_template_directory_uri() . '/bower_components/jquery/dist/jquery.min.js', array(), '2.2.3');

		// Modernizr
		// wp_register_script('modernizr', get_template_directory_uri() . '/bower_components/modernizr/src/Modernizr.js', array(), '3.0.0');

		// Theme Scripts
		wp_register_script('scm_scripts', get_template_directory_uri() . '/js/min/theme-min.js', array('jquery'), $SCM_VERSION);

		// Enqueue Scripts
		wp_enqueue_script('scm_scripts');
	}
}

// Load Conditional Scripts
// function scm_conditional_scripts() {
// 	if (is_page('pagenamehere')) {
// 		// Conditional script(s)
// 		wp_register_script('scriptname', get_template_directory_uri() . '/js/scriptname.js', array('jquery'), '1.0.0');
// 		wp_enqueue_script('scriptname');
// 	}
// }

// Load SCM Blank styles
function scm_styles() {
	global $SCM_VERSION;

	// Custom CSS
	wp_register_style('scm', get_template_directory_uri() . '/css/screen.css', array(), $SCM_VERSION);

	// Register CSS
	wp_enqueue_style('scm');
}

// Register Navigation
function register_scm_menu() {
	register_nav_menus(
		array( 
			'header-menu' => __('Header Menu', 'scm') // Main Navigation
			// 'sidebar-menu' => __('Sidebar Menu', 'scm'), // Sidebar Navigation
			// 'extra-menu' => __('Extra Menu', 'scm') // Extra Navigation if needed (duplicate as many as you need!)
		)
	);
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args($args = '') {
	$args['container'] = false;
	return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter($var) {
	return is_array($var) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list($thelist) {
	return str_replace('rel="category tag"', 'rel="tag"', $thelist);
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes) {
	global $post;
	if (is_home()) {
		$key = array_search('blog', $classes);
		if ($key > -1) {
			unset($classes[$key]);
		}
	} elseif (is_page()) {
		$classes[] = sanitize_html_class($post->post_name);
	} elseif (is_singular()) {
		$classes[] = sanitize_html_class($post->post_name);
	}

	return $classes;
}

// Remove the width and height attributes from inserted images
function remove_width_attribute( $html ) {
	$html = preg_replace( '/(width|height)="\d*"\s/', "", $html );
	return $html;
}


// If Dynamic Sidebar Exists
// if (function_exists('register_sidebar')) {
// 	// Define Sidebar Widget Area 1
// 	register_sidebar(
// 		array(
// 			'name' => __('Widget Area 1', 'scm'),
// 			'description' => __('Description for this widget-area...', 'scm'),
// 			'id' => 'widget-area-1',
// 			'before_widget' => '<div id="%1$s" class="%2$s">',
// 			'after_widget' => '</div>',
// 			'before_title' => '<h3>',
// 			'after_title' => '</h3>'
// 		)
// 	);
// }

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action('wp_head', array(
		$wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
		'recent_comments_style'
	));
}

// Pagination
function scm_pagination() {
	global $wp_query;
	$big = 999999999;
	echo paginate_links(
		array(
			'base' => str_replace($big, '%#%', get_pagenum_link($big)),
			'format' => '?paged=%#%',
			'current' => max(1, get_query_var('paged')),
			'total' => $wp_query->max_num_pages
		)
	);
}

// Custom Excerpts
function scm_index($length) {
	return 20;
}

// Create the Custom Excerpts callback
function scm_excerpt($length_callback = '', $more_callback = '') {
	global $post;
	if (function_exists($length_callback)) {
		add_filter('excerpt_length', $length_callback);
	}
	if (function_exists($more_callback)) {
		add_filter('excerpt_more', $more_callback);
	}

	$output = get_the_excerpt();
	$output = apply_filters('wptexturize', $output);
	$output = apply_filters('convert_chars', $output);
	$output = '<p>' . $output . '</p>';
	echo $output;
}

// Custom View Article link to Post
function scm_view_article($more) {
	global $post;
	return '&hellip; <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'scm') . '</a>';
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions($html) {
	$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
	return $html;
}

// Threaded Comments
function enable_threaded_comments() {
	if (!is_admin()) {
		if (is_singular() AND comments_open() AND (get_option('thread_comments') == 1)) {
			wp_enqueue_script('comment-reply');
		}
	}
}

/*------------------------------------*\
  Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action('init', 'scm_header_scripts'); // Add Custom Scripts to wp_head
// add_action('wp_print_scripts', 'scm_conditional_scripts'); // Add Conditional Page Scripts
add_action('get_header', 'enable_threaded_comments'); // Enable Threaded Comments
add_action('wp_enqueue_scripts', 'scm_styles'); // Add Theme Stylesheet
add_action('init', 'register_scm_menu'); // Add SCM Blank Menu
add_action('widgets_init', 'my_remove_recent_comments_style'); // Remove inline Recent Comment Styles from wp_head()
add_action('init', 'scm_pagination'); // Add our SCM Pagination

// Remove Actions
remove_action('wp_head', 'feed_links_extra', 3); // Display the links to the extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // Display the links to the general feeds: Post and Comment Feed
remove_action('wp_head', 'rsd_link'); // Display the link to the Really Simple Discovery service endpoint, EditURI link
remove_action('wp_head', 'wlwmanifest_link'); // Display the link to the Windows Live Writer manifest file.
remove_action('wp_head', 'wp_generator'); // Display the XHTML generator that is generated on the wp_head hook, WP version
remove_action('wp_head', 'rel_canonical');
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

// Add Filters
add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)
add_filter('widget_text', 'do_shortcode'); // Allow shortcodes in Dynamic Sidebar
add_filter('widget_text', 'shortcode_unautop'); // Remove <p> tags in Dynamic Sidebars (better!)
add_filter('wp_nav_menu_args', 'my_wp_nav_menu_args'); // Remove surrounding <div> from WP Navigation
add_filter('the_category', 'remove_category_rel_from_category_list'); // Remove invalid rel attribute
add_filter('the_excerpt', 'shortcode_unautop'); // Remove auto <p> tags in Excerpt (Manual Excerpts only)
add_filter('the_excerpt', 'do_shortcode'); // Allows Shortcodes to be executed in Excerpt (Manual Excerpts only)
add_filter('excerpt_more', 'scm_view_article'); // Add 'View Article' button instead of [...] for Excerpts
add_filter('post_thumbnail_html', 'remove_thumbnail_dimensions', 10); // Remove width and height dynamic attributes to thumbnails
add_filter('post_thumbnail_html', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images
add_filter('image_send_to_editor', 'remove_width_attribute', 10 ); // Remove width and height dynamic attributes to post images

// Remove Filters
remove_filter('the_excerpt', 'wpautop'); // Remove <p> tags from Excerpt altogether

// Shortcodes

/*------------------------------------*\
  ShortCode Functions
\*------------------------------------*/

/*------------------------------------*\
  Custom Functions
\*------------------------------------*/

add_filter('site_transient_update_themes', 'remove_update_themes', 100, 1); // No site theme update notifications 

function remove_update_themes($value) {
	return null;
}

// Returns maximum size for image whilst maintaining an aspect ratio
function scm_opt_ratio($target, $dimensions) {
	$width = $dimensions[0];
	$height = $dimensions[1];

	$optWidth = $target[0];
	$optHeight = $target[1];

	$ratio = $optWidth / $optHeight;

	$imageWidth = $optWidth;
	$imageHeight = $optHeight;

	if($width < $optWidth || $height < $optHeight) {
		if($width / $ratio <= $height) {
			$imageWidth = $width;
			$imageHeight = $height / $ratio;
		}
		else {
			$imageWidth = $width / $ratio;
			$imageHeight = $height;
		}
	}
	
	return array(floor($imageWidth), floor($imageHeight));
}

// Dump Object Short Function
function _d($obj, $return = false) {
	if($return) {
		return '<pre>' . print_r($obj, true) . '</pre>';
	}
	print '<pre>' . print_r($obj, true) . '</pre>';	
}
