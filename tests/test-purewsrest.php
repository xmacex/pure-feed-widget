<?php

require_once('PureWsRest.php');

class TestPureWsRest extends WP_UnitTestCase
{
    function test_retrieving_feed_should_succeed_on_valid_url()
    {
        $xml = simplexml_load_file(WSRESTFILE);

        $this->assertInstanceOf(SimpleXMLElement::class, $xml);
    }

    function test_parsed_response_shoult_contain_five_publications()
    {
        $xml = simplexml_load_file(WSRESTFILE);

        $results = $xml->xpath('//core:result/core:content');
        $this->assertCount(5, $results);
    }

    function test_parsing_of_titles_should_work_from_valid_data()
    {
        $xml = simplexml_load_file(WSRESTFILE);

        $this->assertEquals("Full Synopsis of EVA Copenhagen 2018 - Politics of the Machines - Art and After", $xml->xpath('//core:result/core:content[1]/stab:title')[0]);
        $this->assertEquals("Change the Refrains; Eco-Logic Design for the Revaluation of Urban Space", $xml->xpath('//core:result/core:content[2]/stab:title')[0]);
        $this->assertEquals("Affect and Emotions in Patient Data Work", $xml->xpath('//core:result/core:content[3]/stab:title')[0]);
        $this->assertEquals("Anarchival proposals in design anthropology", $xml->xpath('//core:result/core:content[4]/stab:title')[0]);
        $this->assertEquals("GIFT", $xml->xpath('//core:result/core:content[5]/stab:title')[0]);
    }

    function test_parsing_of_author_first_names_should_work_from_valid_data()
    {
        $xml = simplexml_load_file(WSRESTFILE);

        $this->assertEquals(["Laura", "Morten"], $xml->xpath('//core:result/core:content[1]/stab:persons/person-template:personAssociation/person-template:name/core:firstName'));
        $this->assertEquals(["Jonas"], $xml->xpath('//core:result/core:content[2]/stab:persons/person-template:personAssociation/person-template:name/core:firstName'));
        $this->assertEquals(["Tariq Osman", "Jonas"], $xml->xpath('//core:result/core:content[3]/stab:persons/person-template:personAssociation/person-template:name/core:firstName'));
        $this->assertEquals(["Ester", "Jonas"], $xml->xpath('//core:result/core:content[4]/stab:persons/person-template:personAssociation/person-template:name/core:firstName'));
        $this->assertEquals(["Jon", "Benjamin", "Steve", "Lina", "Anders Sundnes", "William", "Paulina", "Karin", "Jocelyn", "Emily-Clare", "Annika", "Tim"], $xml->xpath('//core:result/core:content[5]/stab:persons/person-template:personAssociation/person-template:name/core:firstName'));
    }

    function test_parsing_of_publication_years_should_work_from_valid_data()
    {
        $xml = simplexml_load_file(WSRESTFILE);

        $this->assertEquals(["2018", "2018", "2018", "2016", "2018"], $xml->xpath('//core:result/core:content/stab:publicationDate/core:year'));
    }
}
