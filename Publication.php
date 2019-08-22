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
        $desc = new SimpleXMLElement($elem->description);

        foreach($desc->div[0]->a as $related)
        {
            if($related["rel"] == "Person")
            {
                $name = (string)$related->span;
                array_push($this->authors, $name);
            }
        }
        $this->date = strtotime($desc->div[0]->span[0]);
        $this->title = (string)$elem->title;
        $this->link = (string)$elem->link;
        $this->publication = (string)$desc->div[1]->div[1]->table->tbody->tr[1]->td;
    }

    public function __toString()
    {
        return implode(", ", $this->authors ) . $this->title . $this->date . $this->publication;
    }

    private function authString()
    {
        return implode(", ", $this->authors);
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

    private function year()
    {
        return date('Y', $this->date);
    }

    public function toHtml()
    {
        $output = "<li class='item'>";
        $output .= "<span class='authors'>" . $this->authString() . "</span>";
        // $output .= ". ";
        $output .= " ";
        // $output .= "<span class='date'>" . $this->date . "</span>";
        $output .= "<span class='date'>" . $this->year() . "</span>";
        $output .= ". ";
        $output .= $this->titleHtml();
        $output .= ". ";
        $output .= "<span class='publication'>" . $this->publication . "</span>";
        $output .= "</li>";
        return $output;
    }
}
