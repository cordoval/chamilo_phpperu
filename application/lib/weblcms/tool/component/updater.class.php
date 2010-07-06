<?php
/**
 * $Id: edit.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../../content_object_publication_form.class.php';

class ToolUpdaterComponent extends ToolComponent
{
    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
            
            $datamanager = WeblcmsDataManager :: get_instance();
            $publication = $datamanager->retrieve_content_object_publication($pid);
            $content_object = $publication->get_content_object();
            
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $pid)));
            
            $trail = BreadcrumbTrail :: get_instance();
            
            if (Request :: get('pcattree') > 0)
            {
                foreach (Tool :: get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
                {
                    $trail->add(new Breadcrumb($this->get_url(), $breadcrumb->get_name()));
                }
            }
            if (Request :: get('tool') == 'wiki')
            {
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => $pid)), $content_object->get_title()));
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => $pid)), $content_object->get_title()));
            }
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'edit', Tool :: PARAM_PUBLICATION_ID => $pid)), Translation :: get('Edit')));
            $trail->add_help('courses general');
            
            if ($form->validate() || Request :: get('validated'))
            {
                if (! Request :: get('validated'))
                {
                    $form->update_content_object();
	                if ($form->is_version())
	                {
	                    $publication->set_content_object_id($content_object->get_latest_version()->get_id());
	                    $publication->update();
	                }
                }
                
                $publication_form = new ContentObjectPublicationForm(ContentObjectPublicationForm :: TYPE_SINGLE, $content_object, $this, false, $this->get_parent()->get_course(), false, array(Tool :: PARAM_PUBLICATION_ID => $pid, 'validated' => 1));
                $publication_form->set_publication($publication);
                
                if ($publication_form->validate() || $content_object->get_type() == 'introduction')
                { 
                    $publication_form->update_content_object_publication();
                    $message = htmlentities(Translation :: get('ContentObjectUpdated'));
                    
                    $show_details = Request :: get('details');
                    $tool = Request :: get('tool');
                    
                    $params = array();
                    if ($show_details == 1)
                    {
                        $params[Tool :: PARAM_PUBLICATION_ID] = $pid;
                        $params[Tool :: PARAM_ACTION] = Tool :: ACTION_VIEW;
                    }
                    
                    if ($tool == 'learning_path')
                    {
                        $params[Tool :: PARAM_ACTION] = null;
                        $params['display_action'] = 'view';
                        $params[Tool :: PARAM_PUBLICATION_ID] = Request :: get(Tool :: PARAM_PUBLICATION_ID);
                    }
                    
                    if (! isset($show_details) && $tool != 'learning_path')
                    {
                        $filter = array('tool_action');
                    }
                    else
                    {
                        $filter = array();
                    }
                 
                    $this->redirect($message, false, $params, $filter);
                }
                else
                {
                    $this->display_header();
                    $publication_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $this->display_header();
                $form->display();
                $this->display_footer();
            }
        }
    }
}
?>