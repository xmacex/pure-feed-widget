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
        $this->publication = $this->retrieve_hosttitle($elem);
    }

    /**
     * Retrieve publication for an item.
     *
     * Figuring out what to have for publication is maybe not exactly
     * trivial, thus a function for it. Might instead set properties
     * directly.
     *
     * @param  SimpleXmlElement $elem
     * @return string           host publication title
     */
    private function retrieve_hosttitle($elem)
    {
        $pubtype = (string)$elem->xpath('@xsi:type')[0];
        
        switch($pubtype) {
        case 'stab:ContributionToBookAnthologyType':
            $host = (string)$elem->xpath('stabl:hostPublicationTitle')[0];
            break;
        case 'stab:ContributionToJournalType':
            $host = (string)$elem->xpath('stabl:journal/journal-template:title/extensions-core:string')[0];
            break;
        /*
        case 'stab:ContributionToConferenceType':
            $host = (string)$elem->xpath('');
            break;
            */
        default:
            $host = '<span style="color: red;">unknown</span>';
        }

        return $host;
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
