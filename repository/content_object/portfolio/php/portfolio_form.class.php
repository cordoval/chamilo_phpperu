<?php
namespace repository\content_object\portfolio;

use repository\ContentObjectForm;

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
}
?>