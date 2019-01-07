<?php
class Publication
{
    public $authors = [];
    public $date;
    public $title;
    public $link;
    public $publication;

    public function __construct($elem)
    {
        foreach($elem->xpath('stabl:persons/person-template:personAssociation/person-template:name') as $nameelem)
        {
            $name = (string)$nameelem->xpath('core:lastName')[0] . ", " . (string)$nameelem->xpath('core:firstName')[0];
            array_push($this->authors, $name);
        }
        $this->date = (integer)$elem->xpath('stabl:publicationDate/core:year')[0];
        $this->title = (string)$elem->xpath('stabl:title')[0];
        $this->link = (string)$elem->xpath('core:portalUrl')[0];
        $this->publication = '<span style="color: red";>Publication</span>'; // FIXME
    }

    public function __toString()
    {
        return implode(", ", $this->authors ) . $this->title . $this->date . $this->publication;
    }

    private function authString()
    {
        return implode("; ", $this->authors);
    }

    private function titleHtml($link=true)
    {
        $output = "<span class='title'>";
        if ($link)
        {
            $output .= "<a href='" . $this->link . "'>" . $this->title . "</a>";
        }
        else
        {
            $output .= $this->title;
        }
        $output .= "</span>";

        return $output;
    }

    public function toHtml()
    {
        $output = "<li class='item'>";
        $output .= "<span class='authors'>" . $this->authString() . "</span>";
        // $output .= ". ";
        $output .= " ";
        $output .= "<span class='date'>" . $this->date . "</span>";
        $output .= ". ";
        $output .= $this->titleHtml();
        $output .= ". ";
        $output .= "<span class='publication'>" . $this->publication . "</span>";
        $output .= "</li>";
        return $output;
    }
}
