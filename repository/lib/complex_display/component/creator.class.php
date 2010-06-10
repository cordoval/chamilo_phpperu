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
            $complex_content_object_item_id = $this->get_complex_content_object_item_id();
            $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);

            if (!$this->get_root_content_object())
            {
                $this->display_header();
            	$this->display_error_message(Translation :: get('NoParentSelected'));
            	$this->display_footer();
                exit;
            }

            $type = Request :: get('type');

            $pub = new RepoViewer($this, $type, RepoViewer :: SELECT_SINGLE, array(), false);
            $pub->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, ComplexDisplay :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM);
            $pub->set_parameter('cid', $complex_content_object_item_id);
            $pub->set_parameter('type', $type);


            if (!$pub->is_ready_to_be_published())
            {
                $html[] = '<p><a href="' . $this->get_url(array('type' => $type)) . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt="' . Translation :: get('BrowserTitle') . '" style="vertical-align:middle;"/> ' . Translation :: get('BrowserTitle') . '</a></p>';
                $html[] = $pub->as_html();
                $this->display_header(BreadcrumbTrail :: get_instance());
                echo implode("\n", $html);
                $this->display_footer();
            }
            else
            {
                $cloi = ComplexContentObjectItem :: factory($type);

                $cloi->set_ref($pub->get_selected_objects());
                $cloi->set_user_id($this->get_user_id());

                if($complex_content_object_item_id)
                {
                	$cloi->set_parent($complex_content_object_item->get_ref());
                }
                else
                {
                	$cloi->set_parent($this->get_root_content_object()->get_id());
                }

                $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($cloi->get_ref()));

                /*$cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_CREATE_CLOI, 'object' => $pub->get_selected_objects())));

                if ($cloi_form)
                {
                    if ($cloi_form->validate() || ! $cloi->is_extended())
                    {
                        $cloi_form->create_complex_content_object_item();
                        $this->my_redirect($complex_content_object_item_id);
                    }
                    else
                    {
                        $this->display_header(new BreadcrumbTrail());
                    	$cloi_form->display();
                    	$this->display_footer();
                    }
                }
                else
                {*/
                    $cloi->create();
                    $this->my_redirect($complex_content_object_item_id);
                //}
            }

        }
    }

    private function my_redirect($complex_content_object_item_id)
    {
        $message = htmlentities(Translation :: get('ContentObjectCreated'));

        $params = array();
        $params['cid'] = $complex_content_object_item_id;
        $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT;

        $this->redirect($message, '', $params);
    }
}
?>