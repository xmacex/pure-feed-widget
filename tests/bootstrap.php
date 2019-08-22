<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Pure_Feed
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // WPCS: XSS ok.
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/pure-feed.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

const WSRESTFILE = 'tests/publications-via-wsrest.xml';
const WSRESTFILE_VANCOUVER = 'tests/publications-via-wsrest-rendering-vancouver.xml';
// See here for possible values orderBy.property for the research output type: https://pure.itu.dk/ws/rest/allowedorderbyproperties?type=dk.atira.pure.api.shared.model.researchoutput.ResearchOutput
const WSRESTURL = 'https://pure.itu.dk/ws/rest/publication?associatedOrganisationUuids.uuid=cf9b4e6a-e1ad-41e3-9475-7679abe7131b&window.size=5&associatedOrganisationAggregationStrategy=RecursiveContentValueAggregator&orderBy.property=publicationDate';
