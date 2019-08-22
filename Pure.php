<?php
/**
 * An abstraction over Pure, to simulate the data source
 */

require_once 'Publication.php';

/**
 * A Pure API representation.
 */
class Pure {
	/**
	 * Constructs a data source.
	 *
	 * @param string $url     URL to grab data from.
	 * @param string $apikey  Rendering style.
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

		// Parameters in an array would be more native than making XML things.

		/*
		$params = [
			'size' => $size,
			'order' => $order,
			'rendering' => $rendering,
			// 'associatedOrganizationAggregationStrategy' => $qparam->orgagg,
			'linkingStrategy' => 'portalLinkingStrategy',
			'publicationStatus' => '/dk/atira/pure/researchoutput/status/published',
			'locale' => 'en_GB',
			'apiKey' => $this->apikey
		];
		*/

		/*
		// Using HTTP GET
		$url = $endpoint . "/organisational-units/" . $this->org . "/research-outputs" . "?" . http_build_query($params);
		$xml = simplexml_load_file($url);
		*/

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
	 * @throws Exception                  Stuff went wrong.
	 */
	private function query( string $endpoint, SimpleXMLElement $query ) {
		$curl = curl_init( $this->url . '/' . $endpoint . '?' . http_build_query( [ 'apiKey' => $this->apikey ] ) );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/xml' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $query->asXML() );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$result = curl_exec( $curl );

		if ( curl_errno( $curl ) ) {
			throw new Exception( curl_error( $curl ) );
		}
		curl_close( $curl );

		$xml = simplexml_load_string( $result );
		return $xml;
	}
}
