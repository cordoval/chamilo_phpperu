<?php
require_once dirname(__FILE__) . '/../metadata_attribute_nesting.class.php';

/**
 * This class describes the form for a MetadataAttributeNesting object.
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 **/
class MetadataAssociationsForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    const PARAM_PROPERTY = 'properties';
    const PARAM_ATTRIBUTE = 'attributes';
    
    private $user;
    private $metadata_property_type;
    private $application;

    function MetadataAssociationsForm($form_type, $metadata_property_type, $action, $user, $application)
    {
        parent :: __construct('metadata_attribute_nesting_settings', 'post', $action);
        
        $this->user = $user;
        $this->metadata_property_type = $metadata_property_type;
        $this->application = $application;
        
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        //        $this->addElement('text', MetadataAttributeNesting :: PROPERTY_PARENT_ID, Translation :: get('ParentId'));
        //        $this->addRule(MetadataAttributeNesting :: PROPERTY_PARENT_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        //
        //        $this->addElement('text', MetadataAttributeNesting :: PROPERTY_CHILD_ID, Translation :: get('ChildId'));
        //        $this->addRule(MetadataAttributeNesting :: PROPERTY_CHILD_ID, Translation :: get('ThisFieldIsRequired'), 'required');
        //
        //        $this->addElement('text', MetadataAttributeNesting :: PROPERTY_CHILD_TYPE, Translation :: get('ChildType'));
        //        $this->addRule(MetadataAttributeNesting :: PROPERTY_CHILD_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');
        

        $condition = new EqualityCondition(MetadataAttributeNesting :: PROPERTY_PARENT_ID, $this->metadata_property_type->get_id());
        
        $mdm = MetadataDataManager :: get_instance();
        
        $metadata_attribute_nestings = $mdm->retrieve_metadata_attribute_nestings($condition);
        $metadata_property_nestings = $mdm->retrieve_metadata_property_nestings($condition);
        
        $defaults = array();
        $attributes = array();
        
        while ($nesting = $metadata_attribute_nestings->next_result())
        {
            $propName = ($nesting->get_child_type() == MetadataManager :: PARAM_METADATA_PROPERTY_TYPE) ? self :: PARAM_PROPERTY : self :: PARAM_ATTRIBUTE;
            $element = array();
            
            $element['classes'] = 'type type_cda_language';
            $element['id'] = $propName . '_' . $nesting->get_child_id();
            
            switch ($nesting->get_child_type())
            {
                case MetadataManager :: PARAM_METADATA_PROPERTY_TYPE :
                    $property_type = $this->application->retrieve_metadata_property_type($nesting->get_child_id());
                    $name = $property_type->get_ns_prefix() . ':' . $property_type->get_name() . ' (property)';
                    break;
                case MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE :
                    $property_attribute_type = $this->application->retrieve_metadata_property_attribute_type($nesting->get_child_id());
                    $name = $property_attribute_type->render_name() . ' (attribute)';
                    break;
            }
            $element['title'] = $name;
            $defaults[] = $element;
        }
        
        $attributes['defaults'] = $defaults;
        $attributes['exclude'] = array();
        $attributes['locale']['Display'] = 'Select attributes';
        $attributes['locale']['Error'] = 'Error';
        $attributes['locale']['NoResults'] = 'No results found';
        $attributes['locale']['Searching'] = 'Searching';
        //$attributes['options'] = '';
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'application/metadata/php/xml_feeds/xml_attributes_feed.php';
        $element_finder = $this->createElement('element_finder', MetadataAttributeNesting :: CLASS_NAME, Translation :: get('SelectAttributes'), $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']);
        $this->addElement($element_finder);
        
        $properties = array();
        
        $attributes = array();
        $defaults = array();
        
        while ($nesting = $metadata_property_nestings->next_result())
        {
            $propName = ($nesting->get_child_type() == MetadataManager :: PARAM_METADATA_PROPERTY_TYPE) ? self :: PARAM_PROPERTY : self :: PARAM_ATTRIBUTE;
            $element = array();
            
            $element['classes'] = 'type type_cda_language';
            $element['id'] = $propName . '_' . $nesting->get_child_id();
            
            switch ($nesting->get_child_type())
            {
                case MetadataManager :: PARAM_METADATA_PROPERTY_TYPE :
                    $property_type = $this->application->retrieve_metadata_property_type($nesting->get_child_id());
                    $name = $property_type->get_ns_prefix() . ':' . $property_type->get_name() . ' (property)';
                    break;
                case MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE :
                    $property_attribute_type = $this->application->retrieve_metadata_property_attribute_type($nesting->get_child_id());
                    $name = $property_attribute_type->render_name() . ' (attribute)';
                    break;
            }
            $element['title'] = $name;
            $defaults[] = $element;
        }
        
        $attributes['defaults'] = $defaults;
        $attributes['exclude'] = array();
        $attributes['locale']['Display'] = 'Select attributes';
        $attributes['locale']['Error'] = 'Error';
        $attributes['locale']['NoResults'] = 'No results found';
        $attributes['locale']['Searching'] = 'Searching';
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'application/metadata/php/xml_feeds/xml_attributes_feed.php';
        $element_finder = $this->createElement('element_finder', MetadataPropertyNesting :: CLASS_NAME, Translation :: get('SelectProperties'), $attributes['search_url'], $attributes['locale'], $attributes['defaults'], $attributes['options']);
        $this->addElement($element_finder);
    }

    function build_editing_form()
    {
        $this->build_basic_form();
        
        //$this->addElement('hidden', MetadataAttributeNesting :: PROPERTY_ID);
        

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_associations()
    {
        $fails = 0;
        $values = $this->exportValues();
        
        $this->application->delete_metadata_attribute_nestings(Utilities :: camelcase_to_underscores(MetadataAttributeNesting :: CLASS_NAME), $this->metadata_property_type);
        
        foreach ($values[MetadataAttributeNesting :: CLASS_NAME] as $type => $elements)
        {
            foreach ($elements as $id)
            {
                $metadata_attribute_nesting = new MetadataAttributeNesting();
                
                $metadata_attribute_nesting->set_parent_id($this->metadata_property_type->get_id());
                $metadata_attribute_nesting->set_child_id($id);
                $typeName = ($type == 'attributes') ? MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE : MetadataManager :: PARAM_METADATA_PROPERTY_TYPE;
                $metadata_attribute_nesting->set_child_type($typeName);
                
                if (! $metadata_attribute_nesting->create())
                    $fails ++;
            }
        
        }
        
        $this->application->delete_metadata_property_nestings(Utilities :: camelcase_to_underscores(MetadataPropertyNesting :: CLASS_NAME), $this->metadata_property_type);
        
        foreach ($values[MetadataPropertyNesting :: CLASS_NAME] as $type => $elements)
        {
            foreach ($elements as $id)
            {
                
                $metadata_property_nesting = new MetadataPropertyNesting();
                
                $metadata_property_nesting->set_parent_id($this->metadata_property_type->get_id());
                $metadata_property_nesting->set_child_id($id);
                $typeName = ($type == 'attributes') ? MetadataManager :: PARAM_METADATA_PROPERTY_ATTRIBUTE_TYPE : MetadataManager :: PARAM_METADATA_PROPERTY_TYPE;
                $metadata_property_nesting->set_child_type($typeName);
                
                if (! $metadata_property_nesting->create())
                    $fails ++;
            }
        }
        
        return ($fails < 1) ? true : false;
    }

    //    function create_metadata_attribute_nesting()
    //    {
    //    	$metadata_attribute_nesting = $this->metadata_attribute_nesting;
    //    	$values = $this->exportValues();
    //
    //    	$metadata_attribute_nesting->set_parent_id($values[MetadataAttributeNesting :: PROPERTY_PARENT_ID]);
    //    	$metadata_attribute_nesting->set_child_id($values[MetadataAttributeNesting :: PROPERTY_CHILD_ID]);
    //    	$metadata_attribute_nesting->set_child_type($values[MetadataAttributeNesting :: PROPERTY_CHILD_TYPE]);
    //
    //   		return $metadata_attribute_nesting->create();
    //    }
    

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        //		$metadata_attribute_nesting = $this->metadata_attribute_nesting;
    //
    //                $defaults[MetadataAttributeNesting :: PROPERTY_PARENT_ID] = $metadata_attribute_nesting->get_parent_id();
    //                $defaults[MetadataAttributeNesting :: PROPERTY_CHILD_ID] = $metadata_attribute_nesting->get_child_id();
    //                $defaults[MetadataAttributeNesting :: PROPERTY_CHILD_TYPE] = $metadata_attribute_nesting->get_child_type();
    //
    //		parent :: setDefaults($defaults);
    }

}
?>