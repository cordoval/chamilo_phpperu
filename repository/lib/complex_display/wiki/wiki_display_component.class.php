<?php
/**
 * $Id: wiki_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki
 */
/**
 * @author Samumon
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class WikiDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Wiki', $component_name, $builder);
    }
}

?>
