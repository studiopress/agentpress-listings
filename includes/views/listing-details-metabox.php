<?php
wp_nonce_field( 'agentpress_details_metabox_save', 'agentpress_details_metabox_nonce' );

echo '<div style="width: 90%; float: left">';

	printf( '<p><label>%s<input type="text" name="ap[_listing_text]" value="%s" /></label></p>', __( 'Custom Text: ', 'apl' ), esc_attr( genesis_get_custom_field('_listing_text') ) );
	printf( '<p><span class="description">%s</span></p>', __( 'Custom text shows on the featured listings widget image.', 'apl' ) );

echo '</div><br style="clear: both;" /><br /><br />';

$pattern = '<p><label>%s<br /><input type="text" name="ap[%s]" value="%s" /></label></p>';

echo '<div style="width: 45%; float: left">';

	printf( $pattern, __( 'Price:', 'apl' ), '_listing_price', esc_attr( genesis_get_custom_field('_listing_price') ) );
	printf( $pattern, __( 'Address:', 'apl' ), '_listing_address', esc_attr( genesis_get_custom_field('_listing_address') ) );
	printf( $pattern, __( 'City:', 'apl' ), '_listing_city', esc_attr( genesis_get_custom_field('_listing_city') ) );
	printf( $pattern, __( 'State:', 'apl' ), '_listing_state', esc_attr( genesis_get_custom_field('_listing_state') ) );
	printf( $pattern, __( 'ZIP:', 'apl' ), '_listing_zip', esc_attr( genesis_get_custom_field('_listing_zip') ) );
	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_details]\')', __( 'Send to text editor', 'apl' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

	printf( $pattern, __( 'MLS #:', 'apl' ), '_listing_mls', esc_attr( genesis_get_custom_field('_listing_mls') ) );
	printf( $pattern, __( 'Square Feet:', 'apl' ), '_listing_sqft', esc_attr( genesis_get_custom_field('_listing_sqft') ) );
	printf( $pattern, __( 'Bedrooms:', 'apl' ), '_listing_bedrooms', esc_attr( genesis_get_custom_field('_listing_bedrooms') ) );
	printf( $pattern, __( 'Bathrooms:', 'apl' ), '_listing_bathrooms', esc_attr( genesis_get_custom_field('_listing_bathrooms') ) );
	printf( $pattern, __( 'Basement:', 'apl' ), '_listing_basement', esc_attr( genesis_get_custom_field('_listing_basement') ) );

echo '</div><br style="clear: both;" /><br /><br />';

echo '<div style="width: 45%; float: left;">';

	printf( __( '<p><label>Enter Map Embed Code:<br /><textarea name="ap[_listing_map]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'apl' ), 'width: 99%;', htmlentities( genesis_get_custom_field('_listing_map') ) );

	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_map]\')', __( 'Send to text editor', 'apl' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

	printf( __( '<p><label>Enter Video Embed Code:<br /><textarea name="ap[_listing_video]" rows="5" cols="18" style="%s">%s</textarea></label></p>', 'apl' ), 'width: 99%;', htmlentities( genesis_get_custom_field('_listing_video') ) );

	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_video]\')', __( 'Send to text editor', 'apl' ) );

echo '</div><br style="clear: both;" />';