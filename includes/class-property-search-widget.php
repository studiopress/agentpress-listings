<?php
/**
 * This widget presents a search widget which uses listings' taxonomy for search fields.
 *
 * @package AgentPress
 * @since 2.0
 * @author Ron Rennick
 */
add_action('widgets_init', 'register_agentpress_listings_search_widget');
function register_agentpress_listings_search_widget() {
	register_widget('AgentPress_Listings_Search_Widget');
}

class AgentPress_Listings_Search_Widget extends WP_Widget {

	function AgentPress_Listings_Search_Widget() {
		$widget_ops = array( 'classname' => 'property-search', 'description' => __( 'Display property search dropdown', 'apl' ) );
		$control_ops = array( 'width' => 200, 'height' => 250, 'id_base' => 'property-search' );
		$this->WP_Widget( 'property-search', __( 'AgentPress - Listing Search', 'apl' ), $widget_ops, $control_ops );
	}

	function widget($args, $instance) {
		global $_agentpress_taxonomies;
		
		$listings_taxonomies = $_agentpress_taxonomies->get_taxonomies();
		
		extract($args);		
		echo $before_widget;
		
		if ($instance['title']) echo $before_title . apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) . $after_title;
		
		echo '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" ><input type="hidden" value="" name="s" /><input type="hidden" value="listing" name="post_type" />';
	
		foreach( $listings_taxonomies as $tax => $data ) {
			if( !isset( $instance[$tax] ) || !$instance[$tax] )
				continue;

			$terms = get_terms( $tax, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 100, 'hierarchical' => false ) );
			if( empty( $terms ) )
				continue;

			$current = !empty( $wp_query->query_vars[$tax] ) ? $wp_query->query_vars[$tax] : '';				
			echo "<select name='$tax' id='$tax' class='agentpress-taxonomy'>\n\t";
			echo '<option value="" ' . selected( $current == '', true, false ) . ">{$data['labels']['name']}</option>\n";
			foreach( $terms as $term ) 
				echo "\t<option value='{$term->slug}' " . selected( $current, $term->slug, false ) . ">{$term->name}</option>\n";
							
			echo '</select><br />';
		}
	
		echo '<input type="submit" id="searchsubmit" class="searchsubmit" value="'. esc_attr__( 'Search Properties', 'apl' ) .'" />
		<div class="clear"></div>
	</div>
	</form>';
		
		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	function form($instance) {
		global $_agentpress_taxonomies;
		
		$listings_taxonomies = $_agentpress_taxonomies->get_taxonomies();
		$new_widget = empty( $instance );
		
		printf( '<p><label for="%s">%s</label><input type="text" id="%s" name="%s" value="%s" style="%s" /></p>', $this->get_field_id('title'), __( 'Title:', 'apl' ), $this->get_field_id('title'), $this->get_field_name('title'), esc_attr( $instance['title'] ), 'width: 95%;' );
		?>
		<h5><?php _e( 'Include these taxonomies in the search widget', 'apl' ); ?></h5>
		<?php
		foreach( $listings_taxonomies as $tax => $data ) {
			$terms = get_terms( $tax );
			if( empty( $terms ) )
				continue;
		?>
		<p><label><input id="<?php echo $this->get_field_id( $tax ); ?>" type="checkbox" name="<?php echo $this->get_field_name( $tax ); ?>" value="1" <?php checked( 1, $instance[$tax] || $new_widget ); ?>/> <?php echo $data['labels']['name']; ?></label></p>
		<?php
		}
	}
}
?>