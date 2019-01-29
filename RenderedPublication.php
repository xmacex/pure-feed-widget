<?php
class RenderedPublication
{
    public $rendered;
    public function __construct($elem)
    {
        $this->rendered = $elem;
        $this->title = (string)$elem->xpath('div/span[@class="title"]/a/span')[0];
    }
    
    public function __toString()
    {
        return $this->rendered->asXML();
    }

    public function toHtml()
    {
        $output = "<li class='item'>";
        $output .= (string)$this->rendered->xpath('div')[0]->asXML();
        $output .= "</li>";
        return $output;
    }
}
