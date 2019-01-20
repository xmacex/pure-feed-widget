<?php
/**
  Plugin Name: Pure feed widget
  Plugin URL: https://github.com/xmacex/pure-widget
  Description: Render feeds from Elsevier Pure systems
  Author: Mace Ojala
  Author URI: https://github.com/xmacex
*/

require_once('Publication.php');
require_once('PureWsRest.php');

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
            $this->datasource = new PureWsRest($instance['url']);
            echo "<ul class='references'>";
            foreach($this->datasource->publications as $pub)
            {
                // $pub = new Publication($item);
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
        $url = !empty($instance['url']) ? $instance['url'] : null;
        ?>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('title'));?>">
            <?php esc_attr_e('Title:', 'text_domain');?>
          </label>
          <input id="<?php echo esc_attr($this->get_field_id('title')); ?>"
            class="title"
            name="<?php echo esc_attr($this->get_field_name('title'));?>"
            type="text"
            value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
          <label for="<?php echo esc_attr($this->get_field_id('url'));?>">
            <?php esc_attr_e('Url:', 'text_domain');?>
          </label>
          <input id="<?php echo esc_attr($this->get_field_id('url')); ?>"
            class="url"
            name="<?php echo esc_attr($this->get_field_name('url'));?>"
            type="text"
            value="<?php echo esc_attr($url); ?>">
          </p>
        <?php
    }

    // Save options
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : 'Latest publications';
        $instance['url'] = (!empty($new_instance['url'])) ? strip_tags($new_instance['url']) : null;
        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('Pure_Widget');
});
