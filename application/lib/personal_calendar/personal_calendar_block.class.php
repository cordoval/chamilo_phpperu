<?php
/**
 * $Id: personal_calendar_block.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar
 */

require_once Path :: get_library_path() . 'block.class.php';

/**
==============================================================================
 *	This class represents a general Personal Calendar Block.
 *
 *	@author Hans De bisschop
==============================================================================
 */

class PersonalCalendarBlock extends Block
{

    /**
     * Constructor.
     * @param array $types The learning object types that may be published.
     * @param  boolean $email_option If true the publisher has the option to
     * send the published learning object by email to the selecter target users.
     */
    function PersonalCalendarBlock($parent, $block_info)
    {
        parent :: __construct($parent, $block_info);
    }

    /**
     * Create a new personal calendar component
     * @param string $type The type of the component to create.
     * @param Weblcms $weblcms The weblcms in
     * which the created component will be used
     */
    static function factory($personal_calendar, $block)
    {
        $type = $block->get_component();
        $filename = dirname(__FILE__) . '/block/personal_calendar_' . $type . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" block');
        }
        $class = 'PersonalCalendar' . Utilities :: underscores_to_camelcase($type);
        require_once $filename;
        return new $class($personal_calendar, $block);
    }
}
?>