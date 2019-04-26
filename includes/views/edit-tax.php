<?php
/**
 * Edit Taxonomy View.
 *
 * @package agentpress-listing
 */

$options = get_option( $this->settings_field );

// phpcs:ignore WordPress.Security.NonceVerification.NoNonceVerification
$option_key = ( isset( $_REQUEST['id'] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) : '';

if ( ! empty( $option_key ) && array_key_exists( $option_key, (array) $options ) ) {
	$received_taxonomy = stripslashes_deep( $options[ $option_key ] );
} else {
	wp_die( esc_html__( "Nice try, partner. But that taxonomy doesn't exist or can't be edited. Click back and try again.", 'agentpress-listings' ) );
}
?>

<h2><?php esc_html_e( 'Edit Taxonomy', 'agentpress-listings' ); ?></h2>

<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_page . '&amp;action=edit' ) ); ?>">
<?php wp_nonce_field( 'agentpress-action_edit-taxonomy' ); ?>
<table class="form-table">

	<tr class="form-field">
		<th scope="row" valign="top"><label for="agentpress_taxonomy[id]"><?php esc_html_e( 'ID', 'agentpress-listings' ); ?></label></th>
		<td>
		<input type="text" value="<?php echo esc_attr( $option_key ); ?>" size="40" disabled="disabled" />
		<input name="agentpress_taxonomy[id]" id="agentpress_taxonomy[id]" type="hidden" value="<?php echo esc_attr( $option_key ); ?>" size="40" />
		<p class="description"><?php esc_html_e( 'The unique ID is used to register the taxonomy. (cannot be changed)', 'agentpress-listings' ); ?></p></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="agentpress_taxonomy[name]"><?php esc_html_e( 'Plural Name', 'agentpress-listings' ); ?></label></th>
		<td><input name="agentpress_taxonomy[name]" id="agentpress_taxonomy[name]" type="text" value="<?php echo esc_html( $received_taxonomy['labels']['name'] ); ?>" size="40" />
		<p class="description"><?php esc_html_e( 'Example: "Property Types" or "Locations"', 'agentpress-listings' ); ?></p></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="agentpress_taxonomy[singular_name]"><?php esc_html_e( 'Singular Name', 'agentpress-listings' ); ?></label></th>
		<td><input name="agentpress_taxonomy[singular_name]" id="agentpress_taxonomy[singular_name]" type="text" value="<?php echo esc_html( $received_taxonomy['labels']['singular_name'] ); ?>" size="40" />
		<p class="description"><?php esc_html_e( 'Example: "Property Type" or "Location"', 'agentpress-listings' ); ?></p></td>
	</tr>

</table>

<?php submit_button( __( 'Update', 'agentpress-listings' ) ); ?>

</form>
