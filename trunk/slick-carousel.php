<?php
/*
Plugin Name: Slick Carousel
Plugin URI: https://github.com/surevine/slick-carousel
Description: A carousel plugin using the Slick javascript library
Version: 1.0
Author: Lloyd Watkin <lloyd.watkin@surevine.com>
Author URI: http://www.surevine.com
License: MIT
*/

function slickc_post_type() {
	$labels = array(
		'name' => __('Carousel Images', 'slick-carousel'),
		'singular_name' => __('Carousel Image', 'slick-carousel'),
		'add_new' => __('Add New', 'slick-carousel'),
		'add_new_item' => __('Add New Carousel Image', 'slick-carousel'),
		'edit_item' => __('Edit Carousel Image', 'slick-carousel'),
		'new_item' => __('New Carousel Image', 'slick-carousel'),
		'view_item' => __('View Carousel Image', 'slick-carousel'),
		'search_items' => __('Search Carousel Images', 'slick-carousel'),
		'not_found' => __('No Carousel Image', 'slick-carousel'),
		'not_found_in_trash' => __('No Carousel Images found in Trash', 'slick-carousel'),
		'parent_item_colon' => '',
		'menu_name' => __('Slick Carousel', 'slick-carousel'),
	);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'page',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => 21,
		'supports' => array('title', 'excerpt', 'thumbnail', 'page-attributes'),
	); 
	register_post_type('slickc', $args);
}

function slickc_taxonomies() {
	$args = array('hierarchical' => true);
	register_taxonomy('carousel_category', 'slickc', $args);
}

function slickc_addFeaturedImageSupport() {
	$supportedTypes = get_theme_support('post-thumbnails');
	if ($supportedTypes === false) {
		add_theme_support('post-thumbnails', array('slickc'));	  
		add_image_size('featured_preview', 100, 55, true);
	} else if (is_array($supportedTypes)) {
		$supportedTypes[0][] = 'slickc';
		add_theme_support('post-thumbnails', $supportedTypes[0]);
		add_image_size('featured_preview', 100, 55, true);
	}
}

add_action('after_setup_theme', 'slickc_addFeaturedImageSupport');
add_action('init', 'slickc_post_type');
add_action('init', 'slickc_taxonomies', 0);

// Load in the pages doing everything else!
require_once('slick-admin.php');
require_once('slick-settings.php');
require_once('slick-frontend.php');