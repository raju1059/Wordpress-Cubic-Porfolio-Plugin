<?php
/**
 * Tista_Cubeportfolio_Post_Type class.
 *
 * @package Tista Cubeportfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tista_Cubeportfolio_Post_Type Class.
 */
class Tista_Cubeportfolio_Post_Type {

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		
		add_action( 'init', array( $this, 'tista_register_post_types' ), 9 );
		add_action( 'init', array( $this, 'tista_register_taxonomies' ), 9 );
		add_action( 'init', array( $this, 'support_jetpack_omnisearch' ) );
		add_filter( 'rest_api_allowed_post_types', array( $this, 'rest_api_allowed_post_types' ) );
		
	}

	/**
	 * Register core taxonomies.
	 */
	public function tista_register_taxonomies() {

		if ( ! is_blog_installed() ) {
			return;
		}
		$lavel_cat = array(
						'name'              => __( 'Portfolio categories', 'tista' ),
						'singular_name'     => __( 'Category', 'tista' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'tista' ),
						'search_items'      => __( 'Search categories', 'tista' ),
						'all_items'         => __( 'All categories', 'tista' ),
						'parent_item'       => __( 'Parent category', 'tista' ),
						'parent_item_colon' => __( 'Parent category:', 'tista' ),
						'edit_item'         => __( 'Edit category', 'tista' ),
						'update_item'       => __( 'Update category', 'tista' ),
						'add_new_item'      => __( 'Add new category', 'tista' ),
						'new_item_name'     => __( 'New category name', 'tista' ),
						'not_found'         => __( 'No categories found', 'tista' ),
				);
		$lavel_tags = array(
						'name'                       => __( 'Portfolio tags', 'tista' ),
						'singular_name'              => __( 'Tag', 'tista' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'tista' ),
						'search_items'               => __( 'Search tags', 'tista' ),
						'all_items'                  => __( 'All tags', 'tista' ),
						'edit_item'                  => __( 'Edit tag', 'tista' ),
						'update_item'                => __( 'Update tag', 'tista' ),
						'add_new_item'               => __( 'Add new tag', 'tista' ),
						'new_item_name'              => __( 'New tag name', 'tista' ),
						'popular_items'              => __( 'Popular tags', 'tista' ),
						'separate_items_with_commas' => __( 'Separate tags with commas', 'tista' ),
						'add_or_remove_items'        => __( 'Add or remove tags', 'tista' ),
						'choose_from_most_used'      => __( 'Choose from the most used tags', 'tista' ),
						'not_found'                  => __( 'No tags found', 'tista' ),
					);
					
		$slug_cat = get_theme_mod( 'tista-nodi-cat' );
		$slug_cat = ( empty( $slug_cat ) ) ? 'tista-nodi-cat' : $slug_cat;
		
		$slug_tags = get_theme_mod( 'tista-nodi-tags' );
		$slug_tags = ( empty( $slug_tags ) ) ? 'tista-nodi-tags' : $slug_tags;

		  $args_cat = array(
			'labels'              => $lavel_cat,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => $slug_cat,'with_front' => false, ),
			'capability_type'     => 'portfolio',
			'has_archive'         => true,
			'hierarchical'        => true,
			'menu_position'       => null,    
		  );		  

		$args_tags = array(
			'labels'              => $lavel_tags,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => $slug_tags,'with_front' => false, ),
			'capability_type'     => 'portfolio',
			'has_archive'         => true,
			'hierarchical'        => true,
			'menu_position'       => null,    
		  );
				
		register_taxonomy( 'portfolio_cat','portfolio', $args_cat	);
		register_taxonomy( 'portfolio_tag', 'portfolio', $args_tags );

	}

	/**
	 * Register core post types.
	 */
	public function tista_register_post_types() {
		if ( ! is_blog_installed() || post_type_exists( 'portfolio' ) ) {
			return;
		}
		
		$supports = array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields','comments','publicize', 'wpcom-markdown' );
		$slug = get_theme_mod( 'tista-portfolio' );
		$slug = ( empty( $slug ) ) ? 'tista-portfolio' : $slug;
		$levels =  array(
							'name'                  => __( 'Portfolios', 'tista' ),
							'singular_name'         => __( 'Portfolio', 'tista' ),
							'all_items'             => __( 'All Portfolios', 'tista' ),
							'menu_name'             => _x( 'Portfolios', 'Admin menu name', 'tista' ),
							'add_new'               => __( 'Add New', 'tista' ),
							'add_new_item'          => __( 'Add new portfolio', 'tista' ),
							'edit'                  => __( 'Edit', 'tista' ),
							'edit_item'             => __( 'Edit portfolio', 'tista' ),
							'new_item'              => __( 'New portfolio', 'tista' ),
							'view'                  => __( 'View portfolio', 'tista' ),
							'view_item'             => __( 'View portfolio', 'tista' ),
							'search_items'          => __( 'Search Portfolios', 'tista' ),
							'not_found'             => __( 'No Portfolios found', 'tista' ),
							'not_found_in_trash'    => __( 'No Portfolios found in trash', 'tista' ),
							'parent'                => __( 'Parent portfolio', 'tista' ),
							'featured_image'        => __( 'portfolio image', 'tista' ),
							'set_featured_image'    => __( 'Set portfolio image', 'tista' ),
							'remove_featured_image' => __( 'Remove portfolio image', 'tista' ),
							'use_featured_image'    => __( 'Use as portfolio image', 'tista' ),
							'insert_into_item'      => __( 'Insert into portfolio', 'tista' ),
							'uploaded_to_this_item' => __( 'Uploaded to this portfolio', 'tista' ),
							'filter_items_list'     => __( 'Filter Portfolios', 'tista' ),
							'items_list_navigation' => __( 'Portfolios navigation', 'tista' ),
							'items_list'            => __( 'Portfolios list', 'tista' ),
						);
		$args = array(
					'labels'              => $levels,
					'description'         => __( 'This is where you can add new Portfolios gallery.', 'tista' ),
					'public'              => true,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true,
					'exclude_from_search' => false,
					'hierarchical'        => false, // Hierarchical causes memory issues - WP loads all records!
					'rewrite'             => array( 'slug' => $slug ),
					'query_var'           => true,
					'supports'            => $supports,
					'has_archive'         => true,
					'show_in_nav_menus'   => true,
					'show_in_rest'        => true,
					'show_in_menu'        => true,
				);
		register_post_type( 'portfolio', $args );
	}
	/**
	 * Add Portfolio Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'portfolio' );
		}
	}

	/**
	 * Added Portfolio for Jetpack related posts.
	 *
	 * @param  array $post_types
	 * @return array
	 */
	public static function rest_api_allowed_post_types( $post_types ) {
		$post_types[] = 'portfolio';

		return $post_types;
	}
	
}	
new Tista_Cubeportfolio_Post_Type;