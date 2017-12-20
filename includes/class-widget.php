<?php
/**
 * Creative Commons Sharing Settings.
 *
 * @since   1.0
 * @package Trust_Indicators
 */

/**
 * Creative Commons Sharing Settings class.
 *
 * @since 1.0
 */
class Creative_Commons_Sharing_Widget extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'creative_commons_sharing',
			'description' => esc_html__( 'Creative Commons Sharing', 'creative-commons-sharing' ),
		);
		parent::__construct( 'creative_commons_sharing', 'Creative Commons Sharing', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * If this is not a single post, don't output the widget. It won't work outside single posts.
	 *
	 * @param array $args Sidebar arguments.
	 * @param array $instance This instance of the widget.
	 */
	public function widget( $args, $instance ) {
		if ( ! is_single() ) {
			return;
		}

		global $post;

		wp_enqueue_script( 'creative-commons-sharing-js', plugins_url( 'assets/widget.js', dirname( __FILE__ ) ), array( 'jquery' ), Creative_Commons_Sharing::VERSION, false );
		wp_enqueue_style( 'creative-commons-sharing-css', plugins_url( 'assets/widget.css', dirname( __FILE__ ) ), array(), Creative_Commons_Sharing::VERSION );
		add_action( 'wp_ajax_my_action', 'my_action' );
		add_action( 'wp_ajax_nopriv_my_action', 'my_action' );

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		echo sprintf(
			'<div id="creative-commons-share-modal" style="display:none;" data-postid="%s" data-pluginsdir="%s"></div>',
			esc_attr( $post->ID ),
			esc_attr( plugins_url() )
		);

		echo '<div class="license">';
			echo sprintf(
				'<p><button name="%1$s" id="cc-btn" class="creative-commons-button">%1$s</button></p>',
				esc_html__( 'Republish This Story', 'creative-commons-sharing' )
			);
			echo sprintf(
				'<p><a class="license" rel="license" target="_blank" href="http://creativecommons.org/licenses/by-nd/4.0/"><img alt="%s" style="border-width:0" src="https://i.creativecommons.org/l/by-nd/4.0/88x31.png" /></a></p>',
				esc_html__( 'Creative Commons License', 'creative-commons-sharing' )
			);
		echo '</div>';

		echo sprintf(
			'<div class="message">%s</div>',
			wpautop( esc_html( $instance['text'] ) )
		);

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options.
	 */
	public function form( $instance ) {
		echo sprintf( '<p><em>%s</em></p>', esc_html__( 'This widget will only display on single articles.', 'creative-commons-sharing' ) );
		$title = ! empty( $instance['title'] ) ? $instance['title'] : '';
		$text = ! empty( $instance['text'] ) ? $instance['text'] : esc_html__( 'Republish our articles for free, online or in print, under Creative Commons license.', 'text_domain' );
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p>
			<textarea class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'text' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'text' ) ); ?>" type="text" cols="30" rows="10"><?php echo esc_attr( $text ); ?></textarea>
		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options.
	 * @param array $old_instance The previous options.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? $new_instance['text'] : '';

		return $instance;
	}
}
