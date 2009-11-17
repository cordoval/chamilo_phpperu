<?php
/**
 * $Id: complex_edit.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../../content_object_publication_form.class.php';

class ToolComplexEditComponent extends ToolComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $cid = Request :: get(Tool :: PARAM_COMPLEX_ID) ? Request :: get(Tool :: PARAM_COMPLEX_ID) : $_POST[Tool :: PARAM_COMPLEX_ID];
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID) ? Request :: get(Tool :: PARAM_PUBLICATION_ID) : $_POST[Tool :: PARAM_PUBLICATION_ID];
            
            $datamanager = RepositoryDataManager :: get_instance();
            $cloi = $datamanager->retrieve_complex_content_object_item($cid);
            
            //if(!WikiTool :: is_wiki_locked($cloi->get_parent()))
            {
                $cloi->set_default_property('user_id', $this->get_user_id());
                $content_object = $datamanager->retrieve_content_object($cloi->get_ref());
                $content_object->set_default_property('owner', $this->get_user_id());
                $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, Tool :: PARAM_COMPLEX_ID => $cid, Tool :: PARAM_PUBLICATION_ID => $pid, 'details' => Request :: get('details'))));
                $trail = new BreadcrumbTrail();
                if (Request :: get('tool') == 'learning_path')
                {
                    $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view_clo', 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), $datamanager->retrieve_content_object(Request :: get('pid'))->get_title()));
                    $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view_clo', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), $content_object->get_title()));
                }
                else
                {
                    $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', 'display_action' => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), RepositoryDataManager :: get_instance()->retrieve_content_object(Request :: get('pid'))->get_title()));
                    $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', 'display_action' => 'view_item', Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), $content_object->get_title()));
                }
                
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'))), Translation :: get('Edit')));
                $trail->add_help('courses general');
                
                if ($form->validate() || Request :: get('validated'))
                {
                    $form->update_content_object();
                    if ($form->is_version())
                    {
                        $cloi->set_ref($content_object->get_latest_version()->get_id());
                        $cloi->update();
                    }
                    
                    $message = htmlentities(Translation :: get('ContentObjectUpdated'));
                    
                    $params = array();
                    if (Request :: get('pid') != null)
                    {
                        $params['pid'] = Request :: get('pid');
                        $params['tool_action'] = 'view';
                    }
                    if (Request :: get('cid') != null)
                    {
                        $params['pid'] = Request :: get('pid');
                        $params['cid'] = Request :: get('cid');
                        $params['tool_action'] = 'view_item';
                    }
                    
                    if (Request :: get('details') == 1)
                    {
                        $params['cid'] = $cid;
                        $params['tool_action'] = 'view_item';
                    }
                    
                    if (Request :: get('tool') == 'wiki')
                    {
                        $params['tool_action'] = 'view';
                        $params['display_action'] = 'view_item';
                    }
                    
                    if (Request :: get('tool') == 'learning_path')
                    {
                        $params['tool_action'] = 'view_clo';
                        $params['display_action'] = 'view_item';
                    }
                    
                    $this->redirect($message, '', $params);
                
                }
                else
                {
                    $this->display_header($trail, true);
                    $form->display();
                    $this->display_footer();
                }
            }
        }
    }
}
?>