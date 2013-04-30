<?php
/**
 Plugin Name: Sortable Sticky Posts
 Description: Adds drag and drop Sticky Post sorting to the Settings > Reading Page. WordPress likes to store Stickies in order they were stickied. I don't like that.
 Author: Aaron Brazell
 Author URI: htt://technosailor.com
 License: GPLv2
 License URI:
 Version: 1.0
 */

class WP_Sortable_Stickies {

	public function __construct() {
		$this->hooks();
	}

	public function hooks() {
		add_action( 'admin_init', array( $this, 'stickies_options' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enq_js' ) );
		add_action( 'admin_head', array( $this, 'admin_css' ) );
		add_action( 'admin_head', array( $this, 'admin_js' ) );
		add_action( 'wp_ajax_dr-sort-stickies', array( $this, 'dr_sort_stickies' ) );
		#add_action( 'pre_get_posts', array( $this, 'modify_query' ) );
	}

	public function admin_enq_js() {
		if( !is_admin() )
			return false;

		wp_enqueue_script( 'jquery-ui-draggable' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	public function admin_js() {
		$ajaxnonce = wp_create_nonce('dr-sort-stickies');
		?>
		<script>
		jQuery(document).ready(function(){
			jQuery('#dr-sticky-list').sortable(
				{
					axis			: 'y',
					containment		: 'parent',
					grid			: [0, 25],
					cursor			: 'move',
					items			: '> li',
					opacity			: '0.7',
					revert 			: true,
					scroll 			: true,
					cursorAt 		: 'bottom',
					stop 			: function(event,ui) {
						var items = jQuery('#dr-sticky-list li');
						var stickies = []
						jQuery.each(items, function(k,v) {
							stickies.push(jQuery(v).data('postid'));
						});
						console.debug(stickies);
						jQuery.post( ajaxurl,
							{
								action : 'dr-sort-stickies',
								_ajax_nonce : '<?php echo $ajaxnonce ?>',
								stickies: stickies
							},
							function(data) {
								console.debug(data);
							}
						);
					}	
				}
			);

		});
		</script>
		<?php
	}

	public function admin_css() {
		?>
		<style>
		#dr-sticky-list {}
		#dr-sticky-list li { 
			padding:7px; 
			line-height: 30px; 
			background: #eee;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			cursor: move;
		}
		a#dr-sticky-order-reset {
			height: 30px; 
			background: maroon;
			line-height: 30px;
			padding: 7px;
			font-weight:bold;
			color: #fff;
			cursor: pointer;
		}
		</style>
		<?php
	}

	public function modify_query( $query) {
		$query->set( 'orderby', 'post__in' );
		$query->set( 'post__in', get_option('sticky_posts') );
	}

	public function dr_sort_stickies() {
		check_ajax_referer( 'dr-sort-stickies', '_ajax_nonce' );
		if( get_option( 'sticky_posts' ) )
			update_option( 'sticky_posts', $_POST['stickies'] );
		else
			add_option( 'sticky_posts', $_POST['stickies'] );
		exit;
	}

	public function stickies_options() {
		add_settings_section( 'dr-stickies', 'Re-order Stickies', array( $this, 'stickies' ), 'reading' );
		add_settings_field( 'dr-stickies-order', 'Sticky Order', array( $this, 'sticky_order_input' ), 'reading', 'dr-stickies');
	}

	public function stickies() {
		return;
	}

	public function sticky_order_input() {
		$stickies = get_option( 'sticky_posts' );
		if( count( $stickies ) < 1 ) {
			echo '<p>There is no spoon... er stickies!!!</p>';
			return false;
		}

		$sticky_query = new WP_Query( array( 'post_per_page' => count( $stickies ), 'orderby' => 'post__in', 'post__in' => $stickies ) );
		if( $sticky_query->have_posts() ) :
			echo '<div id="dr-sticky-sorter-container">';
			echo '<ul id="dr-sticky-list">';
			while( $sticky_query->have_posts() ) : $sticky_query->the_post();
				echo '<li data-postid="' . get_the_ID() . '">' . get_the_title() . ' (Pub: ' . get_the_date() . ')</li>';
			endwhile;
			echo '</ul>';
			echo '</div>';
		endif;

	}
}
$wp_sortable_stickies = new WP_Sortable_Stickies;