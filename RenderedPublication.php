<?php
class RenderedPublication
{
    public $rendered;
    public function __construct($elem, $rendering='vancouver')
    {
        $this->rendering = $rendering;
        $this->rendered = $elem;
        $this->title = $this->get_title($elem, $this->rendering);
    }

    public function get_title($elem, $rendering)
    {
        switch ($rendering) {
        case 'vancouver':
            $title = (string)$elem->xpath('div/span[@class="title"]/a/span')[0];
            break;
        case 'apa':
            $title = (string)$elem->xpath('//em')[0]; // Whoop crude
            break;
        default:
            $title = NULL;
        }

        return $title;
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
