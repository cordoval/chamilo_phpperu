<?php
/**
 * $Id: phrases_publication.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar
 */
require_once Path :: get_application_path() . '/lib/phrases/phrases_data_manager.class.php';

/**
 * This class represents a CalendarEventPublication.
 *
 * @author Hans de Bisschop
 */
class PhrasesMasteryLevel extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'mastery_level';

    const PROPERTY_LEVEL = 'level';
    const PROPERTY_DISPLAY_ORDER = 'display_order';
    const PROPERTY_UPGRADE_AMOUNT = 'upgrade_amount';
    const PROPERTY_UPGRADE_SCORE = 'upgrade_score';

    /**
     * Get the default properties of all CalendarEventPublications.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_LEVEL, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_UPGRADE_AMOUNT, self :: PROPERTY_UPGRADE_SCORE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PhrasesDataManager :: get_instance();
    }

    function get_level()
    {
        return $this->get_default_property(self :: PROPERTY_LEVEL);
    }

    function set_level($level)
    {
        $this->set_default_property(self :: PROPERTY_LEVEL, $level);
    }

    function get_display_order()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
    }

    function set_display_order($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
    }

    function get_upgrade_amount()
    {
        return $this->get_default_property(self :: PROPERTY_UPGRADE_AMOUNT);
    }

    function set_upgrade_amount($upgrade_amount)
    {
        $this->set_default_property(self :: PROPERTY_UPGRADE_AMOUNT, $upgrade_amount);
    }

    function get_upgrade_score()
    {
        return $this->get_default_property(self :: PROPERTY_UPGRADE_SCORE);
    }

    function set_upgrade_score($upgrade_score)
    {
        $this->set_default_property(self :: PROPERTY_UPGRADE_SCORE, $upgrade_score);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function move($places)
    {
        return $this->get_data_manager()->move_phrases_mastery_level($this, $places);
    }
}
?>