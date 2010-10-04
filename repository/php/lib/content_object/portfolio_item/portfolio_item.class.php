<?php
/**
 * $Id: portfolio_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.portfolio_item
 */
class PortfolioItem extends ContentObject implements Versionable
{
    const PROPERTY_REFERENCE = 'reference_id';
	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_REFERENCE);
    }

    function get_reference()
    {
        return $this->get_additional_property(self :: PROPERTY_REFERENCE);
    }

    function set_reference($reference)
    {
        $this->set_additional_property(self :: PROPERTY_REFERENCE, $reference);
    }
}
?>