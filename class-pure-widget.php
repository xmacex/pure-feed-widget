<?php
/**
 * WordPress widget for fetching research publications from Pure.
 *
 * @package PureFeedWidget
 */

namespace PureFeedWidget;

require_once 'class-pure.php';

/**
 * A WordPress widget for listing data from an Elsevier Pure systems.
 */
class Pure_Widget extends \WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => 'pure_widget',
			'description' => 'Pure feed widget',
		);
		parent::__construct( 'pure_widget', 'Pure Feed widget', $widget_ops );

		$this->datasource = null;
	}

	/**
	 * Widget output.
	 *
	 * Prints nice HTML, or that's the idea.
	 *
	 * @param array $args     Stuff from WordPress.
	 * @param array $instance Widget configuration options.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];

		echo $args['before_title'];
		echo apply_filters( 'widget_title', ! empty( $instance['title'] ) ? $instance['title'] : 'Latest publications' );
		echo $args['after_title'];

		if ( ! empty( $instance['url'] ) ) {
			$this->datasource = new Pure( $instance['url'], $instance['apikey'] );

			echo "<ul class='references'>";
			$publications = $this->datasource->get_research_outputs( $org = $instance['org'], $size = $instance['size'], $rendering = $instance['rendering'] );
			foreach ( $publications as $pub ) {
				print( $pub->as_html() );
				print( PHP_EOL );
			}
			echo '</ul>';
		}
		echo $args['after_widget'];
	}

	/**
	 * Output widget options form for the WordPress admin interface.
	 *
	 * Oh dear this is a mess for now, and would benefit from templating.
	 *
	 * @param array $instance Widget configuration options.
	 * @return void
	 */
	public function form( $instance ) {
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( '', 'text_domain' );
		$url       = ! empty( $instance['url'] ) ? $instance['url'] : null;
		$org       = ! empty( $instance['org'] ) ? $instance['org'] : null;
		$apikey    = ! empty( $instance['apikey'] ) ? $instance['apikey'] : null;
		$size      = ! empty( $instance['size'] ) ? $instance['size'] : 5;
		$rendering = ! empty( $instance['rendering'] ) ? $instance['rendering'] : null;
		?>
	<!-- Widget title -->
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
			<?php esc_attr_e( 'Title:' );?>
		</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			   class="title"
			   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			   type="text"
			   value="<?php echo isset( $title ) ? esc_attr( $title ) : null; ?>">
	</p>
	<!-- API endpoint URL -->
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>">
			<?php esc_attr_e( 'API URL:' ); ?>
		</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'url' ) ); ?>"
			   class="url"
			   name="<?php echo esc_attr( $this->get_field_name( 'url' ) ); ?>"
			   type="text"
		       required
		       pattern="http.*"
			   value="<?php echo isset( $url ) ? esc_attr( $url ) : null; ?>">
	</p>
	<!-- API endpoint key -->
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'apikey' ) ); ?>">
			<?php esc_attr_e( 'API key:' ); ?>
		</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'apikey' ) ); ?>"
			   class="apikey"
			   name="<?php echo esc_attr( $this->get_field_name( 'apikey' ) ); ?>"
			   type="text"
			   required
			   value="<?php echo isset( $url ) ? esc_attr( $apikey ) : null; ?>">
	</p>

	<!-- Organization UUID -->
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'org' ) ); ?>">
			<?php esc_attr_e( 'Organization UUID:' ); ?>
		</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'org' ) ); ?>"
			   class="org"
			   name="<?php echo esc_attr( $this->get_field_name( 'org' ) ); ?>"
			   type="text"
		       required
			   value="<?php echo isset( $org ) ? esc_attr( $org ) : null; ?>">
	</p>
	<!-- Number of items to retrieve -->
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'sizes' ) ); ?>">
			<?php esc_attr_e( 'Number of items:' ); ?>
		</label>
		<input id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"
			   class="size"
			   name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>"
			   type="number"
			   min="1"
			   max="50"
			   value="<?php echo isset( $size ) ? esc_attr( $size ) : 5; ?>">
	</p>
	<!-- Rendering style, available style retrieved from endpoint -->
	<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'rendering' ) ); ?>">
			<?php esc_attr_e( 'Style:' ); ?>
		</label>
		<select id="<?php echo esc_attr( $this->get_field_id( 'rendering' ) ); ?>"
			class="rendering"
			name="<?php echo esc_attr( $this->get_field_name( 'rendering' ) ); ?>">
			<!--This would better be AJAX I guess. -->
        <?php
		$formats_url = $url . '/research-outputs-meta/renderings?apiKey=' . $apikey;
		$renderings  = simplexml_load_file( $formats_url );

		if ( $renderings ) {
			foreach ( $renderings->xpath( '//renderings/rendering' ) as $rendering_option ) {
				echo '<option value=' . $rendering_option . ( ( $rendering_option == esc_attr( $rendering ) ) ? ' selected' : null ) . '>' . $rendering_option . '</option>';
			}
		} else {
			echo '<p>Fetching styles failed</p>';
		}
		?>
		</select>
		</p>
		<?php
	}

	/**
	 * Save widget options.
	 *
	 * @param array $new_instance New widget configuration options.
	 * @param array $old_instance Old widget configuration options.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance              = array();
		$instance['title']     = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : 'Latest publications';
		$instance['url']       = ( ! empty( $new_instance['url'] ) ) ? strip_tags( $new_instance['url'] ) : null;
		$instance['apikey']    = ( ! empty( $new_instance['apikey'] ) ) ? strip_tags( $new_instance['apikey'] ) : null;
		$instance['org']       = ( ! empty( $new_instance['org'] ) ) ? strip_tags( $new_instance['org'] ) : null;
		$instance['size']      = ( ! empty( $new_instance['size'] ) ) ? strip_tags( $new_instance['size'] ) : 5;
		$instance['rendering'] = ( ! empty( $new_instance['rendering'] ) ) ? strip_tags( $new_instance['rendering'] ) : 'vancouver';
		return $instance;
	}
}
