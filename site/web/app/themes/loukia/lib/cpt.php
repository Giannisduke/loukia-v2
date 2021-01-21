<?php
// Register Custom Post Type Portfolio
function create_portfolio_cpt() {

	$labels = array(
		'name' => _x( 'Portfolios', 'Post Type General Name', 'loukia' ),
		'singular_name' => _x( 'Portfolio', 'Post Type Singular Name', 'loukia' ),
		'menu_name' => _x( 'Portfolios', 'Admin Menu text', 'loukia' ),
		'name_admin_bar' => _x( 'Portfolio', 'Add New on Toolbar', 'loukia' ),
		'archives' => __( 'Portfolio Archives', 'loukia' ),
		'attributes' => __( 'Portfolio Attributes', 'loukia' ),
		'parent_item_colon' => __( 'Parent Portfolio:', 'loukia' ),
		'all_items' => __( 'All Portfolios', 'loukia' ),
		'add_new_item' => __( 'Add New Portfolio', 'loukia' ),
		'add_new' => __( 'Add New', 'loukia' ),
		'new_item' => __( 'New Portfolio', 'loukia' ),
		'edit_item' => __( 'Edit Portfolio', 'loukia' ),
		'update_item' => __( 'Update Portfolio', 'loukia' ),
		'view_item' => __( 'View Portfolio', 'loukia' ),
		'view_items' => __( 'View Portfolios', 'loukia' ),
		'search_items' => __( 'Search Portfolio', 'loukia' ),
		'not_found' => __( 'Not found', 'loukia' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'loukia' ),
		'featured_image' => __( 'Featured Image', 'loukia' ),
		'set_featured_image' => __( 'Set featured image', 'loukia' ),
		'remove_featured_image' => __( 'Remove featured image', 'loukia' ),
		'use_featured_image' => __( 'Use as featured image', 'loukia' ),
		'insert_into_item' => __( 'Insert into Portfolio', 'loukia' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Portfolio', 'loukia' ),
		'items_list' => __( 'Portfolios list', 'loukia' ),
		'items_list_navigation' => __( 'Portfolios list navigation', 'loukia' ),
		'filter_items_list' => __( 'Filter Portfolios list', 'loukia' ),
	);
	$args = array(
		'label' => __( 'Portfolio', 'loukia' ),
		'description' => __( '', 'loukia' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-portfolio',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
		'taxonomies'  => array( 'category', 'post_tag' ),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'portfolio', $args );

}
add_action( 'init', 'create_portfolio_cpt', 0 );

// Register Custom Post Type Collection
function create_collection_cpt() {

	$labels = array(
		'name' => _x( 'Collections', 'Post Type General Name', 'loukia' ),
		'singular_name' => _x( 'Collection', 'Post Type Singular Name', 'loukia' ),
		'menu_name' => _x( 'Collections', 'Admin Menu text', 'loukia' ),
		'name_admin_bar' => _x( 'Collection', 'Add New on Toolbar', 'loukia' ),
		'archives' => __( 'Collection Archives', 'loukia' ),
		'attributes' => __( 'Collection Attributes', 'loukia' ),
		'parent_item_colon' => __( 'Parent Collection:', 'loukia' ),
		'all_items' => __( 'All Collections', 'loukia' ),
		'add_new_item' => __( 'Add New Collection', 'loukia' ),
		'add_new' => __( 'Add New', 'loukia' ),
		'new_item' => __( 'New Collection', 'loukia' ),
		'edit_item' => __( 'Edit Collection', 'loukia' ),
		'update_item' => __( 'Update Collection', 'loukia' ),
		'view_item' => __( 'View Collection', 'loukia' ),
		'view_items' => __( 'View Collections', 'loukia' ),
		'search_items' => __( 'Search Collection', 'loukia' ),
		'not_found' => __( 'Not found', 'loukia' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'loukia' ),
		'featured_image' => __( 'Featured Image', 'loukia' ),
		'set_featured_image' => __( 'Set featured image', 'loukia' ),
		'remove_featured_image' => __( 'Remove featured image', 'loukia' ),
		'use_featured_image' => __( 'Use as featured image', 'loukia' ),
		'insert_into_item' => __( 'Insert into Collection', 'loukia' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Collection', 'loukia' ),
		'items_list' => __( 'Collections list', 'loukia' ),
		'items_list_navigation' => __( 'Collections list navigation', 'loukia' ),
		'filter_items_list' => __( 'Filter Collections list', 'loukia' ),
	);
	$args = array(
		'label' => __( 'Collection', 'loukia' ),
		'description' => __( '', 'loukia' ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-tablet',
		'supports' => array('title', 'editor', 'excerpt', 'thumbnail', 'custom-fields'),
		'taxonomies' => array(),
		'public' => true,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 5,
		'show_in_admin_bar' => true,
		'show_in_nav_menus' => true,
		'can_export' => true,
		'has_archive' => true,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => false,
		'publicly_queryable' => true,
		'capability_type' => 'post',
	);
	register_post_type( 'collection', $args );

}
add_action( 'init', 'create_collection_cpt', 0 );
