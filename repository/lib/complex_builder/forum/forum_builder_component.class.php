<?php
/**
 * $Id: forum_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ForumBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Forum', $component_name, $builder);
    }
}

?>