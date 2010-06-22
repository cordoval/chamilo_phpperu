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
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    function get_allowed_types()
    {
        return array(Portfolio :: get_type_name(), PortfolioItem :: get_type_name());
    }
}
?>