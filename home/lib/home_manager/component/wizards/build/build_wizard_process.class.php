<?php
/**
 * $Id: build_wizard_process.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class BuildWizardProcess extends HTML_QuickForm_Action
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function BuildWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform(& $page, $actionName)
    {
        $values = $page->controller->exportValues();
        $failures = 0;
        
        $user_id = $this->parent->get_build_user_id();
        
        if (! $this->parent->truncate_home($user_id))
        {
            $failures ++;
        }
        
        $row_amount = $values['rowsamount'];
        
        for($i = 1; $i <= $row_amount; $i ++)
        {
            $row = new HomeRow();
            $row->set_title($values['row' . $i]['title']);
            $row->set_sort($i);
            $row->set_user($user_id);
            
            if (! $row->create())
            {
                $failures ++;
            }
            
            $column_amount = $values['row' . $i]['columnsamount'];
            
            for($j = 1; $j <= $column_amount; $j ++)
            {
                
                $column = new HomeColumn();
                $column->set_row($row->get_id());
                $column->set_title($values['row' . $i]['column' . $j]['title']);
                $column->set_width($values['row' . $i]['column' . $j]['width']);
                $column->set_sort($j);
                $column->set_user($user_id);
                
                if (! $column->create())
                {
                    $failures ++;
                }
                
                $block_amount = $values['row' . $i]['column' . $j]['blocksamount'];
                
                for($k = 1; $k <= $block_amount; $k ++)
                {
                    $block = new HomeBlock();
                    $block->set_column($column->get_id());
                    $block->set_title($values['row' . $i]['column' . $j]['block' . $k]['title']);
                    $block->set_sort($k);
                    $component = explode('.', $values['row' . $i]['column' . $j]['block' . $k]['component']);
                    $block->set_application($component[0]);
                    $block->set_component($component[1]);
                    $block->set_user($user_id);
                    
                    if (! $block->create())
                    {
                        $failures ++;
                    }
                    
                    if (! HomeDataManager :: get_instance()->create_block_properties($block))
                    {
                        $failures ++;
                    }
                }
            }
        }
        
        if ($failures)
        {
            $message = 'HomeNotBuildProperly';
        }
        else
        {
            $message = 'HomeBuildProperly';
        }
        
        $page->controller->container(true);
        $this->parent->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
    }
}
?>