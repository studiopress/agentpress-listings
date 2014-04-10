<?php
/*
	Plugin Name: AgentPress Listings
	Plugin URI: http://www.studiopress.com/
	Description: AgentPress Listings is a plugin which adds a Listings custom post type for Real Estate agents.
	Author: StudioPress
	Author URI: http://www.studiopress.com/

	Version: 1.1.0

	License: GNU General Public License v2.0 (or later)
	License URI: http://www.opensource.org/licenses/gpl-license.php
*/

register_activation_hook( __FILE__, 'agentpress_listings_activation' );
/**
 * This function runs on plugin activation. It checks to make sure the required
 * minimum Genesis version is installed. If not, it deactivates itself.
 *
 * @since 0.1.0
 */
function agentpress_listings_activation() {

		$latest = '1.7.1';

		$theme_info = get_theme_data( TEMPLATEPATH . '/style.css' );

		if ( 'genesis' != basename( TEMPLATEPATH ) ) {
	        deactivate_plugins( plugin_basename( __FILE__ ) ); /** Deactivate ourself */
			wp_die( sprintf( __( 'Sorry, you can\'t activate unless you have installed <a href="%s">Genesis</a>', 'apl' ), 'http://my.studiopress.com/themes/genesis/' ) );
		}

		if ( version_compare( $theme_info['Version'], $latest, '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) ); /** Deactivate ourself */
			wp_die( sprintf( __( 'Sorry, you cannot activate without <a href="%s">Genesis %s</a> or greater', 'apl' ), 'http://www.studiopress.com/support/showthread.php?t=19576', $latest ) );
		}

		/** Flush rewrite rules */
		if ( ! post_type_exists( 'listing' ) ) {
			agentpress_listings_init();
			global $_agentpress_listings, $_agentpress_taxonomies;
			$_agentpress_listings->create_post_type();
			$_agentpress_taxonomies->register_taxonomies();
		}
		flush_rewrite_rules();

}


add_action( 'after_setup_theme', 'agentpress_listings_init' );
/**
 * Initialize AgentPress Listings.
 *
 * Include the libraries, define global variables, instantiate the classes.
 *
 * @since 0.1.0
 */
function agentpress_listings_init() {

	/** Do nothing if a Genesis child theme isn't active */
	if ( ! function_exists( 'genesis_get_option' ) )
		return;

	global $_agentpress_listings, $_agentpress_taxonomies;

	define( 'APL_URL', plugin_dir_url( __FILE__ ) );
	define( 'APL_VERSION', '1.1.0' );

	/** Load textdomain for translation */
	load_plugin_textdomain( 'apl', false, basename( dirname( __FILE__ ) ) . '/languages/' );

	/** Includes */
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-listings.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-taxonomies.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-featured-listings-widget.php' );
	require_once( dirname( __FILE__ ) . '/includes/class-property-search-widget.php' );

	/** Instantiate */
	$_agentpress_listings = new AgentPress_Listings;
	$_agentpress_taxonomies = new AgentPress_Taxonomies;

	add_action( 'widgets_init', 'agentpress_register_widgets' );

}

/**
 * Register Widgets that will be used in the AgentPress Listings plugin
 *
 * @since 0.1.0
 */
function agentpress_register_widgets() {

	$widgets = array( 'AgentPress_Featured_Listings_Widget', 'AgentPress_Listings_Search_Widget' );

	foreach ( (array) $widgets as $widget ) {
		register_widget( $widget );
	}

}

add_action( 'admin_init', 'agentpress_update_check' );
/**
 * This function runs on plugin update to alert users about the widget changes
 *
 * this should only run once
 *
 * @since 1.1.0
 */
function agentpress_update_check() {

	// fetch our current global pagenow
	global $pagenow;

	// fetch our option check
	$update_check	= get_option( 'apl_update_notice' );

	// confirm check hasn't been run and only load for widgets
	if ( $pagenow != 'widgets.php' && ! $update_check ) {

		// create our redirect URL
		$redirect	= admin_url( 'widgets.php?apl-update' );

		// process the redirect
		wp_redirect( esc_url_raw( $redirect ) );
		exit();

	}

}

add_action ( 'admin_notices', 'agentpress_update_message' );
/**
 * Displays the actual update notice based on the query string
 *
 *
 * @since 1.1.0
 */
function agentpress_update_message() {

	// bail if we don't have our query string
	if ( ! isset( $_REQUEST['apl-update'] ) ) {
		return;
	}

	// set our option so we don't have this again
	update_option( 'apl_update_notice', true );

	// set a default message
	$message	= __( 'Your AgentPress Listings and Search widgets will not display correctly until the widgets are saved again. Please save them now.', 'apl' );

	// display message
	echo '<div id="apl-update" class="error" style="border-left:4px solid #FFBA00;">';
	echo '<p><strong>' . __( 'Notice: ', 'apl' ) . '</strong>' . esc_attr( $message ) . '</p>';
	echo '</div>';

	return;

}
