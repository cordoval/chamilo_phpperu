<?php
/**
 * $Id: portfolio_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.portfolio
 */
require_once dirname(__FILE__) . '/portfolio.class.php';
/**
 * This class represents a form to create or update portfolios
 */
class PortfolioForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Portfolio();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        parent :: set_values($defaults);
    }
}
?>