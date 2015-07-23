<?php
/**
 * This file contains the AgentPress_Taxonomies class.
 */

/**
 * This class handles all the aspects of displaying, creating, and editing the
 * user-created taxonomies for the "Listings" post-type.
 *
 */
class AgentPress_Taxonomies {

	var $settings_field = 'agentpress_taxonomies';
	var $menu_page = 'register-taxonomies';

	/**
	 * Construct Method.
	 */
	function __construct() {

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'settings_init' ), 15 );
		add_action( 'admin_init', array( &$this, 'actions' ) );
		add_action( 'admin_notices', array( &$this, 'notices' ) );

		add_action( 'init', array( &$this, 'register_taxonomies' ), 15 );

	}

	function register_settings() {

		register_setting( $this->settings_field, $this->settings_field );
		add_option( $this->settings_field, __return_empty_array(), '', 'yes' );

	}

	function settings_init() {

		add_submenu_page( 'edit.php?post_type=listing', __( 'Register Taxonomies', 'agentpress-listings' ), __( 'Register Taxonomies', 'agentpress-listings' ), 'manage_options', $this->menu_page, array( &$this, 'admin' ) );

	}

	function actions() {

		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != $this->menu_page ) {
			return;
		}

		/** This section handles the data if a new taxonomy is created */
		if ( isset( $_REQUEST['action'] ) && 'create' == $_REQUEST['action'] ) {
			$this->create_taxonomy( $_POST['agentpress_taxonomy'] );
		}

		/** This section handles the data if a taxonomy is deleted */
		if ( isset( $_REQUEST['action'] ) && 'delete' == $_REQUEST['action'] ) {
			$this->delete_taxonomy( $_REQUEST['id'] );
		}

		/** This section handles the data if a taxonomy is being edited */
		if ( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] ) {
			$this->edit_taxonomy( $_POST['agentpress_taxonomy'] );
		}

	}

	function admin() {

		echo '<div class="wrap">';

			if ( isset( $_REQUEST['view'] ) && 'edit' == $_REQUEST['view'] ) {
				require( dirname( __FILE__ ) . '/views/edit-tax.php' );
			}
			else {
				require( dirname( __FILE__ ) . '/views/create-tax.php' );
			}

		echo '</div>';

	}

	function create_taxonomy( $args = array() ) {

		/**** VERIFY THE NONCE ****/

		/** No empty fields */
		if ( ! isset( $args['id'] ) || empty( $args['id'] ) ) {
			wp_die( __( 'Please complete all required fields.', 'agentpress-listings' ) );
		}
		if ( ! isset( $args['name'] ) || empty( $args['name'] ) ) {
			wp_die( __( 'Please complete all required fields.', 'agentpress-listings' ) );
		}
		if ( ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) ) {
			wp_die( __( 'Please complete all required fields.', 'agentpress-listings' ) );
		}

		//* Sanitize id
		$sanitized_id = sanitize_key( $args['id'] );

		//* Bail, if not a valid ID after sanitization
		if ( ! $sanitized_id || is_numeric( $sanitized_id ) ) {
			wp_die( __( 'You have given this taxonomy an invalid slug/ID. Please try again.', 'agentpress-listings' ) );
		}

		$labels = array(
			'name'                  => strip_tags( $args['name'] ),
			'singular_name'         => strip_tags( $args['singular_name'] ),
			'menu_name'             => strip_tags( $args['name'] ),

			'search_items'          => sprintf( __( 'Search %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'popular_items'         => sprintf( __( 'Popular %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'all_items'             => sprintf( __( 'All %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'edit_item'             => sprintf( __( 'Edit %s', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'update_item'           => sprintf( __( 'Update %s', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'new_item_name'         => sprintf( __( 'New %s Name', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'add_or_remove_items'   => sprintf( __( 'Add or Remove %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'choose_from_most_used' => sprintf( __( 'Choose from the most used %s', 'agentpress-listings' ), strip_tags( $args['name'] ) )
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => $sanitized_id ),
			'editable'     => 1
		);

		$tax = array( $sanitized_id => $args );

		$options = get_option( $this->settings_field );

		/** Update the options */
		update_option( $this->settings_field, wp_parse_args( $tax, $options ) );

		/** Flush rewrite rules */
		$this->register_taxonomies();
		flush_rewrite_rules();

		/** Redirect with notice */
		apl_admin_redirect( 'register-taxonomies', array( 'created' => 'true' ) );
		exit;

	}

	function delete_taxonomy( $id = '' ) {

		/**** VERIFY THE NONCE ****/
		
		$options = get_option( $this->settings_field );

		/** Remove any IDs that were somehow made or left blank */
		if ( ! isset( $id ) || empty( $id ) ){
			
			$opts = array();
			
			foreach( $options as $key => $value ){
				
				if( ! empty( $key ) ){
					
					$opts[$key] = $value;
					
				}
				
			}
			
			update_option( $this->settings_field, $opts );
			
		}
		
		/** Look for the ID, delete if it exists */
		if ( array_key_exists( $id, (array) $options ) ) {
			unset( $options[$id] );
		} else {
			wp_die( __( "Nice try, partner. But that taxonomy doesn't exist. Click back and try again.", 'agentpress-listings' ) );
		}

		/** Update the DB */
		update_option( $this->settings_field, $options );

		apl_admin_redirect( 'register-taxonomies', array( 'deleted' => 'true' ) );
		exit;

	}

	function edit_taxonomy( $args = array() ) {

		/**** VERIFY THE NONCE ****/

		/** No empty fields */
		if ( ! isset( $args['id'] ) || empty( $args['id'] ) )
			wp_die( __( 'Please complete all required fields.', 'agentpress-listings' ) );
		if ( ! isset( $args['name'] ) || empty( $args['name'] ) )
			wp_die( __( 'Please complete all required fields.', 'agentpress-listings' ) );
		if ( ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) )
			wp_die( __( 'Please complete all required fields.', 'agentpress-listings' ) );
			
		$id = $args['id'];

		$labels = array(
			'name'                  => strip_tags( $args['name'] ),
			'singular_name'         => strip_tags( $args['singular_name'] ),
			'menu_name'             => strip_tags( $args['name'] ),

			'search_items'          => sprintf( __( 'Search %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'popular_items'         => sprintf( __( 'Popular %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'all_items'             => sprintf( __( 'All %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'edit_item'             => sprintf( __( 'Edit %s', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'update_item'           => sprintf( __( 'Update %s', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'new_item_name'         => sprintf( __( 'New %s Name', 'agentpress-listings' ), strip_tags( $args['singular_name'] ) ),
			'add_or_remove_items'   => sprintf( __( 'Add or Remove %s', 'agentpress-listings' ), strip_tags( $args['name'] ) ),
			'choose_from_most_used' => sprintf( __( 'Choose from the most used %s', 'agentpress-listings' ), strip_tags( $args['name'] ) )
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => $id ),
			'editable'     => 1
		);

		$tax = array( $id => $args );

		$options = get_option( $this->settings_field );

		update_option( $this->settings_field, wp_parse_args( $tax, $options ) );

		apl_admin_redirect( 'register-taxonomies', array( 'edited' => 'true' ) );
		exit;

	}

	function notices() {

		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] != $this->menu_page ) {
			return;
		}

		$format = '<div id="message" class="updated"><p><strong>%s</strong></p></div>';

		if ( isset( $_REQUEST['created'] ) && 'true' == $_REQUEST['created'] ) {
			printf( $format, __('New taxonomy successfully created!', 'agentpress-listings') );
			return;
		}

		if ( isset( $_REQUEST['edited'] ) && 'true' == $_REQUEST['edited'] ) {
			printf( $format, __('Taxonomy successfully edited!', 'agentpress-listings') );
			return;
		}

		if ( isset( $_REQUEST['deleted'] ) && 'true' == $_REQUEST['deleted'] ) {
			printf( $format, __('Taxonomy successfully deleted.', 'agentpress-listings') );
			return;
		}

		return;

	}

	/**
	 * Register the property features taxonomy, manually.
	 */
	function property_features_taxonomy() {

		$name = 'Features';
		$singular_name = 'Feature';

		return array(
			'features' => array(
				'labels' => array(
					'name'                  => strip_tags( $name ),
					'singular_name'         => strip_tags( $singular_name ),
					'menu_name'             => strip_tags( $name ),

					'search_items'          => sprintf( __( 'Search %s', 'agentpress-listings' ), strip_tags( $name ) ),
					'popular_items'         => sprintf( __( 'Popular %s', 'agentpress-listings' ), strip_tags( $name ) ),
					'all_items'             => sprintf( __( 'All %s', 'agentpress-listings' ), strip_tags( $name ) ),
					'edit_item'             => sprintf( __( 'Edit %s', 'agentpress-listings' ), strip_tags( $singular_name ) ),
					'update_item'           => sprintf( __( 'Update %s', 'agentpress-listings' ), strip_tags( $singular_name ) ),
					'add_new_item'          => sprintf( __( 'Add New %s', 'agentpress-listings' ), strip_tags( $singular_name ) ),
					'new_item_name'         => sprintf( __( 'New %s Name', 'agentpress-listings' ), strip_tags( $singular_name ) ),
					'add_or_remove_items'   => sprintf( __( 'Add or Remove %s', 'agentpress-listings' ), strip_tags( $name ) ),
					'choose_from_most_used' => sprintf( __( 'Choose from the most used %s', 'agentpress-listings' ), strip_tags( $name ) )
				),
				'hierarchical' => 0,
				'rewrite' => array( 'features' ),
				'editable' => 0
			)
		);

	}

	/**
	 * Create the taxonomies.
	 */
	function register_taxonomies() {

		foreach( (array) $this->get_taxonomies() as $id => $data ) {
			register_taxonomy( $id, array( 'listing' ), $data );
		}

	}

	/**
	 * Get the taxonomies.
	 */
	function get_taxonomies() {

		return array_merge( $this->property_features_taxonomy(), (array) get_option( $this->settings_field ) );

	}
}