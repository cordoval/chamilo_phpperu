<?php
/**
 * $Id: wiki_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki
 */
require_once dirname(__FILE__) . '/wiki_builder_component.class.php';

class WikiBuilder extends ComplexBuilder
{
    const ACTION_SELECT_HOMEPAGE = 'select_home';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_BROWSE_CLO :
                $component = WikiBuilderComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_SELECT_HOMEPAGE :
                $component = WikiBuilderComponent :: factory('HomepageSelector', $this);
                break;
        }
        
        if (! $component)
            parent :: run();
        else
            $component->run();
    }

    function get_select_homepage_url($root_lo, $cloi)
    {
        return $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => self :: ACTION_SELECT_HOMEPAGE, ComplexBuilder :: PARAM_ROOT_LO => $root_lo->get_id(), ComplexBuilder :: PARAM_SELECTED_CLOI_ID => $cloi->get_id(), 'publish' => Request :: get('publish')));
    }
}

?>