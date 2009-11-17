<?php
/**
 * $Id: glossary_display_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary
 */
/**
 * @author Samumon
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class GlossaryDisplayComponent extends ComplexDisplayComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Glossary', $component_name, $builder);
    }
}

?>
