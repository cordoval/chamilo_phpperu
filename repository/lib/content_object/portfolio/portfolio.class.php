<?php
/**
 * $Id: portfolio.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.portfolio
 */
/**
 * This class represents an portfolio
 */
class Portfolio extends ContentObject
{

    function get_allowed_types()
    {
        return array('portfolio', 'portfolio_item');
    }
}
?>