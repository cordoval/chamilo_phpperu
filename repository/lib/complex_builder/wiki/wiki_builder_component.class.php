<?php
/**
 * $Id: wiki_builder_component.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class WikiBuilderComponent extends ComplexBuilderComponent
{

    static function factory($component_name, $builder)
    {
        return parent :: factory('Wiki', $component_name, $builder);
    }

    function get_select_homepage_url()
    {
        return $this->get_parent()->get_select_homepage_url();
    }
}

?>
