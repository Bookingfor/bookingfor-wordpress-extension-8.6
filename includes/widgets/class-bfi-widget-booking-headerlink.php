<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * BookingFor Cart Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     2.0.5
 * @extends  WP_Widget
 */
if ( ! class_exists( 'BFI_Widget_Headerlink' ) ) {
	class BFI_Widget_Headerlink extends WP_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'bfi-widget_headerlink';
			$this->widget_description = __("Display the link for  login, cart and change currency.", 'bfi' ) . " " . __( 'These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi' );
			$this->widget_id          = 'bookingfor_widget_headerlink';
			$this->widget_name        = __( 'BookingFor Header Link', 'bfi' ) . " - " . __( 'DEPRECATED', 'bfi' ) ;
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

		/**
		 * Output widget.
		 *
		 * @see WP_Widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {
			extract( $args );
			// these are the widget options
			$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : "";
			if ( is_admin() ) {
				return ;
			}
			$title = apply_filters('widget_title', $title );
			$args["title"] =  $title;
			$args["instance"] =  $instance;
			bfi_get_template("widgets/headerlinkwidgets.php",$args);	
		}

		// widget form creation
		function form($instance) {
			$showlanguages = ( ! empty( $instance['showlanguages'] ) ) ? esc_attr($instance['showlanguages']) : '0';
			$showcurrency = ( ! empty( $instance['showcurrency'] ) ) ? esc_attr($instance['showcurrency']) : '0';
			$showcart = ( ! empty( $instance['showcart'] ) ) ? esc_attr($instance['showcart']) : '0';
			$showlogin = ( ! empty( $instance['showlogin'] ) ) ? esc_attr($instance['showlogin']) : '0';
			$showfavorites= ( ! empty( $instance['showfavorites'] ) ) ? esc_attr($instance['showfavorites']) : '0';	
			$customclass="";

			$newcodeid = uniqid("newcode");
		?>
		<p class="bfi-deprecated">
			<?php _e('These features have been deprecated. This means they are no longer supported and will be removed in the next version', 'bfi') ?>
		</p>
		<p>
			aggiungere widget HTML con il seguente codice:
			<textarea class="autoheight" id="<?php echo $newcodeid ?>" style="width:100%; min-height: 150px;" oninput='this.style.height = "";this.style.height = this.scrollHeight + "px"'>
<div class="bfiwidgetcontainer <?php echo $customclass ?>">
	<div class="bookingforwidget bfiheader"
	 data-showlanguages="<?php echo (!empty($showlanguages)) ?"true":"false"; ?>"
	 data-showcurrency="<?php echo (!empty($showcurrency)) ?"true":"false"; ?>"
	 data-showcart="<?php echo (!empty($showcart)) ?"true":"false"; ?>"
	 data-showlogin = "<?php echo (!empty($showlogin)) ?"true":"false"; ?>"
	 data-showfavorites = "<?php echo (!empty($showfavorites)) ?"true":"false"; ?>"
	></div>	
</div>
			</textarea>
			<script type="text/javascript">
window.setTimeout( function() {
    
	jQuery("#<?php echo $newcodeid ?>").height( jQuery("#<?php echo $newcodeid ?>")[0].scrollHeight );
}, 1);	
jQuery("#<?php echo $newcodeid ?>").on( 'visibility', function() {
	window.setTimeout( function() {
    jQuery("#<?php echo $newcodeid ?>").height( jQuery("#<?php echo $newcodeid ?>")[0].scrollHeight );
	}, 100);
});
	</script>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo ($instance)?esc_attr($instance['title']):''; ?>" />
		</p>
			<p class="bookingoptions">
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showlanguages'); ?>" value="1" <?php  echo ($showlanguages=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show languages selector', 'bfi'); ?></label>
			</p>
			<p class="bookingoptions">
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showcurrency'); ?>" value="1" <?php  echo ($showcurrency=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show currency selector', 'bfi'); ?></label>
			</p>
			<p class="bookingoptions">
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showcart'); ?>" value="1" <?php  echo ($showcart=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show link to cart', 'bfi'); ?></label>
			</p>
			<p class="bookingoptions">
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showlogin'); ?>" value="1" <?php  echo ($showlogin=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show login as popup', 'bfi'); ?></label>
			</p>
			<p class="bookingoptions">
				<label class="checkbox"><input type="checkbox" name="<?php echo $this->get_field_name('showfavorites'); ?>" value="1" <?php  echo ($showfavorites=='1') ? 'checked="checked"' : ''; ?> /><?php _e('Show link to Travel planner', 'bfi'); ?></label>
			</p>
		
		<?php 
				
		}
		// update widget
		function update($new_instance, $old_instance) {

			  $instance = $old_instance;
			  // Fields
			  $instance['title'] = strip_tags($new_instance['title']);
			  $instance['showlanguages'] =! empty( $new_instance[ 'showlanguages' ] ) ? 1 : 0;
			  $instance['showcurrency'] =! empty( $new_instance[ 'showcurrency' ] ) ? 1 : 0;
			  $instance['showcart'] =! empty( $new_instance[ 'showcart' ] ) ? 1 : 0;
			  $instance['showlogin'] =! empty( $new_instance[ 'showlogin' ] ) ? 1 : 0;
			  $instance['showfavorites'] =! empty( $new_instance[ 'showfavorites' ] ) ? 1 : 0;
			 return $instance;
		}

	}
}