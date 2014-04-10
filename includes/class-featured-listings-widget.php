<?php
/**
 * This widget presents loop content, based on your input, specifically for the homepage.
 *
 * @package AgentPress
 * @since 2.0
 * @author Nathan Rice
 */
class AgentPress_Featured_Listings_Widget extends WP_Widget {

	function AgentPress_Featured_Listings_Widget() {
		$widget_ops = array( 'classname' => 'featured-listings', 'description' => __( 'Display grid-style featured listings', 'apl' ) );
		$control_ops = array( 'width' => 300, 'height' => 350 );
		$this->WP_Widget( 'featured-listings', __( 'AgentPress - Featured Listings', 'apl' ), $widget_ops, $control_ops );
	}

	function listings( $count, $widget_id ) {

		if( false === get_transient( 'agentpress_featured_listings_'.$widget_id ) ) :

			$args	= array(
				'fields'			=> 'ids',
				'post_type'			=> 'listing',
				'posts_per_page'	=> absint( $count ),
			);

			$listings	= get_posts( $args );

			if ( ! $listings ) {
				return false;
			}

			set_transient( 'agentpress_featured_listings_'.$widget_id, $listings, 60*60*72 );

		endif;

		$listings	= get_transient( 'agentpress_featured_listings_'.$widget_id );

		return $listings;

	}

	function listing_data( $post_id ) {

		if ( ! $post_id ) {
			return false;
		}

		if( false === get_transient( 'agentpress_featured_listing_data_'.$post_id ) ) :

			// pull each item
			$link	= get_permalink( $post_id );
			$image	= get_the_post_thumbnail( $post_id, 'properties', array( 'class' => 'attachment-properties' ) );
			$price	= get_post_meta( $post_id, '_listing_price', true );
			$text	= get_post_meta( $post_id, '_listing_text', true );

			// fallback for image
			if ( ! $image ) {
				$image = sprintf( '<img class="attachment-properties" src="%s" alt="%s" />', CHILD_URL.'/images/default-listing.png', get_the_title( $post_id ) );
			}

			// pull address info
			$street	= get_post_meta( $post_id, '_listing_address', true );
			$city	= get_post_meta( $post_id, '_listing_city', true );
			$state	= get_post_meta( $post_id, '_listing_state', true );
			$zip	= get_post_meta( $post_id, '_listing_zip', true );

			// create array for storing
			$data	= array(
				'link'				=> $link,
				'image'				=> $image,
				'price'				=> $price,
				'text'				=> $text,
				'street'			=> $street,
				'city-state-zip'	=> array(
					'city'		=> $city,
					'state'		=> $state,
					'zip'		=> $zip
				),

			);

			set_transient( 'agentpress_featured_listing_data_'.$post_id, $data, 60*60*72 );

		endif;

		$data	= get_transient( 'agentpress_featured_listing_data_'.$post_id );

		return $data;

	}

	function listing_display( $data ) {

		$item	= ''; // initialze the $item variable

		$item	.= sprintf( '<a href="%s">%s</a>', esc_url( $data['link'] ), $data['image'] );

		if ( ! empty( $data['price'] ) ) {
			$item	.= sprintf( '<span class="listing-price">%s</span>', esc_attr( $data['price'] ) );
		}

		if ( ! empty( $data['text'] ) ) {
			$item .= sprintf( '<span class="listing-text">%s</span>', esc_html( $data['text'] ) );
		}

		// start build out of address
		if ( ! empty( $data['street'] ) ) {
			$item .= sprintf( '<span class="listing-address">%s</span>', esc_html( $data['street'] ) );
		}

		// start build out of address
		if ( ! empty( $data['city-state-zip'] ) ) {
			// implode the city / state setup to make sure commas are OK
			$ctst	= array( $data['city-state-zip']['city'], $data['city-state-zip']['state'] );
			$ctst	= implode( ', ', $ctst );
			// set the zipcode
			$zip	= ! empty( $data['city-state-zip']['zip' ] ) ? esc_html( $data['city-state-zip']['zip'] ) : '';
			// show the stuff
			$item .= sprintf( '<span class="listing-city-state-zip">%s %s</span>', esc_html( rtrim( $ctst, ', ' ) ), $zip );
		}

		$item .= sprintf( '<a href="%s" class="more-link">%s</a>', esc_url( $data['link'] ), __( 'View Listing', 'apl' ) );

		// send it back
		return $item;

	}

	function widget( $args, $instance ) {

		extract( $args );

		// set our count with fallback
		$count	= isset( $instance['count'] ) && ! empty( $instance['count'] ) ? absint( $instance['count'] ) : 10;

		// go fetch our items
		$listings	= $this->listings( $count, $args['widget_id'] );

		// bail if no listings found
		if ( ! $listings ) {
			return;
		}

		// begin build
		echo $before_widget;

			// check for title and run filter
			$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			// display title if present
			if ( ! empty( $title ) ) { echo $before_title . $title . $after_title; };

			$toggle = ''; // for left/right class

			// loop through our listing IDs
			foreach ( $listings as $listing_id ) {

				// fetch our listing data
				$data	= $this->listing_data( $listing_id );

				if ( ! $data ) {
					continue;
				}

				$display	= $this->listing_display( $data );

				// set our toggle class
				$toggle = $toggle == 'left' ? 'right' : 'left';

				// fetch our post class
				$class	= join( ' ', get_post_class( $toggle, $listing_id ) );

				// run the final filter
				$display	= apply_filters( 'agentpress_featured_listings_widget_loop', $display );

				// now set the display and return it
				echo '<div class="'.$class.'">';
					echo '<div class="widget-wrap">';
						echo '<div class="listing-wrap">'.$display.'</div>';
					echo '</div>';
				echo '</div>';

			} // end listing item loop

		echo $after_widget;

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']	= sanitize_text_field( $new_instance['title'] );
		$instance['count']	= absint( $new_instance['count'] );

		// delete our transient
		$widget_id	= $_POST['widget-id'][0];
		delete_transient( 'agentpress_featured_listings_'.$widget_id );

		return $instance;
	}

	function form( $instance ) {

		$title	= isset( $instance['title'] )	? esc_attr( $instance['title'] )	: '';
		$count	= isset( $instance['count'] )	? absint( $instance['count'] )		: 10;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'apl' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'How many results should be returned?', 'apl' ); ?></label>
			<input class="small-text" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="text" value="<?php echo $count; ?>" />
		</p>

	<?php }
}