<?php
/**
 * $Id: metadata_lom_edit_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */
class MetadataLOMEditForm extends FormValidator
{
    const FORM_ACTION = 'lom_form_action';
    const FORM_ACTION_VALUE = 'lom_form_action_value';
    
    const FORM_ACTION_ADD_GENERAL_TITLE = 'add_title';
    const FORM_ACTION_ADD_GENERAL_IDENTIFIER = 'add_identifier';
    const FORM_ACTION_ADD_GENERAL_LANGUAGE = 'add_general_language';
    const FORM_ACTION_ADD_GENERAL_DESCRIPTION_STRING = 'add_general_description_string';
    const FORM_ACTION_ADD_LIFECYCLE_ENTITY = 'add_lifeCycle_entity';
    const FORM_ACTION_ADD_RIGHTS_DESCRIPTION = 'add_rights_description';
    
    const FORM_ACTION_SAVE = 'save';
    const FORM_ACTION_REMOVE = 'remove';
    
    const FORM_WIDTH_LARGE = 300;
    const FORM_WIDTH_MEDIUM = 200;
    const FORM_WIDTH_SMALL = 100;
    
    const LOM_GENERAL_IDENTIFIER = 'general_identifier';
    const LOM_GENERAL_TITLE = 'general_title';
    const LOM_GENERAL_LANGUAGE = 'general_language';
    const LOM_GENERAL_DESCRIPTION = 'general_description';
    const LOM_LIFECYCLE_CONTRIBUTION = 'lifeCycle_contribution';
    const LOM_LIFECYCLE_CONTRIBUTION_ENTITY = 'lifeCycle_contribution_entity';
    const LOM_RIGHTS_DESCRIPTION = 'rights_description';
    
    const MSG_FORM_HAS_UPDATE = 'msg_form_has_update';
    
    /**
     * @var IeeeLomMapper
     */
    private $ieee_lom_mapper;
    
    private $catalogs;
    private $current_values;
    private $constants;
    private $skipped_indexes;
    private $info_messages;

    public function MetadataLOMEditForm($content_object_id, $ieee_lom_mapper, $action, $catalogs)
    {
        parent :: __construct('lom_metadata', 'post', $action);
        
        /*
		 * Init 
		 */
        $this->current_values = array();
        $this->constants = array();
        $this->content_object_id = $content_object_id;
        $this->catalogs = $catalogs;
        
        /*
		 * Set the lom object for the Form.
		 * If the form was submitted, it retrieves the lom from the session
		 */
        $this->init_lom_mapper($this->content_object_id, $ieee_lom_mapper);
        $this->init_info_messages();
        
    //$this->build_editing_form();
    }

    /*************************************************************************/
    
    /*
	 * Build the form on page
	 * QuickForm will allow to automatically repopulate 
	 * submitted fields (if the form was submitted of course) 
	 */
    function build_editing_form()
    {
        $this->init_skipped_indexes();
        
        /*
		 * Do any action asked by a click on the form
		 * (e.g. add a field)
		 */
        $this->manage_form_action();
        //debug($this->skipped_indexes, 'skipped indexes');
        

        /*
		 * At this step, all the Lom document modifications 
		 * (if any requested) have been done
		 * -> save the IeeeLomMapper in session to reuse after the next form postback 
		 */
        $this->store_lom_mapper($this->content_object_id, $this->ieee_lom_mapper);
        
        /*
		 * At this step, the eventual new skipped indexes are set 
		 */
        $this->store_skipped_indexes($this->skipped_indexes);
        
        $this->disable_action_on_enter_key_pressed();
        
        //debug($this->ieee_lom_mapper->get_ieee_lom()->get_dom());
        

        $this->build_general_identifier($this->ieee_lom_mapper);
        $this->build_general_title($this->ieee_lom_mapper);
        $this->build_general_language($this->ieee_lom_mapper);
        $this->build_general_description($this->ieee_lom_mapper);
        $this->build_lifeCycle_contribution($this->ieee_lom_mapper);
        $this->build_rights_description($this->ieee_lom_mapper);
        
        $this->set_action_fields();
        $this->add_submit_buttons();
        
        /*
		 * Set original default form values
		 */
        parent :: setDefaults($this->current_values);
        
        /*
		 * Set original form values that must not change between postbacks
		 */
        parent :: setConstants($this->constants);
    }

    private function disable_action_on_enter_key_pressed()
    {
        $js = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/metadata_editor.js');
        
        $this->addElement('html', $js);
    }

    /*** General **********************************************************************/
    
    /**
     * 1.1 Identifier
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function build_general_identifier($ieee_lom_mapper)
    {
        $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMIdentifiers') . '</h3>');
        
        $data = $ieee_lom_mapper->get_identifier();
        
        //debug($data);
        

        for($index = 0; $index < count($data); $index ++)
        {
            /*
	         * General.Identifier (1 -> n)
	         */
            $show_remove_button = ($index == 0) ? false : true;
            
            if (! isset($this->skipped_indexes[self :: LOM_GENERAL_IDENTIFIER][$index]))
            {
                $catalog_metadata_id = $data[$index][IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE];
                $entry_metadata_id = $data[$index][IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE];
                $original_id = $data[$index][IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE];
                $override_id = $data[$index][IeeeLomMapper :: OVERRIDE_ID_ATTRIBUTE];
                
                $group_fields = array();
                $group_fields[] = $this->create_textfield('catalog', Translation :: translate('MetadataLomCatalog'), array('style' => 'width:' . self :: FORM_WIDTH_LARGE . 'px'));
                $group_fields[] = $this->create_textfield('entry', Translation :: translate('MetadataLomEntry'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
                
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE);
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE);
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE);
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: OVERRIDE_ID_ATTRIBUTE);
                
                if ($show_remove_button)
                {
                    //$group_fields[] = $this->createElement('image', 'remove_identifier_' . $index , Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE_GENERAL_IDENTIFIER . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
                    $group_fields[] = $this->createElement('image', 'remove_identifier_' . $index, Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE . '_' . self :: LOM_GENERAL_IDENTIFIER . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
                }
                
                $renderer = $this->defaultRenderer();
                $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', self :: LOM_GENERAL_IDENTIFIER . '[' . $index . ']');
                
                $this->addGroup($group_fields, self :: LOM_GENERAL_IDENTIFIER . '[' . $index . ']', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
                
                $rule = array();
                $rule['catalog'][] = array(Translation :: translate('MetadataLomCatalogEntryEmptyError'), 'required');
                $rule['entry'][] = array(Translation :: translate('MetadataLomCatalogEntryEmptyError'), 'required');
                $this->addGroupRule(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . ']', $rule);
                
                $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][catalog]', $data[$index]['catalog']);
                $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][entry]', $data[$index]['entry']);
                $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE . ']', $catalog_metadata_id);
                $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE . ']', $entry_metadata_id);
                $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE . ']', $original_id);
                $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: OVERRIDE_ID_ATTRIBUTE . ']', $override_id);
            }
        }
        
        /*
	     * Add the "add title" button
	     */
        $this->addElement('image', self :: FORM_ACTION_ADD_GENERAL_IDENTIFIER, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_GENERAL_IDENTIFIER . "')"));
    }

    /**
     * 1.2 Title
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function build_general_title($ieee_lom_mapper)
    {
        $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMTitles') . '</h3>');
        
        $titles = $ieee_lom_mapper->get_titles();
        $strings = $titles->get_strings();
        
        //debug($strings);
        

        for($index = 0; $index < count($strings); $index ++)
        {
            /*
	         * General.Title (1 -> n)
	         */
            $show_remove_button = ($index == 0) ? false : true;
            
            if (! isset($this->skipped_indexes[self :: LOM_GENERAL_TITLE][$index]))
            {
                $string_metadata_id = $strings[$index][IeeeLomLangStringMapper :: STRING_METADATA_ID];
                $language_metadata_id = $strings[$index][IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID];
                $string_original_id = $strings[$index][IeeeLomLangStringMapper :: STRING_ORIGINAL_ID];
                $string_override_id = $strings[$index][IeeeLomLangStringMapper :: STRING_OVERRIDE_ID];
                
                $this->add_lang_string(self :: LOM_GENERAL_TITLE, $index, Translation :: translate('MetadataLOMTitle'), true, $show_remove_button);
                
                $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][string]', $strings[$index]['string']);
                $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][language]', $strings[$index]['language']);
                $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $string_metadata_id);
                $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $language_metadata_id);
                $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . ']', $string_original_id);
                $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $string_override_id);
            }
        }
        
        /*
	     * Add the "add title" button
	     */
        $this->addElement('image', self :: FORM_ACTION_ADD_GENERAL_TITLE, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_GENERAL_TITLE . "')"));
    }

    /**
     * 1.3 Language
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function build_general_language($ieee_lom_mapper)
    {
        $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMGeneralLanguage') . '</h3>');
        
        $data = $ieee_lom_mapper->get_general_languages();
        
        //debug($data);
        

        $showed_element_total = 0;
        for($index = 0; $index < count($data); $index ++)
        {
            /*
	         * General.Language (0 -> n)
	         */
            $show_remove_button = true;
            
            if (! isset($this->skipped_indexes[self :: LOM_GENERAL_LANGUAGE][$index]))
            {
                $showed_element_total ++;
                
                $metadata_id = $data[$index][IeeeLomMapper :: METADATA_ID_ATTRIBUTE];
                $original_id = $data[$index][IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE];
                $override_id = $data[$index][IeeeLomMapper :: OVERRIDE_ID_ATTRIBUTE];
                
                $group_fields = array();
                $group_fields[] = $this->createElement('select', 'language', null, $this->get_catalog(Catalog :: CATALOG_LOM_LANGUAGE, false));
                
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_ATTRIBUTE);
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE);
                $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: OVERRIDE_ID_ATTRIBUTE);
                
                if ($show_remove_button)
                {
                    $group_fields[] = $this->createElement('image', 'remove_identifier_' . $index, Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE . '_' . self :: LOM_GENERAL_LANGUAGE . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
                }
                
                $this->addGroup($group_fields, self :: LOM_GENERAL_LANGUAGE . '[' . $index . ']');
                
                $this->set_current_value(self :: LOM_GENERAL_LANGUAGE . '[' . $index . '][language]', $data[$index]['language']);
                $this->set_current_value(self :: LOM_GENERAL_LANGUAGE . '[' . $index . '][' . IeeeLomMapper :: METADATA_ID_ATTRIBUTE . ']', $metadata_id);
                $this->set_current_value(self :: LOM_GENERAL_LANGUAGE . '[' . $index . '][' . IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE . ']', $original_id);
                $this->set_current_value(self :: LOM_GENERAL_LANGUAGE . '[' . $index . '][' . IeeeLomMapper :: OVERRIDE_ID_ATTRIBUTE . ']', $override_id);
            }
        }
        
        if ($showed_element_total == 0)
        {
            $this->addElement('html', '<div class="row"><div class="formw">' . Translation :: translate('MetadataLOMGeneralLanguageNotDefined') . '</div></div>');
        }
        
        /*
	     * Add the "add title" button
	     */
        $this->addElement('image', self :: FORM_ACTION_ADD_GENERAL_LANGUAGE, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_GENERAL_LANGUAGE . "')"));
    }

    /**
     * 1.4 Description
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function build_general_description($ieee_lom_mapper)
    {
        $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMDescriptions') . '</h3>');
        
        $string_mappers = $ieee_lom_mapper->get_descriptions();
        
        //debug($string_mappers);
        

        for($index = 0; $index < count($string_mappers); $index ++)
        {
            $string_mapper = $string_mappers[$index];
            
            for($string_index = 0; $string_index < count($string_mapper->get_strings()); $string_index ++)
            {
                /*
    	         * General.Description (1 -> n)
    	         */
                $show_remove_button = ($string_index == 0) ? false : true;
                
                if (! isset($this->skipped_indexes[self :: LOM_GENERAL_DESCRIPTION][$index][$string_index]))
                {
                    $group_name = self :: LOM_GENERAL_DESCRIPTION . '[' . $index . ']';
                    
                    $this->add_lang_string($group_name, $string_index, Translation :: translate('MetadataLOMDescription'), true, $show_remove_button, true);
                    
                    $this->set_current_value($group_name . '[' . $string_index . '][string]', $string_mapper->get_string($string_index));
                    $this->set_current_value($group_name . '[' . $string_index . '][language]', $string_mapper->get_language($string_index));
                    $this->set_current_value($group_name . '[' . $string_index . '][' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $string_mapper->get_string_metadata_id($string_index));
                    $this->set_current_value($group_name . '[' . $string_index . '][' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $string_mapper->get_lang_metadata_id($string_index));
                    $this->set_current_value($group_name . '[' . $string_index . '][' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $string_mapper->get_string_override_id($string_index));
                    $this->set_current_value($group_name . '[' . $string_index . '][' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . ']', $string_mapper->get_string_original_id($string_index));
                }
            }
            
            /*
    	     * Add the "add description string" button
    	     */
            $this->addElement('image', self :: FORM_ACTION_ADD_GENERAL_DESCRIPTION_STRING, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_GENERAL_DESCRIPTION_STRING . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
        }
    }

    /*** Life Cycle *******************************************************************/
    
    /**
     * 2.3 Contribution
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function build_lifeCycle_contribution($ieee_lom_mapper)
    {
        $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMContribution') . '</h3>');
        
        $data = $ieee_lom_mapper->get_lifeCycle_contribution();
        //debug($data);
        

        /*
	     * Create contributions
	     */
        for($index = 0; $index < count($data); $index ++)
        {
            //debug($data[$index]['entity'][0]->getValue('FN'));
            //debug($data[$index]['role'], 'ROLE');
            //debug($data[$index]);
            

            $group_name = self :: LOM_LIFECYCLE_CONTRIBUTION;
            
            $this->addElement('hidden', $group_name . '[' . $index . '][contribution_override_id]');
            $this->set_current_value($group_name . '[' . $index . '][contribution_override_id]', $data[$index]['contribution_override_id']);
            
            $this->addElement('hidden', $group_name . '[' . $index . '][contribution_original_id]');
            $this->set_current_value($group_name . '[' . $index . '][contribution_original_id]', $data[$index]['contribution_original_id']);
            
            /*
	    	 * Create role for contribution
	    	 */
            $this->add_role($group_name, $index);
            $this->set_current_value($group_name . '[' . $index . '][role]', $data[$index]['role']);
            $this->set_current_value($group_name . '[' . $index . '][' . IeeeLomMapper :: METADATA_ID_ROLE_ATTRIBUTE . ']', $data[$index]['role_metadata_id']);
            $this->set_current_value($group_name . '[' . $index . '][role_original_id]', $data[$index]['role_original_id']);
            $this->set_current_value($group_name . '[' . $index . '][role_override_id]', $data[$index]['role_override_id']);
            
            /*
	    	 * Create entities for contribution
	    	 */
            $this->addElement('html', '<div class="row"><div class="formw"><h5>Entity</h5></div></div>');
            $tot_entity = count($data[$index]['entity']) > 0 ? count($data[$index]['entity']) : 1; //show at least one entity
            for($entity_index = 0; $entity_index < $tot_entity; $entity_index ++)
            {
                if (! isset($this->skipped_indexes[self :: LOM_LIFECYCLE_CONTRIBUTION_ENTITY][$index][$entity_index]))
                {
                    $group_name_entity = $group_name . '[' . $index . '][entity]';
                    $entity = $data[$index]['entity'][$entity_index];
                    
                    $show_remove_button = ($entity_index == 0) ? false : true;
                    
                    //debug($entity);
                    

                    $this->add_entity($group_name_entity, $index, $entity_index, $show_remove_button);
                    
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][name]', $entity['name']['value']);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][email]', $entity['email']['value']);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][organisation]', $entity['organisation']['value']);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][name_metadata_id]', isset($entity['name']['name_metadata_id']) ? $entity['name']['name_metadata_id'] : DataClass :: NO_UID);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][email_metadata_id]', isset($entity['email']['email_metadata_id']) ? $entity['email']['email_metadata_id'] : DataClass :: NO_UID);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][organisation_metadata_id]', isset($entity['organisation']['organisation_metadata_id']) ? $entity['organisation']['organisation_metadata_id'] : DataClass :: NO_UID);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][override_id]', isset($entity['entity_override_id']) ? $entity['entity_override_id'] : DataClass :: NO_UID);
                    $this->set_current_value($group_name_entity . '[' . $entity_index . '][entity_original_id]', isset($entity['entity_original_id']) ? $entity['entity_original_id'] : DataClass :: NO_UID);
                }
            }
            
            /*
    	     * Add the "add entity" button
    	     */
            $this->addElement('image', self :: FORM_ACTION_ADD_LIFECYCLE_ENTITY, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_LIFECYCLE_ENTITY . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
            
            /*
	    	 * Add date for contribution
	    	 */
            $this->add_datetime($group_name, $index, $data[$index]['date']);
            $this->set_current_value($group_name . '[' . $index . '][date][' . IeeeLomMapper :: METADATA_ID_DATE_ATTRIBUTE . ']', $data[$index]['date_metadata_id']);
            $this->set_current_value($group_name . '[' . $index . '][date][date_original_id]', $data[$index]['date_original_id']);
            $this->set_current_value($group_name . '[' . $index . '][date][date_override_id]', $data[$index]['date_override_id']);
        }
    }

    /*** Rights ***********************************************************************/
    
    /**
     * 6.3 Description
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function build_rights_description($ieee_lom_mapper)
    {
        $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMRights') . '</h3>');
        
        $descriptions = $ieee_lom_mapper->get_rights_description();
        $strings = $descriptions->get_strings();
        
        //debug($strings);
        

        $showed_element_total = 0;
        for($index = 0; $index < count($strings); $index ++)
        {
            if (! isset($this->skipped_indexes[self :: LOM_RIGHTS_DESCRIPTION][$index]))
            {
                $showed_element_total ++;
                
                $string_metadata_id = $strings[$index][IeeeLomLangStringMapper :: STRING_METADATA_ID];
                $language_metadata_id = $strings[$index][IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID];
                //$string_original_id   = $strings[$index][IeeeLomLangStringMapper :: STRING_ORIGINAL_ID];
                $string_override_id = $strings[$index][IeeeLomLangStringMapper :: STRING_OVERRIDE_ID];
                
                $this->add_lang_string(self :: LOM_RIGHTS_DESCRIPTION, $index, Translation :: translate('MetadataLOMRightsDescription'), true, true, true);
                
                $this->set_current_value(self :: LOM_RIGHTS_DESCRIPTION . '[' . $index . '][string]', $strings[$index]['string']);
                $this->set_current_value(self :: LOM_RIGHTS_DESCRIPTION . '[' . $index . '][language]', $strings[$index]['language']);
                $this->set_current_value(self :: LOM_RIGHTS_DESCRIPTION . '[' . $index . '][' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $string_metadata_id);
                $this->set_current_value(self :: LOM_RIGHTS_DESCRIPTION . '[' . $index . '][' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $language_metadata_id);
                $this->set_current_value(self :: LOM_RIGHTS_DESCRIPTION . '[' . $index . '][' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $string_override_id);
            }
        }
        
        if ($showed_element_total == 0)
        {
            $this->addElement('html', '<div class="row"><div class="formw">' . Translation :: translate('MetadataLOMRightsDescriptionNotDefined') . '</div></div>');
        }
        
        /*
	     * Add the "add title" button
	     */
        $this->addElement('image', self :: FORM_ACTION_ADD_RIGHTS_DESCRIPTION, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_RIGHTS_DESCRIPTION . "')"));
    }

    /*************************************************************************/
    
    /**
     * Add hidden fields allowing to send back to the server the action 
     * that has to be done on the form 
     * (e.g. add / remove a field)
     */
    private function set_action_fields()
    {
        $this->addElement('hidden', self :: FORM_ACTION, null, array('id' => self :: FORM_ACTION));
        $this->addElement('hidden', self :: FORM_ACTION_VALUE, null, array('id' => self :: FORM_ACTION_VALUE));
        
        $this->constants[self :: FORM_ACTION] = '';
        $this->constants[self :: FORM_ACTION_VALUE] = '';
    }

    /**
     * Add the submit button to the form
     */
    private function add_submit_buttons()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update', 'onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_SAVE . "')"));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /*************************************************************************/
    
    /**
     * Treats an action made on the form
     */
    private function manage_form_action()
    {
        $action = Request :: post(self :: FORM_ACTION);
        
        //debug($action);
        $result = false;
        
        if (String :: start_with($action, self :: FORM_ACTION_REMOVE))
        {
            $result = $this->manage_remove_action($action);
        }
        else
        {
            switch ($action)
            {
                case self :: FORM_ACTION_ADD_GENERAL_IDENTIFIER :
                    $result = $this->add_identifier();
                    break;
                
                case self :: FORM_ACTION_ADD_GENERAL_TITLE :
                    $result = $this->add_title();
                    break;
                
                case self :: FORM_ACTION_ADD_GENERAL_LANGUAGE :
                    $result = $this->add_general_language();
                    break;
                
                case self :: FORM_ACTION_ADD_GENERAL_DESCRIPTION_STRING :
                    $result = $this->add_description_string();
                    break;
                
                case self :: FORM_ACTION_ADD_LIFECYCLE_ENTITY :
                    $result = $this->add_lifeCycle_entity();
                    break;
                
                case self :: FORM_ACTION_ADD_RIGHTS_DESCRIPTION :
                    $result = $this->add_rights_description();
                    break;
            }
        }
        
        if ($result)
        {
            $this->add_info_message(self :: MSG_FORM_HAS_UPDATE);
        }
    }

    private function manage_remove_action($action)
    {
        $concern = substr($action, strlen(self :: FORM_ACTION_REMOVE . '_'));
        if (stripos($concern, '['))
        {
            $concern = substr($concern, 0, stripos($concern, '['));
        }
        
        switch ($concern)
        {
            case self :: LOM_GENERAL_IDENTIFIER :
                return $this->remove_identifier();
                break;
            
            case self :: LOM_GENERAL_TITLE :
                return $this->remove_title();
                break;
            
            case self :: LOM_GENERAL_LANGUAGE :
                return $this->remove_general_language();
                break;
            
            case self :: LOM_GENERAL_DESCRIPTION :
                return $this->remove_description_string();
                break;
            
            case self :: LOM_LIFECYCLE_CONTRIBUTION_ENTITY :
                return $this->remove_lifeCycle_entity();
                break;
            
            case self :: LOM_RIGHTS_DESCRIPTION :
                return $this->remove_rights_description();
                break;
        }
    }

    /**
     * Add a blank identifier in the form
     */
    public function add_identifier($catalog = '', $identifier = '')
    {
        $this->ieee_lom_mapper->add_general_identifier($catalog, $identifier);
        
        return true;
    }

    /**
     * Remove a clicked identifier from the form
     */
    private function remove_identifier()
    {
        $action_value = Request :: post(self :: FORM_ACTION_VALUE);
        
        if (isset($action_value) && is_numeric($action_value))
        {
            $this->skipped_indexes[self :: LOM_GENERAL_IDENTIFIER][$action_value] = true;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Add a blank title in the form
     */
    public function add_title($text = '', $lang = '')
    {
        $this->ieee_lom_mapper->add_general_title(new IeeeLomLangStringMapper($text, $lang), - 1);
        
        return true;
    }

    /**
     * Remove a clicked title from the form
     */
    private function remove_title()
    {
        $action_value = Request :: post(self :: FORM_ACTION_VALUE);
        
        $title_index = String :: get_value_between_chars($action_value, 0);
        
        if (isset($title_index) && is_numeric($title_index))
        {
            $this->skipped_indexes[self :: LOM_GENERAL_TITLE][$title_index] = true;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Add a blank language in the form
     */
    public function add_general_language($lang = '')
    {
        $this->ieee_lom_mapper->add_general_language($lang);
        
        return true;
    }

    /**
     * Add a blank language in the form
     */
    private function remove_general_language()
    {
        $action_value = Request :: post(self :: FORM_ACTION_VALUE);
        
        if (isset($action_value) && is_numeric($action_value))
        {
            $this->skipped_indexes[self :: LOM_GENERAL_LANGUAGE][$action_value] = true;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
	 * Add a blank description
	 */
    public function add_description_string($text = '', $lang = '')
    {
        $this->ieee_lom_mapper->add_general_description_string(new IeeeLomLangStringMapper($text, $lang));
        
        return true;
    }

    /**
     * Remove a clicked description from the form
     */
    private function remove_description_string()
    {
        $action_value = Request :: post(self :: FORM_ACTION_VALUE);
        
        //debug($action_value);
        

        $description_index = String :: get_value_between_chars($action_value, 0);
        $string_index = String :: get_value_between_chars($action_value, 1);
        
        if (isset($description_index) && is_numeric($description_index) && isset($string_index) && is_numeric($string_index))
        {
            $this->skipped_indexes[self :: LOM_GENERAL_DESCRIPTION][$description_index][$string_index] = true;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Add a blank entity in the form
     */
    public function add_lifeCycle_entity($contribute_index = null, $entity_value = '')
    {
        if (! isset($contribute_index))
        {
            $contribute_index = Request :: post(self :: FORM_ACTION_VALUE);
        }
        
        $this->ieee_lom_mapper->add_lifeCycle_entity($contribute_index, '');
        
        return true;
    }

    /**
     * Remove a clicked entity from the form
     */
    private function remove_lifeCycle_entity()
    {
        $action_value = Request :: post(self :: FORM_ACTION_VALUE);
        
        $contribute_index = String :: get_value_between_chars($action_value, 0);
        $entity_index = String :: get_value_between_chars($action_value, 1);
        
        if (isset($contribute_index) && is_numeric($contribute_index) && isset($entity_index) && is_numeric($entity_index))
        {
            $this->skipped_indexes[self :: LOM_LIFECYCLE_CONTRIBUTION_ENTITY][$contribute_index][$entity_index] = true;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /*
	 * Add rights description in the form
	 */
    public function add_rights_description($text = '', $lang = '')
    {
        $this->ieee_lom_mapper->add_rights_description(new IeeeLomLangStringMapper($text, $lang), - 1);
        
        return true;
    }

    /*
	 * Remove a clicked rights description from the form
	 */
    private function remove_rights_description()
    {
        $action_value = Request :: post(self :: FORM_ACTION_VALUE);
        
        $rd_index = String :: get_value_between_chars($action_value, 0);
        
        if (isset($rd_index) && is_numeric($rd_index))
        {
            $this->skipped_indexes[self :: LOM_RIGHTS_DESCRIPTION][$rd_index] = true;
            
            return true;
        }
        else
        {
            return false;
        }
    }

    /*************************************************************************/
    
    /**
     * Add a textfield and a combobox for the language
     * 
     * @param string $group_name
     * @param integer $index
     * @param string $label
     * @param bool $with_lang_empty_value
     * @param bool $show_remove_button
     * @param integer $use_textarea
     * @return void
     */
    private function add_lang_string($group_name, $index, $label, $with_lang_empty_value = true, $show_remove_button = false, $use_textarea = false)
    {
        //debug($group_name);
        

        $group_fields = array();
        
        if ($use_textarea)
        {
            $group_fields[] = $this->createElement('textarea', 'string', $label, array('style' => 'width:' . self :: FORM_WIDTH_LARGE . 'px'));
        }
        else
        {
            $group_fields[] = $this->create_textfield('string', $label, array('style' => 'width:' . self :: FORM_WIDTH_LARGE . 'px'));
        }
        
        $group_fields[] = $this->createElement('select', 'language', null, $this->get_catalog(Catalog :: CATALOG_LOM_LANGUAGE));
        $group_fields[] = $this->createElement('hidden', IeeeLomLangStringMapper :: STRING_METADATA_ID);
        $group_fields[] = $this->createElement('hidden', IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID);
        $group_fields[] = $this->createElement('hidden', IeeeLomLangStringMapper :: STRING_ORIGINAL_ID);
        $group_fields[] = $this->createElement('hidden', IeeeLomLangStringMapper :: STRING_OVERRIDE_ID);
        
        if ($show_remove_button)
        {
            $group_fields[] = $this->createElement('image', 'remove_' . $group_name . '_' . $index, Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE . '_' . $group_name . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $group_name . '[' . $index . "]')"));
            $colspan_error = 5;
        }
        else
        {
            $colspan_error = 3;
        }
        
        $table_header = array();
        $table_header[] = '<div class="row"><div class="formw"><table padding="0" cellspacing="0">';
        $this->addElement('html', implode("\n", $table_header));
        
        $renderer = $this->defaultRenderer();
        $renderer->setElementTemplate('<!-- BEGIN error --><tr><td></td><td colspan="' . $colspan_error . '"><span class="form_error">{error}</span></td></tr><!-- END error --><tr>{element}</tr>', $group_name . '[' . $index . ']');
        $renderer->setGroupElementTemplate('<td style="vertical-align:top"><!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label}</td><td style="vertical-align:top">{element}</td>', $group_name . '[' . $index . ']');
        
        $this->addGroup($group_fields, $group_name . '[' . $index . ']', null, '');
        
        $table_footer[] = '</table></div></div>';
        $this->addElement('html', implode("\n", $table_footer));
        
        $rule = array();
        $rule['string'][] = array(Translation :: translate('MetadataLOMTextEmptyError'), 'required');
        $this->addGroupRule($group_name . '[' . $index . ']', $rule);
    }

    private function add_entity($group_name, $contribute_index, $entity_index, $show_remove_button)
    {
        $group_fields = array();
        $group_fields[] = $this->create_textfield('name', Translation :: translate('MetadataLomContriName'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
        $group_fields[] = $this->create_textfield('email', Translation :: translate('MetadataLomContriEmail'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
        $group_fields[] = $this->create_textfield('organisation', Translation :: translate('MetadataLomContriOrg'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
        
        $group_fields[] = $this->createElement('hidden', 'name_metadata_id');
        $group_fields[] = $this->createElement('hidden', 'email_metadata_id');
        $group_fields[] = $this->createElement('hidden', 'organisation_metadata_id');
        $group_fields[] = $this->createElement('hidden', 'override_id');
        $group_fields[] = $this->createElement('hidden', 'entity_original_id');
        
        if ($show_remove_button)
        {
            $group_fields[] = $this->createElement('image', 'remove_entity_' . $entity_index, Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE . '_' . self :: LOM_LIFECYCLE_CONTRIBUTION_ENTITY . "');$('#" . self :: FORM_ACTION_VALUE . "').val('[" . $contribute_index . '][' . $entity_index . "]')"));
        }
        
        $renderer = $this->defaultRenderer();
        $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $entity_index . ']');
        
        $this->addGroup($group_fields, $group_name . '[' . $entity_index . ']', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        
        $rule = array();
        $rule['name'][] = array(Translation :: translate('MetadataLomCatalogNameEmptyError'), 'required');
        $this->addGroupRule($group_name . '[' . $entity_index . ']', $rule);
    }

    private function add_role($group_name, $index)
    {
        //debug($group_name);
        

        $group_fields = array();
        $group_fields[] = $this->createElement('html', '<div class="row"><div class="formw"><h5>Role</h5></div></div>');
        $group_fields[] = $this->createElement('select', 'role', Translation :: translate('MetadataLomRole'), $this->get_catalog(Catalog :: CATALOG_LOM_ROLE, false));
        
        $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_ROLE_ATTRIBUTE);
        $group_fields[] = $this->createElement('hidden', 'role_original_id');
        $group_fields[] = $this->createElement('hidden', 'role_override_id');
        
        $renderer = $this->defaultRenderer();
        $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $index . ']');
        
        $this->addGroup($group_fields, $group_name . '[' . $index . ']', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        
        $rule = array();
        $rule['role'][] = array(Translation :: translate('MetadataLomRoleEmptyError'), 'required');
        $this->addGroupRule($group_name . '[' . $index . ']', $rule);
    }

    private function add_datetime($group_name, $index, $date_value)
    {
        $group_fields = array();
        $group_fields[] = $this->createElement('html', '<div class="row"><div class="formw"><h5>Date</h5></div></div>');
        $group_fields[] = $this->createElement('select', 'day', Translation :: translate('MetadataDay'), $this->get_catalog(Catalog :: CATALOG_DAY));
        $group_fields[] = $this->createElement('select', 'month', Translation :: translate('MetadataMonth'), $this->get_catalog(Catalog :: CATALOG_MONTH));
        $group_fields[] = $this->createElement('select', 'year', Translation :: translate('MetadataYear'), $this->get_catalog(Catalog :: CATALOG_YEAR));
        $group_fields[] = $this->createElement('select', 'hour', Translation :: translate('MetadataHour'), $this->get_catalog(Catalog :: CATALOG_HOUR));
        $group_fields[] = $this->createElement('select', 'min', Translation :: translate('MetadataMin'), $this->get_catalog(Catalog :: CATALOG_MIN));
        $group_fields[] = $this->createElement('select', 'sec', Translation :: translate('MetadataSec'), $this->get_catalog(Catalog :: CATALOG_SEC));
        
        $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_DATE_ATTRIBUTE);
        $group_fields[] = $this->createElement('hidden', 'date_original_id');
        $group_fields[] = $this->createElement('hidden', 'date_override_id');
        
        $renderer = $this->defaultRenderer();
        $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $index . '][date]');
        
        $this->addGroup($group_fields, $group_name . '[' . $index . '][date]', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        
        $this->registerRule('datetime', 'function', 'validate_date_time', __CLASS__);
        $this->addRule(array($group_name . '[' . $index . '][date]'), Translation :: translate('MetadataLomDateInvalid'), 'datetime');
        
        $lom_dt = new IeeeLomDateTime();
        $lom_dt->set_datetime_from_string($date_value);
        
        $this->set_current_value($group_name . '[' . $index . '][date][day]', $lom_dt->get_day(true));
        $this->set_current_value($group_name . '[' . $index . '][date][month]', $lom_dt->get_month(true));
        $this->set_current_value($group_name . '[' . $index . '][date][year]', $lom_dt->get_year(true));
        $this->set_current_value($group_name . '[' . $index . '][date][hour]', $lom_dt->get_hour(true));
        $this->set_current_value($group_name . '[' . $index . '][date][min]', $lom_dt->get_min(true));
        $this->set_current_value($group_name . '[' . $index . '][date][sec]', $lom_dt->get_sec(true));
    }

    public function validate_date_time($date_array)
    {
        //debug($date_array);
        if (isset($date_array[0]))
        {
            $date_array = $date_array[0];
        }
        
        /*
	     * If one element of the date is given, all are mandatory
	     */
        $day_exist = (strlen($date_array['day']) > 0);
        $month_exist = (strlen($date_array['month']) > 0);
        $year_exist = (strlen($date_array['year']) > 0);
        
        if (($day_exist || $month_exist || $year_exist) && (! $day_exist || ! $month_exist || ! $year_exist))
        {
            return false;
        }
        
        /*
	     * If one element of the time is given, all are mandatory
	     */
        $hour_exist = (strlen($date_array['hour']) > 0);
        $min_exist = (strlen($date_array['min']) > 0);
        $sec_exist = (strlen($date_array['sec']) > 0);
        
        if (($hour_exist || $min_exist || $sec_exist) && (! $hour_exist || ! $min_exist || ! $sec_exist))
        {
            return false;
        }
        
        return true;
    }

    /**
     * Get a list of values to fill a combobox
     * 
     * @param string $catalog_name
     * @param bool $with_empty_value
     * @return array
     */
    private function get_catalog($catalog_name, $with_empty_value = true)
    {
        if ($with_empty_value)
        {
            return array('' => '') + $this->catalogs[$catalog_name];
        }
        else
        {
            return $this->catalogs[$catalog_name];
        }
    }

    /*************************************************************************/
    
    /**
     * Set the current value of a field
     * 
     * @param string $field_name
     * @param string $value
     * @param bool $override_existing Indicates wether an already existing value must be overriden
     * @return void
     */
    public function set_current_value($field_name, $value, $override_existing = false)
    {
        if (! isset($this->current_values[$field_name]) || $override_existing)
        {
            $this->current_values[$field_name] = $value;
        }
    }

    /**
     * Set the constant value of a field.
     * Useful for instance to set the value of generated Id of new metadata records 
     * 
     * @param string $field_name
     * @param string $value
     * @param bool $override_existing Indicates wether an already existing value must be overriden
     * @return void
     */
    public function set_constant_value($field_name, $value, $override_existing = false)
    {
        if (! isset($this->constants[$field_name]) || $override_existing)
        {
            $this->constants[$field_name] = $value;
        }
    }

    /**
     * Set the constant values of many fields. 
     * The $constant_values array must contains arrays with two keys 'name' and 'value':
     * 
     * [0] => 	
     * 			[name] 	=> 	name
     * 			[value]	=>	value
     * [1] =>
     * 			...
     * 
     * @param array $constant_values
     * @param bool $override_existing
     * @return void
     */
    public function set_constant_values($constant_values, $override_existing = false)
    {
        foreach ($constant_values as $constant)
        {
            $this->set_constant_value($constant['name'], $constant['value'], $override_existing);
        }
        
        parent :: setConstants($this->constants);
    }

    /*************************************************************************/
    
    /**
     * 
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return unknown_type
     */
    public function set_lom_mapper($ieee_lom_mapper)
    {
        $this->ieee_lom_mapper = $ieee_lom_mapper;
    }

    /**
     * Init the IeeeLomMapper for the form. 
     * If the form was posted, it tries to get it from the session.
     * If the request is not a postback, it returns the given IeeeLomMapper
     * 
     * @param $content_object_id integer
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function init_lom_mapper($content_object_id, $ieee_lom_mapper)
    {
        if ($this->isSubmitted())
        {
            $this->ieee_lom_mapper = $this->get_lom_mapper_from_session($content_object_id);
        }
        else
        {
            $ieeeLom = $ieee_lom_mapper->get_ieee_lom(); //force the load of the inner IeeeLom if it doesn't exist yet
            $this->ieee_lom_mapper = $ieee_lom_mapper;
        }
    }

    /**
     * @param $content_object_id integer
     * @param $ieee_lom_mapper IeeeLomMapper
     * @return void
     */
    private function store_lom_mapper($content_object_id, $ieee_lom_mapper)
    {
        $_SESSION['ieee_lom_mapper_' . $content_object_id] = $ieee_lom_mapper->get_ieee_lom()->get_dom()->saveXML();
        $this->ieee_lom_mapper = $ieee_lom_mapper;
    }

    private function get_lom_mapper_from_session($content_object_id)
    {
        $dom_lom = new DOMDocument();
        $dom_lom->loadXML($_SESSION['ieee_lom_mapper_' . $content_object_id]);
        
        $ieee_lom_mapper = new IeeeLomMapper($content_object_id);
        $ieee_lom_mapper->set_ieee_lom(new IeeeLom($dom_lom));
        $this->ieee_lom_mapper = $ieee_lom_mapper;
        
        return $this->ieee_lom_mapper;
    }

    /**
     * Init the info_messages array for the form
     * If the form was posted, it tries to get it from the session.
     * 
     * @return void
     */
    private function init_info_messages($content_object_id)
    {
        if ($this->isSubmitted())
        {
            $this->info_messages = $_SESSION['ieeeLom_info_messages_' . $content_object_id];
        }
        else
        {
            $_SESSION['ieeeLom_info_messages_' . $content_object_id] = array();
            $this->info_messages = $_SESSION['ieeeLom_info_messages_' . $content_object_id];
        }
    }

    /**
     * Add a message that must be displayed when the form is posted back 
     * 
     * @param string $key The key of the message. Allow to store the same message only once.
     * @param string $message
     * @return void
     */
    public function add_info_message($key, $message)
    {
        if (! array_key_exists($key, $this->info_messages))
        {
            switch ($key)
            {
                case self :: MSG_FORM_HAS_UPDATE :
                    $this->info_messages[$key] = Translation :: translate('MetadataApplyWillBeDoneAfterClickUpdate');
                    break;
                
                default :
                    $this->info_messages[$key] = $message;
                    break;
            }
            
            $_SESSION['ieeeLom_info_messages_' . $this->content_object_id] = $this->info_messages;
        }
    }

    /**
     * 
     * @return array The array of messages
     */
    public function get_info_messages()
    {
        return $this->info_messages;
    }

    /**
     * Init the array containing the skipped indexes. 
     * If the form was posted, it tries to get it from the session.
     * 
     * Note: the skipped_indexes array allow to hide some fields after a delete button has been clicked, 
     * before all the updates are committed to the datasource.
     * 
     * @param string $key The key of the message. Allow to store the same message only once.
     * @param string $message
     * @return void
     */
    private function init_skipped_indexes()
    {
        if ($this->isSubmitted())
        {
            $skipped_indexes = $this->get_skipped_indexes_from_session();
            $this->skipped_indexes = (isset($skipped_indexes)) ? $skipped_indexes : array();
        }
        else
        {
            $this->skipped_indexes = array();
        }
    }

    private function store_skipped_indexes($skipped_indexes)
    {
        $_SESSION['skipped_indexes_' . $this->content_object_id] = $skipped_indexes;
    }

    private function get_skipped_indexes_from_session()
    {
        return $_SESSION['skipped_indexes_' . $this->content_object_id];
    }

    /*************************************************************************/
    
    /**
     * Display the info messages and the form itself
     * 
     * @see chamilo/common/html/formvalidator/FormValidator#display()
     */
    public function display()
    {
        if (count($this->info_messages) > 0)
        {
            $messages = '<ul>';
            
            foreach ($this->info_messages as $message)
            {
                $messages .= '<li>' . $message . '</li>';
            }
            
            $messages .= '</ul>';
            
            Display :: normal_message($messages);
        }
        
        parent :: display();
    }

    /*
	 * Indicates wether the form is submitted and is valid
	 */
    public function must_save()
    {
        $action = Request :: post(self :: FORM_ACTION);
        
        if ($action == self :: FORM_ACTION_SAVE && $this->validate())
        {
            $this->info_messages = array();
            
            return true;
        }
        else
        {
            return false;
        }
    }

}
?>