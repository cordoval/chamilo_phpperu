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
        $html = $this->as_html();
        
    	$this->display_header();
    	echo $html;
    	$this->display_footer();
    }

    function as_html()
    {
        $application = $this->get_application();
        $publication_id = $this->get_publication_id();
        $complex_wrapper_id = $this->get_complex_wrapper_id();
        
        $pub = new RepoViewer($this, 'feedback', false);
		
        $html = array();

        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();

        }
        else
        {
			$objects = $pub->get_selected_objects();
			
        	if(!is_array($objects))
			{
				$objects = array($objects);
			}
			
			foreach($objects as $object)
			{
	            $fb = new FeedbackPublication();
	            $fb->set_application($application);
	            $fb->set_cid($complex_wrapper_id);
	            $fid = $object;
	            $fb->set_fid($object);
	            $fb->set_pid($publication_id);
	            $fb->set_creation_date(time());
	            $fb->set_modification_date(time());
	            $fb->create();
			}

            $message = 'FeedbackCreated';
            $this->redirect(Translation :: get($message), false, array(FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_BROWSE_FEEDBACK));

        }

        return implode('\n', $html);
    }
}
?>