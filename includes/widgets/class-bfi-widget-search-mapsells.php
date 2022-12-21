<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Product Search Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     8.1.0
 * @extends  WP_Widget
 */
if ( ! class_exists( 'BFI_Widget_Search_MapSells' ) ) {
	class BFI_Widget_Search_MapSells extends WP_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'bfi-widget_search_mapsells';
			$this->widget_description = __( 'A box for search on Maps.', 'bfi' ) . " " . __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' );
			$this->widget_id          = 'bookingfor_search_mapsells';
			$this->widget_name        = __( 'BookingFor Search MapSells', 'bfi' ) . " - " . __( 'DEPRECATED', 'bfi' ) ;
			$this->widget_sidebar    = 'bfisidebar MapSell';
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Title', 'bfi' )
				)
			);
			$widget_ops = array(
				'classname'   => $this->widget_cssclass,
				'description' => $this->widget_description,
				'sidebar' => $this->widget_sidebar,
			);

			parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

	//		parent::__construct();
		}
			// widget form creation
			function form($instance) {
				$showdirection = ( ! empty( $instance['showdirection'] ) ) ? esc_attr($instance['showdirection']) : '1';
		
				// aggiunta id del widget nel titolo
				if ($this->number=="__i__"){
				}  else {
					$instance[ 'title' ] = $this->number ;
				}

			?>
				<p class="bfi-deprecated">
					<?php _e('These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi') ?>
				</p>
				<p class="">
					<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showdirection'); ?>" value="1" <?php  echo ($showdirection=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Displays horizontally', 'bfi'); ?></label>
				</p>
			<?php 
			}
			// update widget
			function update($new_instance, $old_instance) {
				$instance = $old_instance;
				// Fields
				$instance['showdirection'] =! empty( $new_instance[ 'showdirection' ] ) ? 1 : 0;
				return $instance;
			}
		/**
		 * Output widget.
		 *
		 * @see WP_Widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
	//		$this->widget_start( $args, $instance );
			wp_enqueue_script('jquery-ui-core');
			extract( $args );
			// these are the widget options
			
			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : "";
			$title = apply_filters('widget_title', $title );
			$args["title"] =  $title;
			$args["instance"] =  $instance;

			bfi_get_template("widgets/booking-searchmapsells.php",$args);	
	//		$this->widget_end( $args );
		}
	}
}