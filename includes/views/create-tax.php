<?php
/**
 * Create taxonomy view.
 *
 * @package agentpress-listing
 */

?>

<h2><?php esc_html_e( 'Listing Taxonomies', 'agentpress-listings' ); ?></h2>

<div id="col-container">

	<div id="col-right">
	<div class="col-wrap">

		<h3><?php esc_html_e( 'Current Listing Taxonomies', 'agentpress-listings' ); ?></h3>
		<table class="widefat tag fixed" cellspacing="0">
			<thead>
			<tr>
			<th scope="col" class="manage-column column-slug"><?php esc_html_e( 'ID', 'agentpress-listings' ); ?></th>
			<th scope="col" class="manage-column column-singular-name"><?php esc_html_e( 'Singular Name', 'agentpress-listings' ); ?></th>
			<th scope="col" class="manage-column column-plural-name"><?php esc_html_e( 'Plural Name', 'agentpress-listings' ); ?></th>
			</tr>
			</thead>

			<tfoot>
			<tr>
			<th scope="col" class="manage-column column-slug"><?php esc_html_e( 'ID', 'agentpress-listings' ); ?></th>
			<th scope="col" class="manage-column column-singular-name"><?php esc_html_e( 'Singular Name', 'agentpress-listings' ); ?></th>
			<th scope="col" class="manage-column column-plural-name"><?php esc_html_e( 'Plural Name', 'agentpress-listings' ); ?></th>
			</tr>
			</tfoot>

			<tbody id="the-list" class="list:tag">

				<?php
				$alt = true;

				$listing_taxonomies = array_merge( $this->property_features_taxonomy(), get_option( $this->settings_field ) );

				foreach ( (array) $listing_taxonomies as $tax_id => $data ) :
					?>

				<tr
					<?php
					if ( $alt ) {
						echo 'class="alternate"';
						$alt = false;
					} else {
						$alt = true; }
					?>
				>
					<td class="slug column-slug">

					<?php if ( isset( $data['editable'] ) && 0 === $data['editable'] ) : ?>
						<?php echo '<strong>' . esc_html( $tax_id ) . '</strong><br /><br />'; ?>
					<?php else : ?>
						<a class="row-title" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_page . '&amp;view=edit&amp;id=' . esc_html( $tax_id ) ) ); ?>"><?php echo esc_html( $tax_id ); ?></a>

						<br />

						<div class="row-actions">
							<span class="edit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->menu_page . '&amp;view=edit&amp;id=' . esc_html( $tax_id ) ) ); ?>"><?php esc_html_e( 'Edit', 'agentpress-listings' ); ?></a> | </span>
							<span class="delete"><a class="delete-tag" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=' . $this->menu_page . '&amp;action=delete&amp;id=' . esc_html( $tax_id ) ) ), 'agentpress-action_delete-taxonomy' ); ?>"><?php esc_html_e( 'Delete', 'agentpress-listings' ); ?></a></span>
						</div>
					<?php endif; ?>

					</td>
					<td class="singular-name column-singular-name"><?php echo esc_html( $data['labels']['singular_name'] ); ?></td>
					<td class="plural-name column-plural-name"><?php echo esc_html( $data['labels']['name'] ); ?></td>
				</tr>

				<?php endforeach; ?>

			</tbody>
		</table>

	</div>
	</div><!-- /col-right -->

	<div id="col-left">
	<div class="col-wrap">

		<div class="form-wrap">
			<h3><?php esc_html_e( 'Add New Listing Taxonomy', 'agentpress-listings' ); ?></h3>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=register-taxonomies&amp;action=create' ) ); ?>">
			<?php wp_nonce_field( 'agentpress-action_create-taxonomy' ); ?>

			<div class="form-field">
				<label for="taxonomy-id"><?php esc_html_e( 'ID', 'agentpress-listings' ); ?></label>
				<input name="agentpress_taxonomy[id]" id="taxonomy-id" type="text" value="" size="40" />
				<p><?php esc_html_e( 'The unique ID is used to register the taxonomy. (no spaces, underscores, or special characters)', 'agentpress-listings' ); ?></p>
			</div>

			<div class="form-field form-required">
				<label for="taxonomy-name"><?php esc_html_e( 'Plural Name', 'agentpress-listings' ); ?></label>
				<input name="agentpress_taxonomy[name]" id="taxonomy-name" type="text" value="" size="40" />
				<p><?php esc_html_e( 'Example: "Property Types" or "Locations"', 'agentpress-listings' ); ?></p>
			</div>

			<div class="form-field form-required">
				<label for="taxonomy-singular-name"><?php esc_html_e( 'Singular Name', 'agentpress-listings' ); ?></label>
				<input name="agentpress_taxonomy[singular_name]" id="taxonomy-singular-name" type="text" value="" size="40" />
				<p><?php esc_html_e( 'Example: "Property Type" or "Location"', 'agentpress-listings' ); ?></p>
			</div>

			<?php submit_button( __( 'Add New Taxonomy', 'agentpress-listings' ), 'secondary' ); ?>
			</form>
		</div>

	</div>
	</div><!-- /col-left -->

</div><!-- /col-container -->
