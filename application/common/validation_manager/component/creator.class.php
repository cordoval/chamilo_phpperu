<?php
/**
 * $Id: creator.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.validation_manager.component
 */

/**
 * Description of creator
 *
 * @author Pieter Hens
 */
class ValidationManagerCreatorComponent extends ValidationManagerComponent
{

	function run()
    {
    	$html = $this->as_html();
    	
    	$this->display_header(BreadcrumbTrail :: get_instance());
    	echo $html;
    	$this->display_footer();
    }
    
    function as_html()
    {
        
        $pid = Request :: get('pid');
        $user_id = Request :: get('user_id');
        $cid = Request :: get('cid');
        $action = Request :: get('action');
        $application = $this->get_parent()->get_application();
        
        if (! $this->get_user())
        {
            $this->display_header($this->get_breadcrumb_trail());
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $id = $this->get_user()->get_id();
        $conditions = array();
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_PID, $pid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_CID, $cid);
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_OWNER, $this->get_user_id());
        $conditions[] = new EqualityCondition(Validation :: PROPERTY_APPLICATION, PortfolioManager :: APPLICATION_NAME);
        $condition = new AndCondition($conditions);

        if ($this->count_validations($condition) == 0)
        {
            $val = new Validation();
            $val->set_cid($cid);
            $val->set_pid($pid);
            $val->set_application($application);
            $val->set_owner($this->get_user()->get_id());
            $val->set_validated(1);
            $today = date('Y-m-d G:i:s');
            $now = time();
            
            $val->set_validated($now);
            
            if ($val->create($val))
            {
                
                $message = 'ValidationCreated';
                $succes = true;
            }
            
            else
            {
                $message = 'ValidationNotCreated';
                $succes = false;
            }
        
        }
        
        else
        {
            
            $val = $this->retrieve_validations($condition)->next_result();
            $val->set_validated(time());
            if ($val->update())
            {
                $message = 'ValidationUpdated';
                $succes = true;
            }
            else
            {
                $message = 'ValidationNotUpdated';
                $succes = true;
            }
        
        }
        $this->redirect(Translation :: get($message), succes ? false : true, array(Application :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, 'pid' => $pid, 'cid' => $cid, 'user_id' => $user_id, 'action' => $action));
    
    }
}
?>