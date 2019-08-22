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
     * @param SimpleXML $xmldata If given, loads this data insteado
     */
    function __construct($path, $xmldata = false)
    {
        if ($xmldata) {
            $xml = $xmldata;
        } else {
            $params = ['rendering' => 'vancouver',
                       'linkingStrategy' => 'portalLinkingStrategy'];
            $url = $path . "&" . http_build_query($params);
            $this->publications = [];
            $xml = simplexml_load_file($url);
        }
        foreach($xml->xpath('//core:result/core:renderedItem') as $item)
        {
            $item->registerXPathNamespace('stabl', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $item->registerXPathNamespace('publication-template', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $this->publications[] = new RenderedPublication($item);
        }
    }

    /* This served testing purposes only, so comment out for now.
    function get_by_title($title)
    {
        $item = array_filter($this->publications,
                             function ($i) use ($title) {
                                 return $i->title == $title;
                             }
        );
        return [(string)$title => $item];
    }
    */
}
