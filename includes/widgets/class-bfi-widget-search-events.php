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
 * @version     2.0.5
 * @extends  WP_Widget
 */
if ( ! class_exists( 'BFI_Widget_Search_Events' ) ) :
class BFI_Widget_Search_Events extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-widget_search_events';
		$this->widget_description = __( 'A Search box for Events.', 'bfi' ) . " " . __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' );
		$this->widget_id          = 'bookingfor_search_events';
		$this->widget_name        = __( 'BookingFor Search Events', 'bfi' ) . " - " . __( 'DEPRECATED', 'bfi' ) ;
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'bfi' )
			)
		);
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

//		parent::__construct();
	}
		// widget form creation
		function form($instance) {
?>
		<p class="bfi-deprecated">
			<?php _e('These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi') ?>
		</p>

<?php 

		}
		// update widget
		function update($new_instance, $old_instance) {
			$instance = $old_instance;
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
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_script('jquery-ui-core');
		extract( $args );
		// these are the widget options
        $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : "";
		$title = apply_filters('widget_title', $title );
		$args["title"] =  $title;
		$args["instance"] =  $instance;
		bfi_get_template("widgets/booking-searchevents.php",$args);	
	}
}
endif;