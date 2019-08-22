<?php
/**
 * @package Pure_Feed
 */

require_once('PureWsRest.php');
require_once('Publication.php');

class TestPublication extends WP_UnitTestCase
{
    /**
     * @dataProvider pure_wsrest_item_provider
     */
    function test_publication_should_parse_types_when_given_pure_item($pub)
    {
        $this->assertInstanceOf(Publication::class, $pub);
        $this->assertInternalType('array', $pub->authors);
        $this->assertInternalType('integer', $pub->date);
        $this->assertInternalType('string', $pub->title);
        $this->assertInternalType('string', $pub->link);
        $this->assertInternalType('string', $pub->publication);
    }

    /**
     * @dataProvider pure_wsrest_item_provider
     */
    function test_publication_should_parse_content_when_given_pure_item($pub)
    {
        $this->assertGreaterThan(0, $pub->authors);
        $this->assertGreaterThan(0, $pub->date);
        $this->assertGreaterThan(0, strlen($pub->title));
        $this->assertGreaterThan(0, strlen($pub->link));
        $this->assertGreaterThan(0, strlen($pub->publication));
    }

    function test_publication_should_infer_host_publication_title()
    {
        $wsrest = new PureWsRest(WSRESTFILE);
        // var_dump($wsrest->publications);
    }

    /**
     * @dataProvider infrastructuring_in_pd_provider
     */
    function _test_a_single_publication_should_have_fields_defined($pub)
    {
        # $iipwdillwdillt = new Publication($elem);
        $iipwdillwdillt = $pub;

        $this->assertInternalType('string', $iipwdillwdillt->title);
        $this->assertEquals("Infrastructuring in PD: What Does Infrastructuring Look Like? When Does It Look Like That?", $iipwdillwdillt->title);
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

    function pure_wsrest_item_provider()
    {
        $feed = new PureWsRest(WSRESTFILE);
        $items = [];
        foreach($feed->publications as $pub)
        {
            $items[] = [(string)$pub->title => $pub];
        }
        return $items;
    }


    function infrastructuring_in_pd_provider()
    {
        $feed = new PureWsRest(WSRESTFILE);
        return $feed->get_by_title("Infrastructuring in PD: What Does Infrastructuring Look Like? When Does It Look Like That?");
    }

    /**
     * Gets live data from IT University of Copenhagen MAD research group
     */
    function pure_feed_item_from_itu_mad_provider()
    {
        $feed = new PureWsRest("https://pure.itu.dk/ws/rest/publication?associatedOrganisationUuids.uuid=cf9b4e6a-e1ad-41e3-9475-7679abe7131b&window.size=5&associatedOrganisationAggregationStrategy=RecursiveContentValueAggregator");
        $items = [];
        foreach($feed->publications as $pub)
        {
            $items[] = [(string)$pub->title => $pub];
        }
        return $items;
    }
}
