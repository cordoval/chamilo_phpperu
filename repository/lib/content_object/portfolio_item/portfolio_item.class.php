<?php
/**
 * $Id: portfolio_item.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.portfolio_item
 */
class PortfolioItem extends ContentObject
{
    const PROPERTY_REFERENCE = 'reference_id';

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