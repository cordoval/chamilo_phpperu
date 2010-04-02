<?php
/**
 * $Id: indicator_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.indicator
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class IndicatorBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Indicator', $component_name, $builder);
    }
}

?>
