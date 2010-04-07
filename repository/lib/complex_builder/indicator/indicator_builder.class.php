<?php
/**
 * $Id: indicator_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator
 */
require_once dirname(__FILE__) . '/indicator_builder_component.class.php';

class IndicatorBuilder extends ComplexBuilder
{

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = IndicatorBuilderComponent :: factory('Browser', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }
}

?>