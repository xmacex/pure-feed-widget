<?php
/**
 * Plugin Name: Pure feed widget
 * Plugin URL: https://github.com/xmacex/pure-feed-widget
 * Description: Render content from Elsevier Pure systems.
 * Version: 0.1.2
 * Author: Mace Ojala
 * Author URI: https://github.com/xmacex
 * Licence: GNU GPLv3
 * Licence URL: https://www.gnu.org/licenses/gpl-3.0.html
 *
 * See here though for merging this WordPress docblock with phpdoc docblock https://developer.wordpress.org/plugins/plugin-basics/header-requirements/
 *
 * @package PureFeed
 */

namespace PureFeed;

require_once 'class-pure-widget.php';

add_action(
	'widgets_init',
	function() {
		register_widget( 'PureFeed\Pure_Widget' );
	}
);
