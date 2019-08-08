<?php
/**
 * Plugin Name: Pure feed widget
 * Plugin URL: https://github.com/xmacex/pure-widget
 * Description: Render feeds from Elsevier Pure systems
 * Version: 0.0.1
 * Author: Mace Ojala
 * Author URI: https://github.com/xmacex
 */

require_once('PureWsRestRendering.php');

class Pure_Widget extends WP_Widget
{
    // Constructor
    public function __construct() {
        $widget_ops = array(
            'classname' => 'pure_widget',
            'description' => 'Pure feed widget'
        );
        parent::__construct('pure_widget', 'Pure widget', $widget_ops);

        $this->datasource = NULL;
    }

    // Widget output
    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        echo $args['before_title'];
        echo apply_filters('widget_title', !empty($instance['title']) ? $instance['title'] : "Latest publications");
        echo $args['after_title'];

        if (!empty($instance['url'])) {
            // $xml = simplexml_load_file($instance['url']);
            $this->datasource = new PureWsRestRendering(
                $instance['url'],
                $instance['org'],
                NULL,
                $noitems=$instance['noitems'],
                $orderby='publicationDate',
                $rendering=$instance['rendering'],
                $orgagg='RecursiveContentValueAggregator');

            echo "<ul class='references'>";
            foreach($this->datasource->publications as $pub)
            {
                print($pub->toHtml() . PHP_EOL);
                print(PHP_EOL);
            }
            echo '</ul>';
        }
        echo $args['after_widget'];
    }

    // Options form
    // Oh dear this is a mess for now
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : esc_html__('', 'text_domain');
        $url = !empty($instance['url']) ? $instance['url'] : NULL;
        $org = !empty($instance['org']) ? $instance['org'] : NULL;
        $apikey = !empty($instance['apikey']) ? $instance['apikey'] : NULL;
        $noitems = !empty($instance['noitems']) ? $instance['noitems'] : 5;
        $rendering = !empty($instance['rendering']) ? $instance['rendering'] : NULL;
?>
    <!-- Widget title -->
    <p>
        <label for="<?php echo esc_attr($this->get_field_id('title'));?>">
            <?php esc_attr_e('Title:');?>
        </label>
        <input id="<?php echo esc_attr($this->get_field_id('title')); ?>"
               class="title"
               name="<?php echo esc_attr($this->get_field_name('title'));?>"
               type="text"
               value="<?php echo isset($title) ? esc_attr($title) : NULL; ?>">
    </p>
    <!-- API endpoint URL -->
    <p>
        <label for="<?php echo esc_attr($this->get_field_id('url'));?>">
            <?php esc_attr_e('API URL:');?>
        </label>
        <input id="<?php echo esc_attr($this->get_field_id('url')); ?>"
               class="url"
               name="<?php echo esc_attr($this->get_field_name('url'));?>"
               type="text"
	       required
	       pattern="http.*"
               value="<?php echo isset($url) ? esc_attr($url) : NULL; ?>">
    </p>
    <!-- API endpoint key -->
    <p>
        <label for="<?php echo esc_attr($this->get_field_id('apikey'));?>">
            <?php esc_attr_e('API key:');?>
        </label>
        <input id="<?php echo esc_attr($this->get_field_id('apikey')); ?>"
               class="apikey"
               name="<?php echo esc_attr($this->get_field_name('apikey'));?>"
               type="text"
	       required
               value="<?php echo isset($url) ? esc_attr($apikey) : NULL; ?>">
    </p>

    <!-- Organization UUID -->
    <p>
        <label for="<?php echo esc_attr($this->get_field_id('org'));?>">
            <?php esc_attr_e('Organization UUID:');?>
        </label>
        <input id="<?php echo esc_attr($this->get_field_id('org')); ?>"
               class="org"
               name="<?php echo esc_attr($this->get_field_name('org'));?>"
               type="text"
	       required
               value="<?php echo isset($org) ? esc_attr($org) : NULL; ?>">
    </p>
    <!-- Number of items to retrieve -->
    <p>
        <label for="<?php echo esc_attr($this->get_field_id('noitems'));?>">
            <?php esc_attr_e('Number of items:');?>
        </label>
        <input id="<?php echo esc_attr($this->get_field_id('noitems')); ?>"
               class="noitems"
               name="<?php echo esc_attr($this->get_field_name('noitems'));?>"
               type="number"
               min="1"
               max="50"
               value="<?php echo isset($noitems) ? esc_attr($noitems) : 5; ?>">
    </p>
    <!-- Rendering style, available style retrieved from endpoint -->
    <p>
        <label for="<?php echo esc_attr($this->get_field_id('rendering'));?>">
            <?php esc_attr_e('Rendering:');?>
        </label>
        <select id="<?php echo esc_attr($this->get_field_id('rendering')); ?>"
		class="rendering"
		name="<?php echo esc_attr($this->get_field_name('rendering'));?>">
	    <!--This would better be AJAX I guess. -->
	    <?php
	    $formats_url = $url . "/research-outputs-meta/renderings?apiKey=" . $apikey;
	    $renderings = simplexml_load_file($formats_url);

	    if($renderings) {
		foreach($renderings->xpath('//renderings/rendering') as $rendering_option) {
		    echo "<option value=$rendering_option " . (($rendering_option == esc_attr($rendering)) ? "selected" : NULL) . ">$rendering_option</option>";
		}
	    } else {
		echo "<p>Fetching publications failed</p>";
	    }
	    ?>
	</select>
    </p>
<?php
}

// Save options
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : 'Latest publications';
        $instance['url'] = (!empty($new_instance['url'])) ? strip_tags($new_instance['url']) : null;
        $instance['apikey'] = (!empty($new_instance['apikey'])) ? strip_tags($new_instance['apikey']) : null;
        $instance['org'] = (!empty($new_instance['org'])) ? strip_tags($new_instance['org']) : null;
        $instance['noitems'] = (!empty($new_instance['noitems'])) ? strip_tags($new_instance['noitems']) : 5;
        $instance['rendering'] = (!empty($new_instance['rendering'])) ? strip_tags($new_instance['rendering']) : "vancouver";
        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('Pure_Widget');
});
