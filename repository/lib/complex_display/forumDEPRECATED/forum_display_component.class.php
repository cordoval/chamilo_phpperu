<?php
/**
 * $Id: forum_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum
 */
/**
 * @author Michael Kyndt
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class ForumDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Forum', $component_name, $builder);
    }
}

?>