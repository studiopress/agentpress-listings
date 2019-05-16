<?php
/**
 * This file contains the AgentPress_Taxonomies class.
 *
 * @package agentpress-listings
 */

/**
 * This class handles all the aspects of displaying, creating, and editing the
 * user-created taxonomies for the "Listings" post-type.
 */
class AgentPress_Taxonomies {

	/**
	 * Settings field
	 *
	 * @var string
	 */
	public $settings_field = 'agentpress_taxonomies';

	/**
	 * Menu page.
	 *
	 * @var string
	 */
	public $menu_page = 'register-taxonomies';

	/**
	 * Construct Method.
	 */
	public function __construct() {

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'settings_init' ), 15 );
		add_action( 'admin_init', array( &$this, 'actions' ) );
		add_action( 'admin_notices', array( &$this, 'notices' ) );

		add_action( 'init', array( &$this, 'register_taxonomies' ), 15 );

	}

	/**
	 * Register settings.
	 */
	public function register_settings() {

		register_setting( $this->settings_field, $this->settings_field );
		add_option( $this->settings_field, __return_empty_array(), '', 'yes' );

	}

	/**
	 * Init settings.
	 */
	public function settings_init() {

		add_submenu_page( 'edit.php?post_type=listing', __( 'Register Taxonomies', 'agentpress-listings' ), __( 'Register Taxonomies', 'agentpress-listings' ), 'manage_options', $this->menu_page, array( &$this, 'admin' ) );

	}

	/**
	 * Actions.
	 */
	public function actions() {

		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== $this->menu_page ) {
			return;
		}

		/** This section handles the data if a new taxonomy is created */
		if ( isset( $_REQUEST['action'] ) && 'create' === $_REQUEST['action'] && isset( $_POST['agentpress_taxonomy'] ) ) {
			$this->create_taxonomy( array_map( 'sanitize_text_field', wp_unslash( $_POST['agentpress_taxonomy'] ) ) );
		}

		/** This section handles the data if a taxonomy is deleted */
		if ( isset( $_REQUEST['action'] ) && 'delete' === $_REQUEST['action'] && isset( $_REQUEST['id'] ) ) {
			$this->delete_taxonomy( sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) );
		}

		/** This section handles the data if a taxonomy is being edited */
		if ( isset( $_REQUEST['action'] ) && 'edit' === $_REQUEST['action'] ) {
			$this->edit_taxonomy( array_map( 'sanitize_text_field', wp_unslash( $_POST['agentpress_taxonomy'] ) ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.NoNonceVerification

	}

	/**
	 * Admin area.
	 */
	public function admin() {

		echo '<div class="wrap">';

		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( isset( $_REQUEST['view'] ) && 'edit' === $_REQUEST['view'] ) {
			require dirname( __FILE__ ) . '/views/edit-tax.php';
		} else {
			require dirname( __FILE__ ) . '/views/create-tax.php';
		}

		echo '</div>';

	}

	/**
	 * Create the taxonomy.
	 *
	 * @param array $args Arguments.
	 */
	public function create_taxonomy( $args = array() ) {

		// VERIFY THE NONCE.

		/** No empty fields */
		if ( ! isset( $args['id'] ) || empty( $args['id'] ) ) {
			wp_die( esc_html__( 'Please complete all required fields.', 'agentpress-listings' ) );
		}
		if ( ! isset( $args['name'] ) || empty( $args['name'] ) ) {
			wp_die( esc_html__( 'Please complete all required fields.', 'agentpress-listings' ) );
		}
		if ( ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) ) {
			wp_die( esc_html__( 'Please complete all required fields.', 'agentpress-listings' ) );
		}

		// Sanitize id.
		$sanitized_id = sanitize_key( $args['id'] );

		// Bail, if not a valid ID after sanitization.
		if ( ! $sanitized_id || is_numeric( $sanitized_id ) ) {
			wp_die( esc_html__( 'You have given this taxonomy an invalid slug/ID. Please try again.', 'agentpress-listings' ) );
		}

		$labels = array(
			'name'                  => wp_strip_all_tags( $args['name'] ),
			'singular_name'         => wp_strip_all_tags( $args['singular_name'] ),
			'menu_name'             => wp_strip_all_tags( $args['name'] ),

			// translators: %s is for name.
			'search_items'          => sprintf( esc_html__( 'Search %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for name.
			'popular_items'         => sprintf( esc_html__( 'Popular %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for name.
			'all_items'             => sprintf( esc_html__( 'All %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for singular name.
			'edit_item'             => sprintf( esc_html__( 'Edit %s', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for singular name.
			'update_item'           => sprintf( esc_html__( 'Update %s', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for singular name.
			'add_new_item'          => sprintf( esc_html__( 'Add New %s', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for singular name.
			'new_item_name'         => sprintf( esc_html__( 'New %s Name', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for name.
			'add_or_remove_items'   => sprintf( esc_html__( 'Add or Remove %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for name.
			'choose_from_most_used' => sprintf( esc_html__( 'Choose from the most used %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => $sanitized_id ),
			'editable'     => 1,
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

	/**
	 * Delete taxonomy.
	 *
	 * @param  string $id Taxonomy id.
	 */
	public function delete_taxonomy( $id = '' ) {

		// VERIFY THE NONCE.
		$options = get_option( $this->settings_field );

		/** Remove any IDs that were somehow made or left blank */
		if ( ! isset( $id ) || empty( $id ) ) {

			$opts = array();

			foreach ( $options as $key => $value ) {

				if ( ! empty( $key ) ) {
					$opts[ $key ] = $value;
				}
			}

			update_option( $this->settings_field, $opts );

		}

		/** Look for the ID, delete if it exists */
		if ( array_key_exists( $id, (array) $options ) ) {
			unset( $options[ $id ] );
		} else {
			wp_die( esc_html__( "Nice try, partner. But that taxonomy doesn't exist. Click back and try again.", 'agentpress-listings' ) );
		}

		/** Update the DB */
		update_option( $this->settings_field, $options );

		apl_admin_redirect( 'register-taxonomies', array( 'deleted' => 'true' ) );
		exit;

	}

	/**
	 * Edit Taxonomy.
	 *
	 * @param  array $args arguments.
	 */
	public function edit_taxonomy( $args = array() ) {

		// VERIFY THE NONCE.

		/** No empty fields */
		if ( ! isset( $args['id'] ) || empty( $args['id'] ) ) {
			wp_die( esc_html__( 'Please complete all required fields.', 'agentpress-listings' ) );
		}

		if ( ! isset( $args['name'] ) || empty( $args['name'] ) ) {
			wp_die( esc_html__( 'Please complete all required fields.', 'agentpress-listings' ) );
		}

		if ( ! isset( $args['singular_name'] ) || empty( $args['singular_name'] ) ) {
			wp_die( esc_html__( 'Please complete all required fields.', 'agentpress-listings' ) );
		}

		$id = $args['id'];

		$labels = array(
			'name'                  => wp_strip_all_tags( $args['name'] ),
			'singular_name'         => wp_strip_all_tags( $args['singular_name'] ),
			'menu_name'             => wp_strip_all_tags( $args['name'] ),

			// translators: %s is for name.
			'search_items'          => sprintf( esc_html__( 'Search %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for name.
			'popular_items'         => sprintf( esc_html__( 'Popular %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for name.
			'all_items'             => sprintf( esc_html__( 'All %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for singular name.
			'edit_item'             => sprintf( esc_html__( 'Edit %s', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for singular name.
			'update_item'           => sprintf( esc_html__( 'Update %s', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for singular name.
			'add_new_item'          => sprintf( esc_html__( 'Add New %s', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for singular name.
			'new_item_name'         => sprintf( esc_html__( 'New %s Name', 'agentpress-listings' ), wp_strip_all_tags( $args['singular_name'] ) ),
			// translators: %s is for name.
			'add_or_remove_items'   => sprintf( esc_html__( 'Add or Remove %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
			// translators: %s is for name.
			'choose_from_most_used' => sprintf( esc_html__( 'Choose from the most used %s', 'agentpress-listings' ), wp_strip_all_tags( $args['name'] ) ),
		);

		$args = array(
			'labels'       => $labels,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => $id ),
			'editable'     => 1,
		);

		$tax = array( $id => $args );

		$options = get_option( $this->settings_field );

		update_option( $this->settings_field, wp_parse_args( $tax, $options ) );

		apl_admin_redirect( 'register-taxonomies', array( 'edited' => 'true' ) );
		exit;

	}

	/**
	 * Notices.
	 */
	public function notices() {

		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( ! isset( $_REQUEST['page'] ) || $_REQUEST['page'] !== $this->menu_page ) {
			return;
		}

		$format = '<div id="message" class="updated"><p><strong>%s</strong></p></div>';

		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( isset( $_REQUEST['created'] ) && 'true' === $_REQUEST['created'] ) {
			printf( wp_kses_post( $format ), esc_html__( 'New taxonomy successfully created!', 'agentpress-listings' ) );
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( isset( $_REQUEST['edited'] ) && 'true' === $_REQUEST['edited'] ) {
			printf( wp_kses_post( $format ), esc_html__( 'Taxonomy successfully edited!', 'agentpress-listings' ) );
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
		if ( isset( $_REQUEST['deleted'] ) && 'true' === $_REQUEST['deleted'] ) {
			printf( wp_kses_post( $format ), esc_html__( 'Taxonomy successfully deleted.', 'agentpress-listings' ) );
			return;
		}
	}

	/**
	 * Register the property features taxonomy, manually.
	 */
	public function property_features_taxonomy() {

		$name = 'Features';

		$singular_name = 'Feature';

		return array(
			'features' => array(
				'labels'       =>
					array(
						'name'                  => wp_strip_all_tags( $name ),
						'singular_name'         => wp_strip_all_tags( $singular_name ),
						'menu_name'             => wp_strip_all_tags( $name ),

						// translators: %s is for name.
						'search_items'          => sprintf( esc_html__( 'Search %s', 'agentpress-listings' ), wp_strip_all_tags( $name ) ),
						// translators: %s is for name.
						'popular_items'         => sprintf( esc_html__( 'Popular %s', 'agentpress-listings' ), wp_strip_all_tags( $name ) ),
						// translators: %s is for name.
						'all_items'             => sprintf( esc_html__( 'All %s', 'agentpress-listings' ), wp_strip_all_tags( $name ) ),
						// translators: %s is for singular name.
						'edit_item'             => sprintf( esc_html__( 'Edit %s', 'agentpress-listings' ), wp_strip_all_tags( $singular_name ) ),
						// translators: %s is for singular name.
						'update_item'           => sprintf( esc_html__( 'Update %s', 'agentpress-listings' ), wp_strip_all_tags( $singular_name ) ),
						// translators: %s is for singular name.
						'add_new_item'          => sprintf( esc_html__( 'Add New %s', 'agentpress-listings' ), wp_strip_all_tags( $singular_name ) ),
						// translators: %s is for singular name.
						'new_item_name'         => sprintf( esc_html__( 'New %s Name', 'agentpress-listings' ), wp_strip_all_tags( $singular_name ) ),
						// translators: %s is for name.
						'add_or_remove_items'   => sprintf( esc_html__( 'Add or Remove %s', 'agentpress-listings' ), wp_strip_all_tags( $name ) ),
						// translators: %s is for name.
						'choose_from_most_used' => sprintf( esc_html__( 'Choose from the most used %s', 'agentpress-listings' ), wp_strip_all_tags( $name ) ),
					),
				'hierarchical' => 0,
				'rewrite'      => array(
					'features',
				),
				'editable'     => 0,
			),
		);

	}

	/**
	 * Create the taxonomies.
	 */
	public function register_taxonomies() {

		foreach ( (array) $this->get_taxonomies() as $id => $data ) {
			register_taxonomy( $id, array( 'listing' ), $data );
		}

	}

	/**
	 * Get the taxonomies.
	 */
	public function get_taxonomies() {

		return array_merge( $this->property_features_taxonomy(), (array) get_option( $this->settings_field ) );

	}
}
