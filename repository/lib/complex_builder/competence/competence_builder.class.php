<?php
/**
 * $Id: competence_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.competence
 */
require_once dirname(__FILE__) . '/competence_builder_component.class.php';

class CompetenceBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_CREATE_CLOI :
                $component = CompetenceBuilderComponent :: factory('Creator', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
}

?>