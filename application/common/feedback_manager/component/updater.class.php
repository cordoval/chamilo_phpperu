<?php
/**
 * $Id: updater.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager.component
 */

/**
 * Description of updaterclass
 *
 * @author pieter
 */

require_once Path :: get_repository_path() . '/lib/content_object/feedback/feedback_form.class.php';

class FeedbackManagerUpdaterComponent extends FeedbackManagerComponent
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
        $id = Request :: get('updateitem');
        $pid = Request :: get('pid');
        $cid = Request :: get('cid');
        
        $url = $this->get_url(array('pid' => $pid, 'cid' => $cid, FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_UPDATE_FEEDBACK, 'updateitem' => $id));
        
        $pub_feedback = AdminDataManager :: get_instance()->retrieve_feedback_publication($id);
        
        $rdm = RepositoryDataManager :: get_instance();
        $object = $rdm->retrieve_content_object($pub_feedback->get_fid());
        
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $object, 'editfeedback', 'post', $url, null, null, false);
        
        if ($form->validate())
        {
            $success = $form->update_content_object();
            
            if ($form->is_version())
            {
                $pub_feedback->set_fid($object->get_latest_version_id());
                $pub_feedback->update();
            }
            
            if($success)
            {
            	$pub_feedback->set_modification_date(time());
            	$pub_feedback->update();
            }
            
            $this->redirect($success ? Translation :: get('FeedbackUpdated') : Translation :: get('FeedbackNotUpdated'), ! $success, array('pid' => $pid, 'cid' => $cid));
        
        }
        else
        {
            
            $html[] = $form->toHtml();
            return implode('\n', $html);
        }
    }

}
?>