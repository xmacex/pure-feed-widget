<?php
/*
  Plugin Name: Pure widget
  Plugin URL: https://github.com/xmacex/pure-widget
  Description: Render Pure feeds
  Author: Mace Ojala
  Author URI: https://github.com/xmacex
*/

class Pure_Widget extends WP_Widget
{
    // Constructor
    public function __construct() {
        $widget_ops = array(
            'classname' => 'pure_widget',
            'description' => 'Pure feed plugin'
        );
        parent::__construct('pure_widget', 'Pure widget', $widget_ops);

        $feeds = array(
            'mad' => 'https://pure.itu.dk/portal/en/organisations/mad-art--design(cf9b4e6a-e1ad-41e3-9475-7679abe7131b)/publications.rss',
            'rosemary' => 'https://pure.itu.dk/portal/en/organisations/mad-art--design(cf9b4e6a-e1ad-41e3-9475-7679abe7131b)/publications.rss'
        );
        
        $feedurl = $feeds['mad'];
        $this->xml = simplexml_load_file($feedurl);
    }

    // Widget output
    public function widget($args, $instance)
    {
        echo $args['before_widget'];
        $feedurl = $instance['url'];
        $xml = simplexml_load_file($feedurl);
        // foreach($xml->channel->item as $item)
        foreach($xml->channel->item as $item)
        {
            $pub = new Publication($item);
            print($pub->toHtml() . PHP_EOL);
            print(PHP_EOL);
        }
        echo $args['after_widget'];
    }

    // Options form
    // Oh dear this is a mess for now
    public function form($instance) {
        $url = !empty($instance->url) ? $instance->url : esc_html__('Give Pure RSS URL', 'text_domain');
        ?>
        <div>
        <label for="<?php echo esc_attr($this->get_field_id('url'));?>">
        <?php esc_attr_e('Url:', 'text_domain');?>
        </label>
        <input class="url"
          id="<?php echo esc_attr($this->get_field_id('url')); ?>"
          name="<?php echo esc_attr($this->get_field_name('url'));?>"
          type="text"
          value="<?php echo esc_attr($url); ?>">
        </div>
        <?php
    }

    // Save options
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['url'] = (!empty($new_instance['url'])) ? strip_tags($new_instance['url']) : 'http://somedefaulturl';
        return $instance;
    }
}

class Publication
{
    public $authors = [];
    public $date;
    public $title;
    public $publication;

    public function __construct($elem)
    {
        $desc = new SimpleXMLElement($elem->description);
        foreach($desc->div[0]->a as $related)
        {
            if($related["rel"] == "Person")
            {
                $name = $related->span;
                array_push($this->authors, $name);
            }
        }
        $this->title = (string)$elem->title;
        $this->publication = (string)$desc->div[1]->div[1]->table->tbody->tr[1]->td;
        // $this->date = (string)$desc->div[0]->span[0];
        $this->date = strtotime($desc->div[0]->span[0]);
    }
    
    public function __toString()
    {
        return implode(", ", $this->authors ) . $this->title . $this->date . $this->publication;
    }

    private function authString()
    {
        return implode(", ", $this->authors);
    }

    private function year()
    {
        return date('Y', $this->date);
    }

    public function toHtml()
    {
        $output = "<li class='item'>";
        $output .= "<span class='authors'>" . $this->authString() . "</span>";
        $output .= ". ";
        // $output .= "<span class='date'>" . $this->date . "</span>";
        $output .= "<span class='date'>" . $this->year() . "</span>";
        $output .= ". ";
        $output .= "<span class='title'>" . $this->title . "</span>";
        $output .= ". ";
        $output .= "<span class='publication'>" . $this->publication . "</span>";
        $output .= "</li>";
        return $output;
    }
}

$feeds = array(
	'mad' => 'https://pure.itu.dk/portal/en/organisations/mad-art--design(cf9b4e6a-e1ad-41e3-9475-7679abe7131b)/publications.rss',
    'rosemary' => 'https://pure.itu.dk/portal/en/organisations/mad-art--design(cf9b4e6a-e1ad-41e3-9475-7679abe7131b)/publications.rss'
);

$feedurl = $feeds['mad'];
$xml = simplexml_load_file($feedurl);

add_action('widgets_init', function() {
    register_widget('Pure_Widget');
});