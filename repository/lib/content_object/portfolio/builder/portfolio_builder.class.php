<?php
/**
 * $Id: portfolio_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.portfolio
 */

class PortfolioBuilder extends ComplexBuilder
{
    const ACTION_CREATE_PORTFOLIO_ITEM = 'create_item';

    function run()
    {
        $action = $this->get_action();
        
        switch ($action)
        {
            case ComplexBuilder :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM:
            	$component = $this->create_component('Deleter');
                break;
            case ComplexBuilder :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM:
            	$component = $this->create_component('Updater');
                break;
            case ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM:
            	$component = $this->create_component('ItemCreator');
                break;
            case PortfolioBuilder :: ACTION_CREATE_PORTFOLIO_ITEM :
                $component = $this->create_component('ItemCreator');
                break;
            case ComplexBuilder :: ACTION_BROWSE : 
            	$component = $this->create_component('Browser');
                break;
            default:
            	$this->set_action(ComplexBuilder :: ACTION_BROWSE);
                $component = $this->create_component('Browser');
                break;
        }
        
            $component->run();
    }

    function get_application_component_path()
	{
		return dirname(__FILE__) . '/component/';
	}
}

?>