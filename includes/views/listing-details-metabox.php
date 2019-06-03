<?php
/**
 * Details Metabox View.
 *
 * @package agentpress-listings
 */

wp_nonce_field( 'agentpress_details_metabox_save', 'agentpress_details_metabox_nonce' );

echo '<div style="width: 90%; float: left">';

	printf( '<p><label>%s<input type="text" name="ap[_listing_text]" value="%s" /></label></p>', esc_html__( 'Custom Text: ', 'agentpress-listings' ), esc_attr( genesis_get_custom_field( '_listing_text' ) ) );
	printf( '<p><span class="description">%s</span></p>', esc_html__( 'Custom text shows on the featured listings widget image.', 'agentpress-listings' ) );

echo '</div><br style="clear: both;" /><br /><br />';

$pattern = '<p><label>%s<br /><input type="text" name="ap[%s]" value="%s" /></label></p>';

echo '<div style="width: 45%; float: left">';

foreach ( (array) $this->property_details['col1'] as $label => $key ) {
	printf( wp_kses( $pattern, $this->allowed_tags ), esc_html( $label ), esc_attr( $key ), esc_attr( genesis_get_custom_field( $key ) ) );
}
	printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_details]\')', esc_html__( 'Send to text editor', 'agentpress-listings' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

foreach ( (array) $this->property_details['col2'] as $label => $key ) {
	printf( wp_kses( $pattern, $this->allowed_tags ), esc_html( $label ), esc_attr( $key ), esc_attr( genesis_get_custom_field( $key ) ) );
}

echo '</div><br style="clear: both;" /><br /><br />';

echo '<div style="width: 45%; float: left;">';

printf( '<p><label>%1$s<br /><textarea name="ap[_listing_map]" rows="5" cols="18" style="%2$s">%3$s</textarea></label></p>', esc_html__( 'Enter Map Embed Code:', 'agentpress-listings' ), 'width: 99%;', wp_kses( genesis_get_custom_field( '_listing_map' ), $this->allowed_tags ) );

printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_map]\')', esc_html__( 'Send to text editor', 'agentpress-listings' ) );

echo '</div>';

echo '<div style="width: 45%; float: left;">';

printf( '<p><label>%1$s:<br /><textarea name="ap[_listing_video]" rows="5" cols="18" style="%2$s">%3$s</textarea></label></p>', esc_html__( 'Enter Video Embed Code', 'agentpress-listings' ), 'width: 99%;', wp_kses( genesis_get_custom_field( '_listing_video' ), $this->allowed_tags ) );

printf( '<p><a class="button" href="%s" onclick="%s">%s</a></p>', '#', 'ap_send_to_editor(\'[property_video]\')', esc_html__( 'Send to text editor', 'agentpress-listings' ) );

echo '</div><br style="clear: both;" />';
