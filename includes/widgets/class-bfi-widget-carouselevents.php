<?php
/**
 * Product Search Widget.
 *
 * @author   BookingFor
 * @category Widgets
 * @package  BookingFor/Widgets
 * @version     2.0.5
 * @extends  WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'BFI_Widget_CarouselEvents' ) ) :
class BFI_Widget_CarouselEvents extends WP_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'bfi-widget_carouselevents';
		$this->widget_description = __( 'A Carousel list of Events.', 'bfi' );
		$this->widget_id          = 'bookingfor_carouselevents';
		$this->widget_name        = __( 'BookingFor Carousel Events', 'bfi' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'bfi' )
			)
			,'tags'  => array(
				'type'  => 'checkbox',
				'std'   => '0',
				'label' => __( 'Tags', 'bfi' )
			)
			,'itemspage'  => array(
				'type'  => 'list',
				'std'   => '4',
				'label' => __( 'Items per page', 'bfi' )
			)
			,'theme'  => array(
				'type'  => 'list',
				'std'   => '0',
				'label' => __( 'Theme', 'bfi' )
			)
			,'maxitems'  => array(
				'type'  => 'number',
				'std'   => '4',
				'label' => __( 'Max items', 'bfi' )
			)
			,'descmaxchars'  => array(
				'type'  => 'number',
				'std'   => '300',
				'label' => __( 'Max description characters', 'bfi' )
			)
		);

		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

	}


// widget form creation
function form($instance) {

	// Check values
	$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : '';
	$tags = ( ! empty( $instance['tags'] ) ) ? $instance['tags'] : array();
	$itemspage = ( ! empty( $instance['itemspage'] ) ) ? $instance['itemspage'] : 4;
	$theme = ( ! empty( $instance['theme'] ) ) ? $instance['theme'] : 1;
	$maxitems = ( ! empty( $instance['maxitems'] ) ) ? $instance['maxitems'] : 10;
	$descmaxchars = ( ! empty( $instance['descmaxchars'] ) ) ? $instance['descmaxchars'] : 300;

	$language = $GLOBALS['bfi_lang'];
				
	$tagsList = BFCHelper::getTags($language,pow(2, 5));
	//sort by name
	if (isset($tagsList) && !empty($tagsList)) {
		usort($tagsList, function($a,$b) {
			return BFCHelper::orderBy($a, $b, 'Name', '');
		});
	}
	$options = array();
	if (!empty($tagsList))
	{
		foreach($tagsList as $tag)
		{
			$options[$tag->TagId] = $tag->Name;
		}
	}
	?>
	<p>
		<label class="bfi-select2" for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'wp_widget_plugin'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	</p>
	<p>
			<label for="<?php echo $this->get_field_id('tags'); ?>"><?php _e('Tags', 'bfi'); ?>
			<?php 
				printf(
					'<select multiple="multiple" name="%s[]" id="%s" class="widefat select2full">',
					$this->get_field_name('tags'),
					$this->get_field_id('tags')
				);
				foreach ($options as $key => $value) {
					printf(
						'<option value="%s" %s style="margin-bottom:3px;">%s</option>',
						$key,
						in_array( $key, $tags) ? 'selected="selected"' : '',
						$value
					);
				}
				echo '</select>';
				?>
			</label>
	</p>
	theme
	<p>
	<?php _e('Theme', 'bfi') ?></label><br />
		<select name="<?php echo $this->get_field_name('theme'); ?>" id="<?php echo $this->get_field_name('theme'); ?>">
			<option value="1" <?php selected( $theme, '1' ); ?> >Simple</option>
			<option value="0" <?php selected( $theme, '0' ); ?> >Complete</option>
		</select>
	</p>
	<p>
	<?php _e('Items per page', 'bfi') ?></label><br />
		<select name="<?php echo $this->get_field_name('itemspage'); ?>" id="<?php echo $this->get_field_name('itemspage'); ?>">
			<option value="1" <?php selected( $itemspage, '1' ); ?> >1</option>
			<option value="3" <?php selected( $itemspage, '3' ); ?> >3</option>
			<option value="4" <?php selected( $itemspage, '4' ); ?> >4</option>
			<option value="6" <?php selected( $itemspage, '6' ); ?> >6</option>
		</select>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('maxitems'); ?>"><?php _e('Max items', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('maxitems'); ?>" name="<?php echo $this->get_field_name('maxitems'); ?>" type="number" value="<?php echo $maxitems; ?>" request />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('descmaxchars'); ?>"><?php _e('Max description characters', 'bfi'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('descmaxchars'); ?>" name="<?php echo $this->get_field_name('descmaxchars'); ?>" type="number" value="<?php echo $descmaxchars; ?>" request />
	</p>
	<?php
	}

	
	// update widget
	function update($new_instance, $old_instance) {
		  $instance = $old_instance;
		  // Fields
		  $instance['title'] = strip_tags($new_instance['title']);
		  $instance['tags'] = ! empty( $new_instance[ 'tags' ] ) ? esc_sql( $new_instance['tags'] ) : "";
		  $instance['itemspage'] = ! empty( $new_instance[ 'itemspage' ] ) ? esc_sql( $new_instance['itemspage'] ) : 4;
		  $instance['theme'] = ! empty( $new_instance[ 'theme' ] ) ? esc_sql( $new_instance['theme'] ) : 0;
		  $instance['maxitems'] = !empty($new_instance['maxitems'])? $new_instance['maxitems'] : 10;
		  $instance['descmaxchars'] =!empty($new_instance['descmaxchars'])? $new_instance['descmaxchars'] : 300;
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
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$args["title"] =  $title;
		$args["instance"] =  $instance;
		bfi_get_template("widgets/carouselevents.php",$args);	
	}
				

}
endif;