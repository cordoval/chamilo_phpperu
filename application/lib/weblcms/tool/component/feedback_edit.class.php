<?php
/**
 * $Id: feedback_edit.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../../content_object_publication_form.class.php';

class ToolFeedbackEditComponent extends ToolComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $cid = Request :: get(Tool :: PARAM_COMPLEX_ID) ? Request :: get(Tool :: PARAM_COMPLEX_ID) : $_POST[Tool :: PARAM_COMPLEX_ID];
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
            $fid = Request :: get(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID) ? Request :: get(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID) : $_POST[ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID];
            
            $datamanager = RepositoryDataManager :: get_instance();
            $condition = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
            $feedbacks = $datamanager->retrieve_content_object_pub_feedback($condition);
            while ($feedback = $feedbacks->next_result())
            {
                $feedback_display = $datamanager->retrieve_content_object($feedback->get_feedback_id());
                $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $feedback_display, 'edit', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_FEEDBACK, ContentObjectPubFeedback :: PROPERTY_FEEDBACK_ID => $fid, Tool :: PARAM_COMPLEX_ID => $cid, Tool :: PARAM_PUBLICATION_ID => $pid, 'details' => Request :: get('details'))));
                
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
                    
                    switch (Request :: get('tool'))
                    {
                        case 'learning_path' :
                            $tool_action = 'view_clo';
                            $display_action = 'discuss';
                            break;
                        case 'wiki' :
                            $tool_action = 'view';
                            $display_action = 'discuss';
                            break;
                        default :
                            $tool_action = 'discuss';
                            break;
                    }
                    
                    $params = array();
                    if (Request :: get(Tool :: PARAM_PUBLICATION_ID) != null)
                    {
                        $params[Tool :: PARAM_PUBLICATION_ID] = Request :: get(Tool :: PARAM_PUBLICATION_ID);
                        $params['tool_action'] = 'view';
                    }
                    if (Request :: get('cid') != null)
                    {
                        $params['cid'] = Request :: get('cid');
                        $params['tool_action'] = $tool_action;
                        $params['display_action'] = $display_action; //'discuss';
                    }
                    
                    if (Request :: get('fid') != null)
                    {
                        $params['fid'] = Request :: get('fid');
                    }
                    
                    if (Request :: get('details') == 1)
                    {
                        $params['cid'] = $cid;
                        $params['tool_action'] = 'discuss';
                    }
                    $this->redirect($message, '', $params);
                
                }
                else
                {
                    $trail = new BreadcrumbTrail();
                    $trail->add_help('courses general');
                    
                    $this->display_header($trail, true);
                    $form->display();
                    $this->display_footer();
                }
            }
        }
    }
}
?>