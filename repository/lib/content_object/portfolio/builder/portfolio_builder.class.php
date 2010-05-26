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
            case ComplexBuilder :: ACTION_BROWSE :
                $component = PortfolioBuilderComponent :: factory('Browser', $this);
                break;
            case PortfolioBuilder :: ACTION_CREATE_PORTFOLIO_ITEM :
                $component = PortfolioBuilderComponent :: factory('ItemCreator', $this);
                break;
            case self :: ACTION_DELETE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = PortfolioBuilderComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM :
                $component = PortfolioBuilderComponent :: factory('Updater', $this);
                break;
            default
        }
        
            $component->run();
    }
}

?>