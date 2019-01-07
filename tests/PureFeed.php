<?php
/**
 * An abstraction over Pure, to simulate the data source
 */
class PureFeed
{
    function __construct($path)
    {
        $this->publications = [];
        $xml = simplexml_load_file($path);
        foreach($xml->channel->item as $item)
        {
            $this->publications[] = $item;
        }
    }

    function get_by_title($title)
    {
        $item = array_filter($this->publications,
                             function ($i) use ($title) {
                                 return $i->title == $title;
                             }
        );
        return [(string)$title => $item];
    }
}
