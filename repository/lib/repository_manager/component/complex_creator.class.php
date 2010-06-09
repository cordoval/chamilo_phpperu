<?php
/**
 * $Id: complex_creator.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */

/**
 * Repository manager component which gives the user the possibility to create a
 * new complex learning object item in his repository.
 */
class RepositoryManagerComplexCreatorComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository general');
        
        $owner = $this->get_user()->get_id();
        $ref = Request :: get(RepositoryManager :: PARAM_CLOI_REF);
        $parent = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
        $root_id = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);
        
        if (! isset($ref))
        {
            $this->display_header($trail, false, true);
            Display :: warning_message('Reference is not set');
            $this->display_footer();
        }
        
        if ($parent)
        {
            $type = RepositoryDataManager :: get_instance()->determine_content_object_type($ref);
            $cloi = ComplexContentObjectItem :: factory($type);
            
            $cloi->set_ref($ref);
            $cloi->set_user_id($owner);
            $cloi->set_parent($parent);
            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));
            
            $cloi_form = ComplexContentObjectItemForm :: factory(ComplexContentObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', $this->get_url(array(RepositoryManager :: PARAM_CLOI_REF => $ref, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, RepositoryManager :: PARAM_CLOI_ID => $parent, 'publish' => Request :: get('publish'))));
            
            if ($cloi_form)
            {
                if ($cloi_form->validate())
                {
                    $cloi_form->create_complex_content_object_item();
                    $cloi = $cloi_form->get_complex_content_object_item();
                    $root_id = $root_id ? $root_id : $cloi->get_id();
                    if ($cloi->is_complex())
                        $id = $cloi->get_ref();
                    else
                        $id = $cloi->get_parent();
                    $this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
                }
                else
                {
                    $this->display_header($trail, false, false);
                    echo '<p>' . Translation :: get('FillIn') . '</p>';
                    $cloi_form->display();
                    $this->display_footer();
                }
            }
            else
            {
                $cloi->create();
                $root_id = $root_id ? $root_id : $cloi->get_id();
                if ($cloi->is_complex())
                    $id = $cloi->get_ref();
                else
                    $id = $cloi->get_parent();
                $this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => Request :: get('publish')));
            }
        }
        else
            $this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $ref, RepositoryManager :: PARAM_CLOI_ROOT_ID => $ref, 'publish' => Request :: get('publish')));
    }
}
?>