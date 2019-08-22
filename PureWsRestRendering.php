<?php
/**
 * An abstraction over Pure, to simulate the data source
 *
 * The serverside does the rendering.
 */

require_once('RenderedPublication.php');

class PureWsRestRendering
{
    /**
     * Constructs a data source
     *
     * @param string $path       URL to grab data from
     * @param SimpleXML $xmldata If given, loads this data instead
     * @param string $rendering  Rendering style
     */
    function __construct($endpoint, $org=NULL, $xmldata=NULL, $noitems=5, $orderby='publicationDate', $rendering='vancouver', $orgagg='RecursiveContentValueAggregator')
    {
        $this->org = $org;
        $this->noitems = $noitems;
        $this->orderby = $orderby;
        $this->rendering = $rendering;
        $this->orgagg = $orgagg;

        $this->publications = [];

        if ($xmldata) {
            $xml = $xmldata;
        } else {
            $params = ['associatedOrganisationUuids.uuid' => $this->org,
                       'window.size' => $this->noitems,
                       'orderBy.property' => $this->orderby,
                       'rendering' => $this->rendering,
                       'associatedOrganizationAggregationStrategy' => $this->orgagg,
                       'linkingStrategy' => 'portalLinkingStrategy',
                       'locale' => 'en_GB'];
            $url = $endpoint . "/publication" . "?" . http_build_query($params);
            $xml = simplexml_load_file($url);
        }

        foreach($xml->xpath('//core:result/core:renderedItem') as $item)
        {
            $item->registerXPathNamespace('stabl', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $item->registerXPathNamespace('publication-template', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $this->publications[] = new RenderedPublication($item, $this->rendering);
        }
    }
}
