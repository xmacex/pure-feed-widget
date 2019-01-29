<?php
/**
 * @package Pure_Feed
 */

require_once('PureWsRestRendering.php');
require_once('RenderedPublication.php');

class TestPublicationRendering extends WP_UnitTestCase
{
    /**
     * @dataProvider pure_wsrest_rendered_item_provider
     */
    function test_should_parse_types_when_given_pure_item($pub)
    {
        $this->assertInstanceOf(RenderedPublication::class, $pub);
        $this->assertInstanceOf(SimpleXMLElement::class, $pub->rendered);
    }

    /**
     * @dataProvider pure_wsrest_rendered_item_provider
     */
    function test_should_parse_content_when_given_pure_item($pub)
    {
        // $this->assertGreaterThan(0, $pub->rendered);
        $this->assertGreaterThan(0, strlen((string)$pub->rendered));
    }

    /**
     * Data providers
     *
     * Data provider outputs look like this
     *
     *     return [
     *         "first data set name" => [item1, item2, item3],
     *         "second data set name" => [item4, item5]
     *     ];
     */

    function pure_wsrest_rendered_item_provider()
    {
        $xmldata = simplexml_load_file(WSRESTFILE_VANCOUVER);
        $feed = new PureWsRestRendering(NULL, $xmldata);
        // return ['A collection of preloaded publications' => $feed->publications];
        
        $items = [];
        foreach($feed->publications as $pub)
        {
            $items[] = [$pub->title => $pub];
        }
        return $items;
    }

    /**
     * Gets live data from IT University of Copenhagen MAD research group
     */
    function pure_feed_item_from_itu_mad_provider()
    {
        $feed = new PureWsRestRendering("https://pure.itu.dk/ws/rest/publication?associatedOrganisationUuids.uuid=cf9b4e6a-e1ad-41e3-9475-7679abe7131b&window.size=5&associatedOrganisationAggregationStrategy=RecursiveContentValueAggregator");
        $items = [];
        foreach($feed->publications as $pub)
        {
            $items[] = [(string)$pub->title => $pub];
        }
        return $items;
    }
}
