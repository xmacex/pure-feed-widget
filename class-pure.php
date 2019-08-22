<?php
/**
 * An abstraction over Pure, to simulate the data source
 *
 * @package PureFeed;
 */

namespace PureFeed;

require_once 'class-publication.php';

use SimpleXMLElement;
use function wp_remote_post;
use function wp_remote_retrieve_body;

/**
 * A Pure API representation.
 */
class Pure {
	/**
	 * Constructs a data source.
	 *
	 * @param string $url     URL to grab data from.
	 * @param string $apikey  Api key.
	 */
	public function __construct( string $url, string $apikey ) {
		$this->url    = $url;
		$this->apikey = $apikey;
	}

	/**
	 * Get research output from the Pure.
	 *
	 * @param string $org        Organization to filter.
	 * @param int    $size       Number of items to get.
	 * @param string $rendering  Rendering type.
	 *
	 * @return array Of publications
	 */
	public function get_research_outputs( string $org = null, int $size = 5, string $rendering = 'vancouver' ) {
		$endpoint = 'research-outputs';
		$order    = '-publicationYear'; // Not exposed as a parameter.

		$research_outputs = [];

		$query = new SimpleXMLElement( '<researchOutputsQuery/>' );
		$query->addChild( 'size', $size );
		$query->addChild( 'linkingStrategy', 'portalLinkingStrategy' ); // This needs to near the top.
		$query->addChild( 'locales' )->addChild( 'locale', 'en_GB' );
		$query->addChild( 'fallbackLocales' )->addChild( 'fallbackLocale', 'en_GB' );
		$query->addChild( 'renderings' )->addChild( 'rendering', $rendering );
		// $query->addChild( 'orderings')->addChild('ordering', '-publicationYear');
		$query->addChild( 'orderings' )->addChild( 'ordering', $order );
		$query->addChild( 'publicationStatuses' )->addChild( 'publicationStatus', '/dk/atira/pure/researchoutput/status/published' );
		$query->addChild( 'forOrganisationalUnits' )->addChild( 'uuids' )->addChild( 'uuid', $org );

		/*
		// Parameters in an array would be more native than making XML things. A function could turn an array to XML for wp_remote_post to send
		$query = [
			'researchOutputsQuery' => [
				'size'                   => $size,
				'linkingStrategy'        => 'portalLinkingstrategy',
				'locales'                => [ 'locale' => 'en_GB' ],
				'renderings'             => [ 'rendering' => $rendering ],
				'orderings'              => [ 'ordering' => $order ],
				'publicationStatuses'    => [ 'publicationStatus' => '/dk/atira/pure/researchoutput/status/published' ],
				'forOrganisationalunits' => [ 'uuids' => [ 'uuid' => $org ] ],
			],
		];
		*/

		$xml = $this->query( $endpoint, $query );

		foreach ( $xml->xpath( '//result/items//renderings/rendering' ) as $item ) {
			$research_outputs[] = new Publication( $item, $rendering );
		}
		return $research_outputs;
	}

	/**
	 * Query the API
	 *
	 * @param string           $endpoint  API endpoint, ie resource type.
	 * @param SimpleXMLElement $query     Query parameters as an SimpleXMLElement.
	 * @return string          $xml       Representation of the response.
	 */
	private function query( string $endpoint, SimpleXMLElement $query ) {
		$url  = $this->url . '/' . $endpoint . '?' . http_build_query( [ 'apiKey' => $this->apikey ] );
		$args = [
			'body'    => $query->asXML(),
			'headers' => [ 'Content-Type' => 'application/xml' ],
		];

		$response = wp_remote_post( $url, $args );

		$xml = simplexml_load_string( wp_remote_retrieve_body( $response ) );
		return $xml;
	}
}
