<?php
/**
 * $Id: wiki_pub_feedback_editor.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */
require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';

class WikiDisplayWikiPubFeedbackEditorComponent extends WikiDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $cid = Request :: get('selected_cloi') ? Request :: get('selected_cloi') : $_POST['selected_cloi'];
            $wiki_publication_id = $this->get_root_lo();
            $fid = Request :: get(WikiPubFeedback :: PROPERTY_FEEDBACK_ID) ? Request :: get(WikiPubFeedback :: PROPERTY_FEEDBACK_ID) : $_POST[WikiPubFeedback :: PROPERTY_FEEDBACK_ID];

            $datamanager = WikiDataManager :: get_instance();
            $condition = new EqualityCondition(WikiPubFeedback :: PROPERTY_FEEDBACK_ID, $fid);
            $feedbacks = $datamanager->retrieve_wiki_pub_feedbacks($condition);
            
            $feedback = $feedbacks->next_result();
            
                $feedback_display = RepositoryDataManager :: get_instance()->retrieve_content_object($feedback->get_feedback_id());
                $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $feedback_display, 'edit', 'post', 
                	$this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_EDIT_FEEDBACK, 
                					     WikiPubFeedback :: PROPERTY_FEEDBACK_ID => $fid, 'selected_cloi' => $cid, 'details' => Request :: get('details'))));
                
                if ($form->validate() || Request :: get('validated'))
                {
                    $form->update_content_object();
                    
                	if ($form->is_version())
	                {
	                    $new_id = $feedback_display->get_latest_version()->get_id();
	                    $feedback->set_feedback_id($new_id);
	                    $feedback->update();
	                }
                    
                    $feedback_display->update();
                    $message = htmlentities(Translation :: get('ContentObjectFeedbackUpdated'));
                    
                    $params = array();
                    if (Request :: get('wiki_publication_id') != null)
                    {
                        $params['wiki_publication_id'] = Request :: get('wiki_publication_id');
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
                    $trail = new BreadcrumbTrail();
                    $trail->add_help('courses general');
                     
                    $this->display_header($trail);
                    $form->display();
					$this->display_footer();
                }
        }
    }
}
?>