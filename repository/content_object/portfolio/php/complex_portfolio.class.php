<?php
namespace repository\content_object\portfolio;

use repository\ComplexContentObjectItem;
use repository\content_object\portfolio_item\PortfolioItem;

/**
 * $Id: complex_portfolio.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.portfolio
 */

class ComplexPortfolio extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Portfolio :: get_type_name(), PortfolioItem :: get_type_name());
    }
}
?>