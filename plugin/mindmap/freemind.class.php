<?php
class Freemind
{
    private $base_node;
    private $version;

    function __construct($version = '0.9.0')
    {
        $this->version = $version;
    }

    function set_base_node(FreemindNode $base_node)
    {
        $this->base_node = $base_node;
    }


    function to_html()
    {
        
    }

    function to_file()
    {
        
    }

    function to_xml()
    {
        return '<map version="' . $this->version . '">' . $this->base_node->to_xml() . '</map>';
    }
}

?>
