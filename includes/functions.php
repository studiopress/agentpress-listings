<?php
/**
 * Holds miscellaneous functions for use in the AgentPress Listings plugin
 *
 */


/**
 * This function redirects the user to an admin page, and adds query args
 * to the URL string for alerts, etc.
 *
 * This is just a temporary function, until WordPress fixes add_query_arg(),
 * or Genesis 1.8 is released, whichever comes first.
 *
 */
function apl_admin_redirect( $page, $query_args = array() ) {

	if ( ! $page )
		return;

	$url = html_entity_decode( menu_page_url( $page, 0 ) );

	foreach ( (array) $query_args as $key => $value ) {
		if ( isset( $key ) && isset( $value ) ) {
			$url = add_query_arg( $key, $value, $url );
		}
	}

	wp_redirect( esc_url_raw( $url ) );

}