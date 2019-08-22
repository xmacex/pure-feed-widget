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
    function __construct($endpoint, $apikey, $org=NULL, $xmldata=NULL, $noitems=5, $orderby='publicationYear', $rendering='vancouver', $orgagg='RecursiveContentValueAggregator')
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
            $params = [
                'size' => $this->noitems,
                'order' => '-publicationYear',
                'rendering' => $this->rendering,
                'associatedOrganizationAggregationStrategy' => $this->orgagg,
                'linkingStrategy' => 'portalLinkingStrategy',
                'publicationStatus' => '/dk/atira/pure/researchoutput/status/published',
                'locale' => 'en_GB',
                'apiKey' => $apikey
            ];
            // Using HTTP GET
            // $url = $endpoint . "/organisational-units/" . $this->org . "/research-outputs" . "?" . http_build_query($params);
            // $xml = simplexml_load_file($url);

            // Using HTTP POST
            $url = $endpoint . "/research-outputs/?apiKey=" . $apikey;
            $xml = $this->get_publications($url);
        }

        foreach($xml->xpath('//result/items//renderings/rendering') as $item) {
            $this->publications[] = new RenderedPublication($item, $this->rendering);
        }
    }

    function get_publications($url)
    {
        /*
        $query = '<?xml version="1.0"?>
        <researchOutputsQuery>
            <size>3</size>
            <linkingStrategy>portalLinkingStrategy</linkingStrategy>
            <renderings>
                <rendering>apa</rendering>
            </renderings>
            <orderings>
                <ordering>-publicationYear</ordering>
            </orderings>
            <publicationStatuses>
                <publicationStatus>/dk/atira/pure/researchoutput/status/published</publicationStatus>
            </publicationStatuses>
            <forOrganisationalUnits>
            <uuids>
                <uuid>ca087d09-fd99-4c42-8180-9799383c072e</uuid>
            </uuids>
            </forOrganisationalUnits>
        </researchOutputsQuery>';
        */

        $query = new SimpleXMLElement('<researchOutputsQuery/>');
        $query->addChild('size', $this->noitems);
        $query->addChild('linkingStrategy', 'portalLinkingStrategy'); // This needs to be before renderings
        $query->addChild('renderings')->addChild('rendering', $this->rendering);
        // $query->addChild('orderings')->addChild('ordering', '-publicationYear');
        $query->addChild('orderings')->addChild('ordering', $this->orderby);
        $query->addChild('publicationStatuses')->addChild('publicationStatus', '/dk/atira/pure/researchoutput/status/published');
        $query->addChild('forOrganisationalUnits')->addChild('uuids')->addChild('uuid', 'ca087d09-fd99-4c42-8180-9799383c072e');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml"));
        curl_setopt($curl, CURLOPT_POST, true);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query->asXML());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);

        if(curl_errno($curl)) {
            throw new Exception(curl_error($curl));
        }

        curl_close($curl);

        $xml = simplexml_load_string($result);
        return $xml;
    }
}
