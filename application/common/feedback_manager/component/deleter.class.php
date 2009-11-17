<?php
/**
 * $Id: deleter.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager.component
 */

/**
 * Description of deleterclas
 *
 * @author pieter
 */
class FeedbackManagerDeleterComponent extends FeedbackManagerComponent
{

    function run()
    {
        echo $this->as_html();
    }

    function as_html()
    {
        //fouten opvang en id dynamisch ophalen
        //$id = Request :: get(FeedbackPublication :: PROPERTY_ID);
        

        $pid = Request :: get('pid');
        $cid = Request :: get('cid');
        
        $id = Request :: get('deleteitem');
        
        if (! $this->get_user())
        {
            $this->display_header($this->get_breadcrumb_trail());
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
        $FeedbackPublication = $this->retrieve_feedback_publication($id);
        
        if ($FeedbackPublication->delete())
        {
            
            $message = 'FeedbackDeleted';
            $succes = true;
        }
        
        else
        {
            $message = 'FeedbackNotDeleted';
            $succes = false;
        }
        $this->redirect(Translation :: get($message), $succes ? false : true, array('pid' => $pid, 'cid' => $cid));
    }
}
?>
