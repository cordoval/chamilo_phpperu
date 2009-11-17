<?php
/**
 * $Id: complex_portfolio.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.portfolio
 */

class ComplexPortfolio extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array('portfolio', 'portfolio_item');
    }
}
?>