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
class FeedbackManagerDeleterComponent extends FeedbackManager
{

    function run()
    {
        $html = $this->as_html();
        
    	$this->display_header();
    	echo $html;
    	$this->display_footer();
    }

    function as_html()
    {
        $ids = Request :: get(FeedbackManager :: PARAM_FEEDBACK_ID);
        
        if (! $this->get_user())
        {
            Display :: error_message(Translation :: get("NotAllowed"));
            exit();
        }
        
        $failures = 0;
        
        if(isset($ids))
        {
	        if(!is_array($ids))
	        {
	        	$ids = array($ids);
	        }
	        
	        foreach($ids as $id)
	        {
	        	$FeedbackPublication = $this->retrieve_feedback_publication($id);
	        
		        if (!$FeedbackPublication->delete())
		        {
		        	$failures++;
		        }
	        }
	       
	        $message = $this->get_result($failures, count($ids), 'FeedbackPublicationNotDeleted', 'FeedbackPublicationsNotDeleted', 'FeedbackPublicationDeleted', 'FeedbackPublicationsDeleted');
	        
	        $this->redirect($message, ($failures > 0) ? true : false, array(FeedbackManager :: PARAM_ACTION => $this->get_parameter(self::PARAM_OLD_ACTION)));
        }
        else
        {
        	Display :: error_message(Translation :: get("NoObjectSelected"));
            exit();
        }
    }
}
?>