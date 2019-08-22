<?php
/**
 * A publication
 *
 * @package PureFeedWidget;
 */

namespace PureFeedWidget;

class Publication {
	/**
	 * Constructor.
	 *
	 * If $rendering is given, then $data is already rendered on
	 * serverside or otherwise. If it is not given, then rendering is
	 * left to us to do.
	 *
	 * @param string $data      Bibliographical data.
	 * @param string $rendering Prerendering style, e.g. apa, vancouver.
	 * @throws Exception        If there are issues.
	 */
	public function __construct( string $data, string $rendering = null ) {
		$this->rendering = $rendering;
		if ( $this->rendering ) {
			$this->rendered = $data;
		} else {
			throw new Exception( 'Parsing of raw API data not implemented yet' );
		}
	}

	/**
	 * Render as string.
	 *
	 * @return string String representation.
	 */
	public function __toString() {
		return (string) $this->rendered->asXML();
	}

	/**
	 * Render as HTML.
	 *
	 * @return string HTML representation.
	 */
	public function as_html() {
		$output  = "<li class='item'>";
		$output .= (string) $this->rendered;
		$output .= '</li>';
		return $output;
	}
}
