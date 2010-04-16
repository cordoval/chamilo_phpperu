<?php
/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
/**
 * @author Sven Vanpoucke
 */


class ComplexDisplayCreatorComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(ADD_RIGHT))
        {
            $pid = Request :: get('pid');
            $cid = Request :: get('cid');
            
            if (! $pid)
            {
                $this->display_error_message(Translation :: get('NoParentSelected'));
            }
            
            $type = Request :: get('type');
            
            $pub = new RepoViewer($this, $type, RepoViewer :: SELECT_SINGLE, array(), false);
            $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ComplexDisplay :: ACTION_CREATE);
            $pub->set_parameter('pid', $pid);
            $pub->set_parameter('cid', $cid);
            $pub->set_parameter('type', $type);
            
            
            if (!$pub->is_ready_to_be_published())
            {
                $html[] = '<p><a href="' . $this->get_url(array('type' => $type, 'pid' => $pid)) . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
                $html[] = $pub->as_html();
                $this->display_header(new BreadcrumbTrail());
                echo implode("\n", $html);
                $this->display_footer();
            }
            else
            {
                $cloi = ComplexContentObjectItem :: factory($type);
                
                $cloi->set_ref($pub->get_selected_objects());
                $cloi->set_user_id($this->get_user_id());
                $cloi->set_parent($pid);
                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($pid));
                
                $cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, 'object' => $pub->get_selected_objects())));
                
                if ($cloi_form)
                {
                    if ($cloi_form->validate() || ! $cloi->is_extended())
                    {
                        $cloi_form->create_complex_content_object_item();
                        $this->my_redirect($pid, $cid);
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
                    $this->my_redirect($pid, $cid);
                }
            }
        
        }
    }

    private function my_redirect($pid, $cid)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));
        
        $params = array();
        $params['pid'] = $pid;
        $params['cid'] = $cid;
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_CLO;
        
        $this->redirect($message, '', $params);
    }
}
?>