<?php
/**
 * $Id: feedback_editor.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';

class ComplexDisplayComponentFeedbackEditComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $cid = Request :: get('selected_cloi') ? Request :: get('selected_cloi') : $_POST['selected_cloi'];
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
            $fid = Request :: get(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID) ? Request :: get(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID) : $_POST[ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID];
            
            $datamanager = RepositoryDataManager :: get_instance();
            $condition = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
            $feedbacks = $datamanager->retrieve_content_object_pub_feedback($condition);
            while ($feedback = $feedbacks->next_result())
            {
                $feedback_display = $datamanager->retrieve_content_object($feedback->get_feedback_id());
                $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $feedback_display, 'edit', 'post', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_EDIT_FEEDBACK, ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID => $fid, 'selected_cloi' => $cid, Tool :: PARAM_PUBLICATION_ID => $pid, 'details' => Request :: get('details'))));
                
                if ($form->validate() || Request :: get('validated'))
                {
                    $form->update_content_object();
                    /*if($form->is_version())
                    {
                        $feedback_display->set_ref($content_object->get_latest_version()->get_id());
                        $feedback_display->update();
                    }*/
                    $feedback_display->update();
                    $message = htmlentities(Translation :: get('ContentObjectFeedbackUpdated'));
                    
                    $params = array();
                    if (Request :: get('pid') != null)
                    {
                        $params['pid'] = Request :: get('pid');
                        $params['tool_action'] = 'view';
                    }
                    if ($cid != null)
                    {
                        $params['selected_cloi'] = $cid;
                        $params['tool_action'] = Request :: get('tool_action');
                        $params['display_action'] = 'discuss';
                    }
                    
                    if (Request :: get('fid') != null)
                    {
                        $params['fid'] = Request :: get('fid');
                    }
                    
                    $this->redirect($message, '', $params);
                
                }
                else
                {
                    $trail = BreadcrumbTrail :: get_instance();
                    $trail->add_help('courses general');
                    
                    $this->display_header($trail);
                    $form->display();
                    $this->display_footer();
                }
            }
        }
    }
}
?>