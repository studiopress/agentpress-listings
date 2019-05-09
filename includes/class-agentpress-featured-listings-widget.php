<?php
/**
 * AgentPress Featured Listing Widget.
 *
 * @package agentpress-listings
 */

/**
 * This widget presents loop content, based on your input, specifically for the homepage.
 *
 * @package AgentPress
 * @since 2.0
 * @author Nathan Rice
 */
class AgentPress_Featured_Listings_Widget extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'featured-listings',
			'description' => __( 'Display grid-style featured listings', 'agentpress-listings' ),
		);

		$control_ops = array(
			'width'  => 300,
			'height' => 350,
		);

		parent::__construct( 'featured-listings', __( 'AgentPress - Featured Listings', 'agentpress-listings' ), $widget_ops, $control_ops );
	}

	/**
	 * Widget function.
	 *
	 * @param  array $args      Arguments.
	 * @param  array $instance  Instance.
	 */
	public function widget( $args, $instance ) {

		// Defaults.
		$instance = wp_parse_args(
			$instance,
			array(
				'title'          => '',
				'posts_per_page' => 10,
			)
		);

		$before_widget = $args['before_widget'];
		$after_widget  = $args['after_widget'];
		$before_title  = $args['before_title'];
		$after_title   = $args['after_title'];

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $before_widget;

		if ( ! empty( $instance['title'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		}

			$toggle = ''; /** For left/right class. */

			$query_args = array(
				'post_type'      => 'listing',
				'posts_per_page' => $instance['posts_per_page'],
				'paged'          => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
			);

			$query = new WP_Query( $query_args );

			if ( $query->have_posts() ) :
				while ( $query->have_posts() ) :
					$query->the_post();

					// Initialze the $loop variable.
					$loop = '';

					// Pull all the listing information.
					$custom_text = genesis_get_custom_field( '_listing_text' );
					$price       = genesis_get_custom_field( '_listing_price' );
					$address     = genesis_get_custom_field( '_listing_address' );
					$city        = genesis_get_custom_field( '_listing_city' );
					$state       = genesis_get_custom_field( '_listing_state' );
					$zip         = genesis_get_custom_field( '_listing_zip' );

					$loop .= sprintf( '<a href="%s">%s</a>', get_permalink(), genesis_get_image( array( 'size' => 'properties' ) ) );

					if ( $price ) {
						$loop .= sprintf( '<span class="listing-price">%s</span>', $price );
					}

					if ( strlen( $custom_text ) ) {
						$loop .= sprintf( '<span class="listing-text">%s</span>', esc_html( $custom_text ) );
					}

					if ( $address ) {
						$loop .= sprintf( '<span class="listing-address">%s</span>', $address );
					}

					if ( $city || $state || $zip ) {

						// Count number of completed fields.
						$pass = count( array_filter( array( $city, $state, $zip ) ) );

						/**
						 * If only 1 field filled out, no comma.
						 * If city filled out, comma after city.
						 * Otherwise, comma after state.
						 */
						if ( 1 === $pass ) {
							$city_state_zip = $city . $state . $zip;
						} elseif ( $city ) {
							$city_state_zip = $city . ', ' . $state . ' ' . $zip;
						} else {
							$city_state_zip = $city . ' ' . $state . ', ' . $zip;
						}

						$loop .= sprintf( '<span class="listing-city-state-zip">%s</span>', trim( $city_state_zip ) );

					}

					$loop .= sprintf( '<a href="%s" class="more-link">%s</a>', get_permalink(), __( 'View Listing', 'agentpress-listings' ) );

					$toggle = ( 'left' === $toggle ) ? 'right' : 'left';

					// Wrap in post class div, and output.
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					printf( '<div class="%s"><div class="widget-wrap"><div class="listing-wrap">%s</div></div></div>', esc_attr( join( ' ', get_post_class( $toggle ) ) ), apply_filters( 'agentpress_featured_listings_widget_loop', $loop ) );

			endwhile;
		endif;

			wp_reset_postdata();

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $after_widget;

	}

	/**
	 * Update function.
	 *
	 * @param  array $new_instance New instance.
	 * @param  array $old_instance Old Instance.
	 * @return array               New instance.
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	/**
	 * Form function.
	 *
	 * @param  array $instance Instance.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			$instance,
			array(
				'title'          => '',
				'posts_per_page' => 10,
			)
		);

		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', esc_attr( $this->get_field_id( 'title' ) ), esc_html__( 'Title:', 'agentpress-listings' ), esc_attr( $this->get_field_id( 'title' ) ), esc_attr( $this->get_field_name( 'title' ) ), esc_attr( $instance['title'] ), 'width: 95%;' );

		printf( '<p>%s <input type="text" name="%s" value="%s" size="3" /></p>', esc_html__( 'How many results should be returned?', 'agentpress-listings' ), esc_attr( $this->get_field_name( 'posts_per_page' ) ), esc_attr( $instance['posts_per_page'] ) );

	}
}
