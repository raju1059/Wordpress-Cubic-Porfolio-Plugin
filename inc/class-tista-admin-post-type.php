<?php
/**
 * Tista_Admin_Post_Type class.
 *
 * @package Tista Cubeportfolio
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Tista_Admin_Post_Type Class.
 */
class Tista_Admin_Post_Type {

	/**
	 * The class constructor.
	 *
	 * @access public
	 */
	public function __construct() {
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );
		// WP List table columns. Defined here so they are always available for events such as inline editing.
			
		add_filter( 'manage_portfolio_posts_columns', array( $this, 'portfolio_columns' ) );
		add_action( 'manage_portfolio_posts_custom_column', array( $this, 'render_portfolio_columns' ), 2 );
		
		add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( $this, 'row_actions' ), 100, 2 );
		
		
		add_filter( 'manage_edit-portfolio_sortable_columns', array( $this, 'portfolio_sortable_columns' ) );
		
		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );		
		//add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );		
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );
		
		// Views
		add_filter( 'views_edit-portfolio', array( $this, 'portfolio_views' ) );
		add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );
		
		// Uploads
		//add_filter( 'upload_dir', array( $this, 'upload_dir' ) );
		
		// Disable post type view mode options
		add_filter( 'view_mode_post_types', array( $this, 'disable_view_mode_options' ) );
		
	}	
	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['portfolio'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Portfolio updated. <a href="%s">View Portfolio</a>', 'tista' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'tista' ),
			3 => __( 'Custom field deleted.', 'tista' ),
			4 => __( 'Portfolio updated.', 'tista' ),
			/* translators: %s: revision title */
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Portfolio restored to revision from %s', 'tista' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			/* translators: %s: Portfolio url */
			6 => sprintf( __( 'Portfolio published. <a href="%s">View Portfolio</a>', 'tista' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Portfolio saved.', 'tista' ),
			/* translators: %s: Portfolio url */
			8 => sprintf( __( 'Portfolio submitted. <a target="_blank" href="%s">Preview Portfolio</a>', 'tista' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf(
				/* translators: 1: date 2: Portfolio url */
				__( 'Portfolio scheduled for: %1$s. <a target="_blank" href="%2$s">Portfolio Portfolio</a>', 'tista' ),
			  	'<strong>' . date_i18n( __( 'M j, Y @ G:i', 'tista' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) . '</strong>'
			),
			/* translators: %s: Portfolio url */
			10 => sprintf( __( 'Portfolio draft updated. <a target="_blank" href="%s">Preview Portfolio</a>', 'tista' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
		return $messages;
	}	
	/**
	 * Specify custom bulk actions messages for different post types.
	 * @param  array $bulk_messages
	 * @param  array $bulk_counts
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['portfolio'] = array(
			/* translators: %s: portfolio count */
			'updated'   => _n( '%s portfolio updated.', '%s portfolios updated.', $bulk_counts['updated'], 'tista' ),
			/* translators: %s: portfolio count */
			'locked'    => _n( '%s portfolio not updated, somebody is editing it.', '%s portfolios not updated, somebody is editing them.', $bulk_counts['locked'], 'tista' ),
			/* translators: %s: portfolio count */
			'deleted'   => _n( '%s portfolio permanently deleted.', '%s portfolios permanently deleted.', $bulk_counts['deleted'], 'tista' ),
			/* translators: %s: portfolio count */
			'trashed'   => _n( '%s portfolio moved to the Trash.', '%s portfolios moved to the Trash.', $bulk_counts['trashed'], 'tista' ),
			/* translators: %s: portfolio count */
			'untrashed' => _n( '%s portfolio restored from the Trash.', '%s portfolios restored from the Trash.', $bulk_counts['untrashed'], 'tista' ),
		);
		return $bulk_messages;
	}
		
	/**
	 * Define custom columns for portfolios.
	 * @param  array $existing_columns
	 * @return array
	 */
	public function portfolio_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns          = array();
		$columns['cb']    = '<input type="checkbox" />';
		//$columns['thumb'] = '<span class="wc-image tips" data-tip="' . esc_attr__( 'Image', 'tista' ) . '">' . __( 'Image', 'tista' ) . '</span>';
		$columns['name']  = __( 'Name', 'tista' );
		$columns['portfolio_cat']  = __( 'Categories', 'tista' );
		$columns['portfolio_tag']  = __( 'Tags', 'tista' );
		$columns['author']  = __( 'Author', 'tista' );
		$columns['date']         = __( 'Date', 'tista' );

		return array_merge( $columns, $existing_columns );

	}
	
	/**
	 * Output custom columns for portfolios.
	 *
	 * @param string $column
	 */
	public function render_portfolio_columns( $column ) {
		global $post;

		if ( empty( $the_post ) || $the_post->get_id() != $post->ID ) {
			$the_post = get_posts( $post );
		}

		// Only continue if we have a portfolio.
		if ( empty( $the_post ) ) {
			return;
		}
		$image = get_the_post_thumbnail( $post->ID, 'thumbnail' );
		switch ( $column ) {
			case 'thumb' :
				echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . $image . '</a>';
				break;
			case 'name' :
				$edit_link = get_edit_post_link( $post->ID );
				$title     = _draft_or_post_title();

				echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

				_post_states( $post );

				echo '</strong>';

				if ( $post->post_parent > 0 ) {
					echo '&nbsp;&nbsp;&larr; <a href="' . get_edit_post_link( $post->post_parent ) . '">' . get_the_title( $post->post_parent ) . '</a>';
				}

				// Excerpt view
				if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
					echo apply_filters( 'the_excerpt', $post->post_excerpt );
				}

				get_inline_data( $post );
				break;
			case 'portfolio_cat' :
			case 'portfolio_tag' :
				if ( ! $terms = get_the_terms( $post->ID, $column ) ) {
					echo '<span class="na">&ndash;</span>';
				} else {
					 $termlist = array();
				if ( $terms && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term ) {
							$termlist[] = '<a href="' . admin_url( 'edit.php?' . $column . '=' . $term->slug . '&post_type=portfolio' ) . ' ">' . $term->name . '</a>';						
					}
				}

					echo implode( ', ', $termlist ); 
				}
				break;
			default :
				break;
		}
	}
	
	/**
	 * Set list table primary column for portfolios.
	 * Support for WordPress 4.3.
	 *
	 * @param  string $default
	 * @param  string $screen_id
	 *
	 * @return string
	 */
	public function list_table_primary_column( $default, $screen_id ) {

		if ( 'edit-portfolio' === $screen_id ) {
			return 'name';
		}		
		return $default;
	}
	
	/**
	 * Set row actions for portfolios.
	 *
	 * @param  array $actions
	 * @param  WP_Post $post
	 *
	 * @return array
	 */
	public function row_actions( $actions, $post ) {
		if ( 'portfolio' === $post->post_type ) {
			return array_merge( array( 'id' => 'ID: ' . $post->ID ), $actions );
		}
		return $actions;
	}
	
	/**
	 * Make columns sortable - https://gist.github.com/906872.
	 *
	 * @param  array $columns
	 * @return array
	 */
	public function portfolio_sortable_columns( $columns ) {
		$custom = array(
			'name'     => 'title',
		);
		return wp_parse_args( $custom, $columns );
	}
	
	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'portfolio' :
				$text = __( 'Portfolio Name', 'tista' );
			break;
			case 'slider' :
				$text = __( 'Slide Name', 'tista' );
			break;
		}

		return $text;
	}
	
	/**
	 * Print coupon description textarea field.
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {
		if ( 'portfolio' === $post->post_type ) {
			?>
			<textarea id="woocommerce-coupon-description" name="excerpt" cols="5" rows="2" placeholder="<?php esc_attr_e( 'Description (optional)', 'woocommerce' ); ?>"><?php echo $post->post_excerpt; // This is already escaped in core ?></textarea>
			<?php
		}
	}
	
	/**
	 * Hidden default Meta-Boxes.
	 * @param  array  $hidden
	 * @param  object $screen
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( 'portfolio' === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}
		return $hidden;
	}
	
	/**
	 * Change views on the edit screen.
	 *
	 * @param  array $views
	 * @return array
	 */
	public function portfolio_views( $views ) {
		global $wp_query;

		// Portfoli do not have authors.
		unset( $views['mine'] );

		// Add sorting link.
		if ( current_user_can( 'edit_others_pages' ) ) {
			$class            = ( isset( $wp_query->query['orderby'] ) && 'menu_order title' === $wp_query->query['orderby'] ) ? 'current' : '';
			$query_string     = remove_query_arg( array( 'orderby', 'order' ) );
			$query_string     = add_query_arg( 'orderby', urlencode( 'menu_order title' ), $query_string );
			$query_string     = add_query_arg( 'order', urlencode( 'ASC' ), $query_string );
			$views['byorder'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . __( 'Sorting', 'tista' ) . '</a>';
		}

		return $views;
	}
	/**
	 * Disable months dropdown on portfolio screen.
	 */
	public function disable_months_dropdown( $bool, $post_type ) {
		return 'portfolio' === $post_type ? true : $bool;
	}
	/**
	 * Filter the directory for uploads.
	 *
	 * @param array $pathdata
	 * @return array
	 */
	public function upload_dir( $pathdata ) {

		// Change upload dir for downloadable files
		if ( isset( $_POST['type'] ) && 'downloadable_portfolio' == $_POST['type'] ) {

			if ( empty( $pathdata['subdir'] ) ) {
				$pathdata['path']   = $pathdata['path'] . '/portfolio_uploads';
				$pathdata['url']    = $pathdata['url'] . '/portfolio_uploads';
				$pathdata['subdir'] = '/portfolio_uploads';
			} else {
				$new_subdir = '/portfolio_uploads' . $pathdata['subdir'];

				$pathdata['path']   = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['path'] );
				$pathdata['url']    = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['url'] );
				$pathdata['subdir'] = str_replace( $pathdata['subdir'], $new_subdir, $pathdata['subdir'] );
			}
		}

		return $pathdata;
	}
	/**
	 * Removes portfolio from the list of post types that support "View Mode" switching.
	 * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
	 * it, so we want to hide the useless UI from the screen options tab.
	 *
	 * @since 2.6
	 * @param  array $post_types Array of post types supporting view mode
	 * @return array             Array of post types supporting view mode, without portfolio
	 */
	public function disable_view_mode_options( $post_types ) {
		unset( $post_types['portfolio'] );
		return $post_types;
	}
	
}	
new Tista_Admin_Post_Type;