<?php
/**
 * An abstraction over Pure, to simulate the data source
 *
 * @package PureFeedWidget;
 */

namespace PureFeedWidget;

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

		$params = [
			'size'                   => $size,
			'linkingStrategy'        => 'portalLinkingStrategy',
			'locales'                => [ 'locale' => 'en_GB' ],
			'renderings'             => [ 'rendering' => $rendering ],
			'orderings'              => [ 'ordering' => $order ],
			'publicationStatuses'    => [ 'publicationStatus' => '/dk/atira/pure/researchoutput/status/published' ],
			'forOrganisationalUnits' => [ 'uuids' => [ 'uuid' => $org ] ],
		];

		// This is the wrong place for this; would be nice if the XML
		// conversion was done from the query function, but these
		// higher level operations need to provide the top level XML
		// root element for each endpoint. Maybe refactor queries as a
		// hierarchy of classes? Or modify the function.
		$query = new SimpleXMLElement( '<researchOutputsQuery/>' );
		$this->array_to_xml( $query, $params );

		$xml = $this->query( $endpoint, $query );

		foreach ( $xml->xpath( '//result/items//renderings/rendering' ) as $item ) {
			$research_outputs[] = new Publication( $item, $rendering );
		}
		return $research_outputs;
	}

	/**
	 * Query the API
	 *
	 * @param  string           $endpoint  API endpoint, ie resource type.
	 * @param  SimpleXMLElement $query     Query parameters as an SimpleXMLElement.
	 * @return string           $xml       Representation of the response.
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

	/**
	 * Populates and XML element with an array, in place.
	 *
	 * @param SimpleXMLElement $object XML object to populate.
	 * @param array            $data   Data to push to the XML object.
	 *
	 * @author Francis Lewis
	 *
	 * From here https://stackoverflow.com/a/19987539/1760439
	 */
	private function array_to_xml( SimpleXMLElement $object, array $data ) {
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$new_object = $object->addChild( $key );
				$this->array_to_xml( $new_object, $value );
			} else {
				// if the key is an integer, it needs text with it to actually work.
				if ( $key === (int) $key ) {
					$key = "$key";
				}

				$object->addChild( $key, $value );
			}
		}
	}
}
