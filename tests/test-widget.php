<?php
/**
 * @package Pure_Feed
 */

class Test_Pure_Widget extends WP_UnitTestCase
{
    function test_construct_should_succeed()
    {
        $w = new Pure_Widget();
        $this->assertEquals("pure_widget", $w->id_base);
        $this->assertEquals("Pure widget", $w->name);
    }

    function test_without_url_should_print_just_header()
    {
        $w = new Pure_Widget();
        $args = array(
            'before_widget' => '<div>',
            'after_widget' => '</div>',
            'before_title' => '<h2>',
            'after_title' => '</h2>'
        );
        $instance = get_option($w->option_name)['_multiwidget'][0];

        ob_start();
        $w->widget($args, $instance);
        $output = ob_get_clean();

        $this->assertEquals("<div><h2>Latest publications</h2></div>", $output);
    }

    function test_with_url_should_retrieve_fetch_and_print_data()
    {
        $w = new Pure_Widget();
        $args = array(
            'before_widget' => '<div>',
            'after_widget' => '</div>',
            'before_title' => '<h2>',
            'after_title' => '</h2>'
        );
        $instance = get_option($w->option_name)['_multiwidget'][0];
        $instance['url'] = 'https://pure.itu.dk/ws/rest/publication?associatedOrganisationUuids.uuid=cf9b4e6a-e1ad-41e3-9475-7679abe7131b&window.size=5&associatedOrganisationAggregationStrategy=RecursiveContentValueAggregator';

        ob_start();
        $w->widget($args, $instance);
        $output = ob_get_clean();

        $this->assertContains("<div><h2>Latest publications</h2><ul class='references'><li class='item'><div class", $output);
    }

    function test_with_unretrievable_url_should_raise_error()
    {
        $w = new Pure_Widget();
        $args = array(
            'before_widget' => '<div class="widget">',
            'after_widget' => '</div>',
            'before_title' => '<h2>',
            'after_title' => '</h2>'
        );
        $instance = array('title' => 'Default title here', 'url' => 'https://kittens');

        // PHPUnit converts errors to exceptions https://phpunit.readthedocs.io/en/7.4/writing-tests-for-phpunit.html#testing-php-errors
        $this->expectException(PHPUnit\Framework\Error\Error::class);
        $this->expectExceptionMessage("simplexml_load_file(): php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known");
        
        $w->widget($args, $instance);
    }
}
