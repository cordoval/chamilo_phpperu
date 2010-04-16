<?php
/**
 * $Id: complex_creator.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.component
 */
require_once dirname(__FILE__) . '/../../content_object_publication_form.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';

class ToolComplexCreatorComponent extends ToolComponent
{

    function run()
    {
        if ($this->is_allowed(ADD_RIGHT))
        {
            $pid = Request :: get(Tool :: PARAM_PUBLICATION_ID);
            if (! $pid)
            {
                $this->display_header(new BreadcrumbTrail());
                $this->display_error_message(Translation :: get('NoParentSelected'));
                $this->display_footer();
            }
            else
            {
                $trail = new BreadcrumbTrail();
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'view', Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID))->get_content_object()->get_title()));
                $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, Tool :: PARAM_PUBLICATION_ID => Request :: get(Tool :: PARAM_PUBLICATION_ID), Tool :: PARAM_COMPLEX_ID => Request :: get('cid'), 'type' => Request :: get('type'))), Translation :: get('Create')));
            }
            
            $type = Request :: get('type');
            
            $pub = new ContentObjectRepoViewer($this, $type, RepoViewer :: SELECT_SINGLE);
            $pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_CREATE_CLOI);
            $pub->set_parameter(Tool :: PARAM_PUBLICATION_ID, $pid);
            $pub->set_parameter('type', $type);
            
            if (!$pub->is_ready_to_be_published())
            {
                $html[] = '<p><a href="' . $this->get_url(array('type' => $type, Tool :: PARAM_PUBLICATION_ID => $pid)) . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
                $html[] = $pub->as_html();
                $this->display_header($trail);
                echo implode("\n", $html);
                $this->display_footer();
            }
            else
            {
                $cloi = ComplexContentObjectItem :: factory($type);
                
                $cloi->set_ref($pub->get_selected_objects());
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_parent(WebLcmsDataManager :: get_instance()->retrieve_content_object_publication($pid)->get_content_object()->get_id());
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($pid));
                
                $cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, 'object' => $object_id)));
                
                if ($cloi_form)
                {
                    if ($cloi_form->validate() || ! $cloi->is_extended())
                    {
                        $cloi_form->create_complex_content_object_item();
                        $this->my_redirect($pid);
                    }
                    else
                    {
                        $this->display_header(new BreadcrumbTrail());
                        $cloi_form->display();
                        $this->display_footer();
                    }
                }
                else
                {
                    $cloi->create();
                    $this->my_redirect($pid);
                }
            }
        
        }
    }

    private function my_redirect($pid)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));
        
        $params = array();
        $params[Tool :: PARAM_PUBLICATION_ID] = $pid;
        $params['tool_action'] = 'view';
        
        $this->redirect($message, '', $params);
    }

}
?>