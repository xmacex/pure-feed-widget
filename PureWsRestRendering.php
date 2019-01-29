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
    function __construct($path, $xmldata=NULL, $rendering='vancouver')
    {
        $this->rendering = $rendering;
        $this->publications = [];

        if ($xmldata) {
            $xml = $xmldata;
        } else {
            $params = ['rendering' => $this->rendering,
                       'linkingStrategy' => 'portalLinkingStrategy',
                       'locale' => 'en_GB'];
            $url = $path . "&" . http_build_query($params);
            $xml = simplexml_load_file($url);
        }

        foreach($xml->xpath('//core:result/core:renderedItem') as $item)
        {
            $item->registerXPathNamespace('stabl', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $item->registerXPathNamespace('publication-template', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $this->publications[] = new RenderedPublication($item, $this->rendering);
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
