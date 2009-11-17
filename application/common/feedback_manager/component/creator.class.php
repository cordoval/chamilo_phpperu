<?php
/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.feedback_manager.component
 */

/**
 * Description of updaterclass
 *
 * @author pieter
 */


class FeedbackManagerCreatorComponent extends FeedbackManagerComponent
{

    function run()
    {
        echo $this->as_html();
    }

    function as_html()
    {
        
        $pid = Request :: get('pid');
        $cid = Request :: get('cid');
        
        $application = $this->get_parent()->get_application();
        $object = Request :: get('object');
        
        $pub = new RepoViewer($this, 'feedback', true);
        
        $actions = array('pid' => $pid, 'cid' => $cid, FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_CREATE_FEEDBACK);
        
        foreach ($actions as $type => $action)
        {
            $pub->set_parameter($type, $action);
        }
        
        $html = array();
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        
        }
        else
        {
            
            $fb = new FeedbackPublication();
            $fb->set_application($application);
            $fb->set_cid($cid);
            $fid = $object; //$this->adm->get_next_feedback_id();
            $fb->set_fid($fid);
            $fb->set_pid($pid);
            $fb->set_creation_date(time());
            $fb->set_modification_date(time());
            $fb->create();
            
            $message = 'FeedbackCreated';
            $html[] = $action;
            $this->redirect(Translation :: get($message), false, array('pid' => $pid, 'cid' => $cid));
        
        }
        
        return implode('\n', $html);
    }
}
?>
