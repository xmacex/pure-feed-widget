<?php
class Publication
{
    public $rendered;
    public function __construct($elem, $rendering='vancouver')
    {
        $this->rendering = $rendering;
        $this->rendered = $elem;
    }

    public function __toString()
    {
        return (string)$this->rendered->asXML();
    }

    public function toHtml()
    {
        $output = "<li class='item'>";
        $output .= (string)$this->rendered;
        $output .= "</li>";
        return $output;
    }
}
