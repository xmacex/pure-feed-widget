<?php
/**
 * An abstraction over Pure, to simulate the data source
 */
class PureWsRest
{
    function __construct($path)
    {
        $this->publications = [];
        $xml = simplexml_load_file($path);
        foreach($xml->xpath('core:result/core:content') as $item)
        {
            $item->registerXPathNamespace('stabl', 'http://atira.dk/schemas/pure4/model/template/abstractpublication/stable');
            $item->registerXPathNamespace('person-template', 'http://atira.dk/schemas/pure4/model/template/abstractperson/stable');
            $this->publications[] = $item;
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
