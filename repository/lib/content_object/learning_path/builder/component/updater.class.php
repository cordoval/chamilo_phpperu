<?php
/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
//require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
//require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class LearningPathBuilderUpdaterComponent extends LearningPathBuilder
{

    function run()
    {

        $updater = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: UPDATER_COMPONENT, $this);
        $update->run();
        /* $trail = new BreadcrumbTrail();
        
        $root_content_object = Request :: get(LearningPathBuilder :: PARAM_ROOT_CONTENT_OBJECT);
        $complex_content_object_item_id = Request :: get(LearningPathBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(LearningPathBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        
        $parameters = array(LearningPathBuilder :: PARAM_ROOT_CONTENT_OBJECT => $root_content_object, LearningPathBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item, LearningPathBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, 'publish' => Request :: get('publish'));
        
        $rdm = RepositoryDataManager :: get_instance();
        $complex_content_object_item = $rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
        $content_object = $rdm->retrieve_content_object($complex_content_object_item->get_ref());
        
        $type = $content_object->get_type();
        
        $complex_content_object_item_form = ComplexContentObjectItemForm :: factory_with_type(ComplexContentObjectItemForm :: TYPE_CREATE, $type, $complex_content_object_item, 'create_complex', 'post', $this->get_url());
        
        if ($complex_content_object_item_form)
        {
            $elements = $complex_content_object_item_form->get_elements();
            $defaults = $complex_content_object_item_form->get_default_values();
        }
        
        if ($content_object->get_type() == LearningPathItem :: get_type_name())
        {
            $item_lo = $content_object;
            $content_object = $rdm->retrieve_content_object($content_object->get_reference());
        }
        
        $content_object_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url($parameters), null, $elements);
        $content_object_form->setDefaults($defaults);
        
        if ($content_object_form->validate())
        {
            $content_object_form->update_content_object();
            
            if ($content_object_form->is_version())
            {
                $new_id = $content_object->get_latest_version()->get_id();
                if ($item_lo)
                {
                    $item_lo->set_reference($new_id);
                    $item_lo->update();
                }
                else
                {
                    $complex_content_object_item->set_ref($new_id);
                }
            }
            
            if ($complex_content_object_item_form)
                $complex_content_object_item_form->update_complex_content_object_item_from_values($content_object_form->exportValues());
            else
                $complex_content_object_item->update();
            
            $parameters[LearningPathBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;
            
            $this->redirect(Translation :: get('ContentObjectUpdated'), false, array_merge($parameters, array(LearningPathBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_BROWSE_CLO, 'publish' => Request :: get('publish'))));
        }
        else
        {
            $trail = new BreadcrumbTrail();
            $trail->add_help('repository learnpath builder');
            $this->display_header($trail);
            echo $content_object_form->toHTML();
            $this->display_footer();
        }*/
    
    }
}

?>