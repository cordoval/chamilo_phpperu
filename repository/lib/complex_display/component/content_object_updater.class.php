<?php
/**
 * $Id: content_object_updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.assessment.component
 */
class ComplexDisplayContentObjectUpdaterComponent extends ComplexDisplayComponent
{

    function run()
    {
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $pid = Request :: get('pid') ? Request :: get('pid') : $_POST['pid'];

            $datamanager = RepositoryDataManager :: get_instance();
            $content_object = $datamanager->retrieve_content_object($pid);

            $content_object->set_default_property(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
            $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_UPDATE_LO, 'selected_cloi' => $selected_cloi, 'selected_cloi' => $cid, 'pid' => $pid)));

            if ($form->validate() || Request :: get('validated'))
            {
                $form->update_content_object();

                $message = htmlentities(Translation :: get('ContentObjectUpdated'));

                $params = array();
                $params['pid'] = Request :: get('pid');
                $params['tool_action'] = Request :: get('tool_action');
                $params[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ComplexDisplay :: ACTION_VIEW_CLO;

                $this->redirect($message, '', $params);

            }
            else
            {
                $this->display_header(new BreadcrumbTrail());
            	$form->display();
            	$this->display_footer();
            }
        }
    }
}
?>