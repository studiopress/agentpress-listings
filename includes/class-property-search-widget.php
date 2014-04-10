<?php
/**
 * This widget presents a search widget which uses listings' taxonomy for search fields.
 *
 * @package AgentPress
 * @since 2.0
 * @author Ron Rennick
 */
class AgentPress_Listings_Search_Widget extends WP_Widget {

	function AgentPress_Listings_Search_Widget() {
		$widget_ops = array( 'classname' => 'property-search', 'description' => __( 'Display property search dropdown', 'apl' ) );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'property-search' );
		$this->WP_Widget( 'property-search', __( 'AgentPress - Listing Search', 'apl' ), $widget_ops, $control_ops );
	}

	function tax_data( $name = false ) {

		global $_agentpress_taxonomies;

		$term_items = $_agentpress_taxonomies->get_taxonomies();

		if ( ! $term_items ) {
			return false;
		}

		// if a name variable is called, return just that one
		if ( $name && isset( $term_items[$name] ) ) {
			return $term_items[$name];
		}

		return $term_items;

	}

	function single_term_data( $term = false ) {

		if ( ! $term ) {
			return false;
		}

		if( false === get_transient( 'agentpress_property_search_'.$term ) ) :

			// set args
			$args	= array(
				'orderby'		=> 'count',
				'order'			=> 'DESC',
				'number'		=> 100,
				'hierarchical'	=> false
			);

			$terms	= get_terms( array( $term ), $args );

			if ( ! $terms ) {
				return false;
			}

			set_transient( 'agentpress_property_search_'.$term, $terms, 60*60*72 );

		endif;

		$terms	= get_transient( 'agentpress_property_search_'.$term );

		return $terms;

	}

	function widget( $args, $instance ) {

		extract( $args );

		// first check for stored terms and bail without
		if ( ! isset( $instance['terms'] ) || isset( $instance['terms'] ) && empty( $instance['terms'] ) ) {
			return;
		}

		$terms	= array_keys( $instance['terms'] );

		// set our button text with fallback
		$button	= isset( $instance['button'] ) && ! empty( $instance['button'] ) ? esc_html( $instance['button'] ) : __( 'Search Properties', 'apl' );

		// start widget display
		echo $before_widget;

		// check for title and run filter
		$title = empty( $instance['title'] ) ? '' : apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		// display title if present
		if ( ! empty( $title ) ) { echo $before_title . $title . $after_title; };

		// begin form build
		echo '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >';
			echo '<input type="hidden" value="" name="s" />';
			echo '<input type="hidden" value="listing" name="post_type" />';

			// loop through our stored terms
			foreach ( $terms as $term ) {

				// fetch data for the term
				$term_data	= $this->tax_data( $term );

				// fetch my terms
				$term_items	= $this->single_term_data( $term );

				if ( ! $term_items ) {
					continue;
				}

				$current	= ! empty( $wp_query->query_vars[$term] ) ? $wp_query->query_vars[$term] : '';

				echo '<select name="'.$term.'" id="'.$term.'" class="agentpress-taxonomy">';
					echo '<option value="" ' . selected( $current == '', true, false ) . '>'.$term_data['labels']['name'].'</option>';
					foreach( $term_items as $term_item ) {
						echo '<option value="'.$term_item->slug.'" '. selected( $current, $term_item->slug, false ) . ' ">'.$term_item->name.'</option>';
					}
				echo '</select>';
			}

			echo '<div class="clear"></div>';
			echo '<input type="submit" id="searchsubmit" class="searchsubmit" value="'. $button .'" />';

		echo '</form>';

		echo $after_widget;

	}


	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title']	= sanitize_text_field( $new_instance['title'] );
		$instance['button']	= sanitize_text_field( $new_instance['button'] );
		$instance['terms']	= (array) $new_instance['terms'];

		return $instance;
	}

	function form( $instance ) {

		$title	= isset( $instance['title'] )	? esc_attr( $instance['title'] )	: '';
		$button	= isset( $instance['button'] )	? esc_attr( $instance['button'] )	: __( 'Search Properties', 'apl' );
		$terms	= isset( $instance['terms'] )	? (array) $instance['terms']	: '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'apl' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>

		<?php
		$tax_data	= $this->tax_data();
		if ( $tax_data ) {
			echo '<h5>'. __( 'Include these taxonomies in the search widget', 'apl' ).'</h5>';
			foreach ( $tax_data as $tax_name => $tax_info ) {

				$item_check	= isset( $terms[$tax_name] ) ? $terms[$tax_name] : '';

				echo '<p>';
					echo '<input id="'. $this->get_field_id( $tax_name ).'" type="checkbox" name="'.$this->get_field_name( 'terms' ).'['.$tax_name.']" value="1" '.checked( 1, $item_check, false ).' />';
					echo '<label for="'. $this->get_field_id( $tax_name ).'">'. esc_attr( $tax_info['labels']['name'] ) . '</label>';
				echo '</p>';

			} // end taxonomy loop
		} // end taxonomy check
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'button' ); ?>"><?php _e( 'Button Text:', 'apl' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'button' ); ?>" name="<?php echo $this->get_field_name( 'button' ); ?>" type="text" value="<?php echo $button; ?>" />
		</p>

	<?php }

}