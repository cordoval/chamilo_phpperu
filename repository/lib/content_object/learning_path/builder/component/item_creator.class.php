<?php
/**
 * $Id: item_creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../../../../complex_builder/complex_repo_viewer.class.php';

class LearningPathBuilderItemCreatorComponent extends LearningPathBuilder
{
    private $rdm;

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository learnpath builder');
        
        $root_content_object = Request :: get(ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT);
        $complex_content_object_item_id = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $publish = Request :: get('publish');
        $type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);
        
        $this->rdm = RepositoryDataManager :: get_instance();
        
    	$parent = $root_content_object;
		if ($complex_content_object_item_id)
		{
		    $parent_complex_content_object_item = $this->rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
		    $parent = $parent_complex_content_object_item->get_ref();
		}
        
        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->rdm->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }
        
        $exclude = $this->retrieve_used_items($this->get_root_content_object()->get_id());
        $exclude[] = $this->get_root_content_object()->get_id();
        
        if (! $type)
        {
            $type = $content_object->get_allowed_types();
        }
        
        $pub = new ComplexRepoViewer($this, $type);
        if ($rtype)
        {
            $pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
        }
        
        $pub->set_parameter(ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT, $root_content_object);
        $pub->set_parameter(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_content_object_item_id);
        $pub->set_parameter('publish', $publish);
        $pub->set_excluded_objects($exclude);
        $pub->parse_input();
        
        if (!$pub->is_ready_to_be_published())
        {
            $t = is_array($type) ? implode(',', $type) : $type;
            $p = $this->rdm->retrieve_content_object($parent);
        	$html[] = '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), $t, $p->get_type(), $p->get_title()) . '</h4><br />'; 
        	$html[] = $pub->as_html();
        }
        else
        {
            $object = $pub->get_selected_objects();
            
        	if (! is_array($object))
            {
                $object = array($object);
            }
            
            $rdm = $this->rdm;
            
            foreach ($object as $obj)
            {
                $content_object = new LearningPathItem();
                $content_object->set_title(LearningPathItem :: get_type_name());
                $content_object->set_description(LearningPathItem :: get_type_name());
                $content_object->set_owner_id($this->get_user_id());
                $content_object->set_reference($obj);
                $content_object->set_parent_id(0);
                
                $content_object->create();
                
                $complex_content_object_item = ComplexContentObjectItem :: factory(LearningPathItem :: get_type_name());
                $complex_content_object_item->set_ref($content_object->get_id());
                
                $complex_content_object_item->set_parent($parent);
                $complex_content_object_item->set_display_order($rdm->select_next_display_order($parent));
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->create();
            }
            
            $this->redirect(Translation :: get('ObjectAdded'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT => $root_content_object, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, 'publish' => Request :: get('publish')));
        }
        
        $this->display_header($trail);
        echo '<br />' . implode("\n", $html);
        $this->display_footer();
    }

    private function retrieve_used_items($parent)
    {
        $items = array();
        
        $complex_content_object_items = $this->rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if ($complex_content_object_item->is_complex())
            {
                $items[] = $complex_content_object_item->get_ref();
                $items = array_merge($items, $this->retrieve_used_items($complex_content_object_item->get_ref()));
            }
        }
        
        return $items;
    }
}

?>