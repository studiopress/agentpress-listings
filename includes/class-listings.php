<?php
/**
 * This file contains the AgentPress_Listings class.
 */

/**
 * This class handles the creation of the "Listings" post type, and creates a
 * UI to display the Listing-specific data on the admin screens.
 *
 */
class AgentPress_Listings {

	public $settings_field = 'agentpress_taxonomies';
	public $menu_page = 'register-taxonomies';
	
	/**
	 * Property details array.
	 */
	public $property_details;

	/**
	 * Construct Method.
	 */
	function __construct() {
		
		$this->property_details = apply_filters( 'agentpress_property_details', array(
			'col1' => array( 
			    __( 'Price:', 'agentpress-listings' )   => '_listing_price', 
			    __( 'Address:', 'agentpress-listings' ) => '_listing_address', 
			    __( 'City:', 'agentpress-listings' )    => '_listing_city', 
			    __( 'State:', 'agentpress-listings' )   => '_listing_state', 
			    __( 'ZIP:', 'agentpress-listings' )     => '_listing_zip' 
			), 
			'col2' => array( 
			    __( 'MLS #:', 'agentpress-listings' )       => '_listing_mls', 
			    __( 'Square Feet:', 'agentpress-listings' ) => '_listing_sqft', 
			    __( 'Bedrooms:', 'agentpress-listings' )    => '_listing_bedrooms', 
			    __( 'Bathrooms:', 'agentpress-listings' )   => '_listing_bathrooms', 
			    __( 'Basement:', 'agentpress-listings' )    => '_listing_basement' 
			)
		) );

		add_action( 'init', array( $this, 'create_post_type' ) );

		add_filter( 'manage_edit-listing_columns', array( $this, 'columns_filter' ) );
		add_action( 'manage_posts_custom_column', array( $this, 'columns_data' ) );

		add_action( 'admin_menu', array( $this, 'register_meta_boxes' ), 5 );
		add_action( 'save_post', array( $this, 'metabox_save' ), 1, 2 );

		add_shortcode( 'property_details', array( $this, 'property_details_shortcode' ) );
		add_shortcode( 'property_map', array( $this, 'property_map_shortcode' ) );
		add_shortcode( 'property_video', array( $this, 'property_video_shortcode' ) );

		#add_action( 'admin_head', array( $this, 'admin_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js' ) );

		add_filter( 'search_template', array( $this, 'search_template' ) );

		add_filter( 'genesis_build_crumbs', array( $this, 'breadcrumbs' ), 10, 2 );

	}

	/**
	 * Creates our "Listing" post type.
	 */
	function create_post_type() {

		$args = apply_filters( 'agentpress_listings_post_type_args',
			array(
				'labels' => array(
					'name'               => __( 'Listings', 'agentpress-listings' ),
					'singular_name'      => __( 'Listing', 'agentpress-listings' ),
					'add_new'            => __( 'Add New', 'agentpress-listings' ),
					'add_new_item'       => __( 'Add New Listing', 'agentpress-listings' ),
					'edit'               => __( 'Edit', 'agentpress-listings' ),
					'edit_item'          => __( 'Edit Listing', 'agentpress-listings' ),
					'new_item'           => __( 'New Listing', 'agentpress-listings' ),
					'view'               => __( 'View Listing', 'agentpress-listings' ),
					'view_item'          => __( 'View Listing', 'agentpress-listings' ),
					'search_items'       => __( 'Search Listings', 'agentpress-listings' ),
					'not_found'          => __( 'No listings found', 'agentpress-listings' ),
					'not_found_in_trash' => __( 'No listings found in Trash', 'agentpress-listings' )
				),
				'public'        => true,
				'query_var'     => true,
				'menu_position' => 6,
				'menu_icon'     => 'dashicons-admin-home',
				'has_archive'   => true,
				'supports'      => array( 'title', 'editor', 'comments', 'thumbnail', 'genesis-seo', 'genesis-layouts', 'genesis-simple-sidebars' ),
				'rewrite'       => array( 'slug' => 'listings' ),
			)
		);

		register_post_type( 'listing', $args );

	}

	function register_meta_boxes() {

		add_meta_box( 'listing_details_metabox', __( 'Property Details', 'agentpress-listings' ), array( &$this, 'listing_details_metabox' ), 'listing', 'normal', 'high' );

	}

	function listing_details_metabox() {
		include( dirname( __FILE__ ) . '/views/listing-details-metabox.php' );
	}

	function metabox_save( $post_id, $post ) {

		if ( ! isset( $_POST['agentpress_details_metabox_nonce'] ) || ! isset( $_POST['ap'] ) )
			return;

		/** Verify the nonce */
	    if ( ! wp_verify_nonce( $_POST['agentpress_details_metabox_nonce'], 'agentpress_details_metabox_save' ) )
	        return;

		/** Run only on listings post type save */
		if ( 'listing' != $post->post_type )
			return;

	    /** Don't try to save the data under autosave, ajax, or future post */
	    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) return;
	    if ( defined( 'DOING_CRON' ) && DOING_CRON ) return;

	    /** Check permissions */
	    if ( ! current_user_can( 'edit_post', $post_id ) )
	        return;

	    $property_details = $_POST['ap'];

	    /** Store the custom fields */
	    foreach ( (array) $property_details as $key => $value ) {

	        /** Save/Update/Delete */
	        if ( $value ) {
	            update_post_meta($post->ID, $key, $value);
	        } else {
	            delete_post_meta($post->ID, $key);
	        }

	    }

 		//* extra check for price that can create a sortable value
 		if ( isset( $property_details['_listing_price'] ) && ! empty( $property_details['_listing_price'] ) ) {

 			$price_sortable	= preg_replace( '/[^0-9\.]/', '', $property_details['_listing_price'] );
 			update_post_meta( $post_id, '_listing_price_sortable', floatval( $price_sortable ) );

 		} else {
 			delete_post_meta( $post_id, '_listing_price_sortable' );
 		}

	}

	/**
	 * Filter the columns in the "Listings" screen, define our own.
	 */
	function columns_filter ( $columns ) {

		$columns = array(
			'cb'                 => '<input type="checkbox" />',
			'listing_thumbnail'  => __( 'Thumbnail', 'agentpress-listings' ),
			'title'              => __( 'Listing Title', 'agentpress-listings' ),
			'listing_details'    => __( 'Details', 'agentpress-listings' ),
			'listing_features'   => __( 'Features', 'agentpress-listings' ),
			'listing_categories' => __( 'Categories', 'agentpress-listings' )
		);

		return $columns;

	}

	/**
	 * Filter the data that shows up in the columns in the "Listings" screen, define our own.
	 */
	function columns_data( $column ) {

		global $post, $wp_taxonomies;

		switch( $column ) {
			case "listing_thumbnail":
				printf( '<p>%s</p>', genesis_get_image( array( 'size' => 'thumbnail' ) ) );
				break;
			case "listing_details":
				foreach ( (array) $this->property_details['col1'] as $label => $key ) {
					printf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
				}
				foreach ( (array) $this->property_details['col2'] as $label => $key ) {
					printf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );
				}
				break;
			case "listing_features":
				echo get_the_term_list( $post->ID, 'features', '', ', ', '' );
				break;
			case "listing_categories":
				foreach ( (array) get_option( $this->settings_field ) as $key => $data ) {
					printf( '<b>%s:</b> %s<br />', esc_html( $data['labels']['singular_name'] ), get_the_term_list( $post->ID, $key, '', ', ', '' ) );
				}
				break;
		}

	}

	function property_details_shortcode( $atts ) {

		global $post;

		$output = '';

		$output .= '<div class="property-details">';

		$output .= '<div class="property-details-col1 one-half first">';
			foreach ( (array) $this->property_details['col1'] as $label => $key ) {
				$output .= sprintf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );	
			}
		$output .= '</div><div class="property-details-col2 one-half">';
			foreach ( (array) $this->property_details['col2'] as $label => $key ) {
				$output .= sprintf( '<b>%s</b> %s<br />', esc_html( $label ), esc_html( get_post_meta($post->ID, $key, true) ) );	
			}
		$output .= '</div><div class="clear">';
			$output .= sprintf( '<p><b>%s</b><br /> %s</p></div>', __( 'Additional Features:', 'agentpress-listings' ), get_the_term_list( $post->ID, 'features', '', ', ', '' ) );

		$output .= '</div>';

		return $output;

	}

	function property_map_shortcode( $atts ) {

		return genesis_get_custom_field( '_listing_map' );

	}

	function property_video_shortcode( $atts ) {

		return genesis_get_custom_field( '_listing_video' );

	}

	function admin_js() {

		wp_enqueue_script( 'accesspress-admin-js', APL_URL . 'includes/js/admin.js', array(), APL_VERSION, true );

	}

	function search_template( $template ) {

		$post_type = get_query_var( 'post_type' );

		if ( is_array( $post_type ) || 'listing' != $post_type ) {
			return $template;
		}

		$listing_template = locate_template( array( 'archive-listing.php' ), false );

		return $listing_template ? $listing_template : $template;	

	}

	function breadcrumbs( $crumbs, $args ) {

		$post_type = get_query_var( 'post_type' );

		if ( is_array( $post_type ) || 'listing' != $post_type ) {
			return $crumbs;
		}

		array_pop( $crumbs );

		$crumbs[] = __( 'Listing Search Results', 'agentpress-listings' );

		return $crumbs;

	}

}
