<?php
/**
 * $Id: ieee_lom_mapper.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
require_once dirname(__FILE__) . '/ieee_lom_langstring_mapper.class.php';
require_once dirname(__FILE__) . '/ieee_lom_default_metadata_generator.class.php';
require_once ('File/Contact_Vcard_Parse.php');
require_once ('File/Contact_Vcard_Build.php');

class IeeeLomMapper extends MetadataMapper
{
    const METADATA_ID_CATALOG_ATTRIBUTE = 'catalog_metadata_id';
    const METADATA_ID_LANG_ATTRIBUTE = 'lang_metadata_id';
    const METADATA_ID_ENTRY_ATTRIBUTE = 'entry_metadata_id';
    const METADATA_ID_ROLE_ATTRIBUTE = 'role_metadata_id';
    const METADATA_ID_ENTITY_ATTRIBUTE = 'entity_metadata_id';
    const METADATA_ID_DATE_ATTRIBUTE = 'date_metadata_id';
    
    private $ieeeLom;
    
    /*
     * Useful to store the new generated id when a new element is saved that 
     * will then be set as constant on the Lom form
     */
    private $constant_values;

    /**
     * 
     * @param mixed $content_object Id of a content_object or a content_object instance
     */
    function IeeeLomMapper($content_object)
    {
        parent :: MetadataMapper($content_object);
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    function set_ieee_lom($ieeeLom)
    {
        $this->ieeeLom = $ieeeLom;
    }

    /**
     * @return IeeeLom
     */
    function get_ieee_lom()
    {
        if (! isset($this->ieeeLom))
        {
            $this->get_metadata();
        }
        
        return $this->ieeeLom;
    }

    function get_metadata()
    {
        /*
	     * Get the IeeeLom object with default values from the datasource
	     */
        $generator = new IeeeLomDefaultMetadataGenerator();
        $generator->set_content_object($this->content_object);
        $this->ieeeLom = $generator->generate();
        
        /*
		 * Add technical datasource infos to the ieeeLom object to allow 
		 * adding /merge of additional metadata  
		 */
        $this->decorate_document_with_content_object_id($this->ieeeLom, $this->content_object->get_id());
        
        //debug($this->ieeeLom->get_dom());
        

        /*
		 * Add the metadata defined in the additional metadata datasource table 
		 */
        $this->additional_metadata_array = $this->retrieve_content_object_additional_metadata($this->content_object);
        $this->merge_additional_metadata($this->additional_metadata_array);
        
        //debug($this->ieeeLom->get_dom());
        

        return $this->ieeeLom;
    }

    function export_metadata($format_for_html_page = false, $return_document = false)
    {
        $ieeeLom = $this->get_metadata();
        
        $xsl = new DOMDocument();
        $xsl->load(dirname(__FILE__) . '/lom_export.xsl');
        
        $proc = new XSLTProcessor();
        $proc->importStylesheet($xsl);
        
        $doc = null;
        if ($format_for_html_page)
        {
            $doc = htmlspecialchars($proc->transformToXML($ieeeLom->get_dom()));
        }
        else
        {
            $doc = $proc->transformToXML($ieeeLom->get_dom());
        }
        
        if ($return_document)
        {
            return $doc;
        }
        else
        {
            echo $doc;
        }
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    public function decorate_document_with_content_object_id($ieeeLom, $content_object_id)
    {
        $this->decorate_general_identifier($ieeeLom, $content_object_id);
        $this->decorate_general_title($ieeeLom, $content_object_id);
        $this->decorate_general_description($ieeeLom, $content_object_id);
        
        $this->decorate_lifeCycle_contribution($ieeeLom, $content_object_id);
        
    //$this->decorate_rights_copyright($ieeeLom, $content_object_id);
    }

    public function decorate_general_identifier($ieeeLom, $content_object_id)
    {
        $nodes = $ieeeLom->get_identifier();
        foreach ($nodes as $node)
        {
            $node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $content_object_id);
        }
    }

    public function decorate_general_title($ieeeLom, $content_object_id)
    {
        $title_nodes = $ieeeLom->get_titles();
        foreach ($title_nodes as $title)
        {
            $title->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $content_object_id);
        }
    }

    /**
     * @param $ieeeLom IeeeLom
     * @param $content_object_id integer
     * @return void
     */
    public function decorate_general_language($ieeeLom, $content_object_id)
    {
        $language_nodes = $ieeeLom->get_languages();
        
        if (isset($language_nodes))
        {
            foreach ($language_nodes as $language)
            {
                $language->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $content_object_id);
            }
        }
    }

    public function decorate_general_description($ieeeLom, $content_object_id)
    {
        $description_nodes = $ieeeLom->get_descriptions();
        
        if (isset($description_nodes))
        {
            foreach ($description_nodes as $description)
            {
                foreach ($description->childNodes as $string)
                {
                    $string->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $content_object_id);
                }
            }
        }
    }

    public function decorate_lifeCycle_contribution($ieeeLom, $content_object_id)
    {
        $nodes = $ieeeLom->get_contribute();
        
        foreach ($nodes as $node)
        {
            //$node->setAttribute('contribution_override_id', $content_object_id);
            $node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $content_object_id);
            
            //role
            $role_node = XMLUtilities :: get_first_element_by_tag_name($node, 'role');
            if (isset($role_node))
            {
                $role_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $content_object_id);
            }
            
            //entity
            //Note: default entity is only one node -> we set the first
            $entity_node = XMLUtilities :: get_first_element_by_tag_name($node, 'entity');
            if (isset($entity_node))
            {
                $entity_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $content_object_id);
            }
            
            //date
            $date_node = XMLUtilities :: get_first_element_by_tag_name($node, 'date');
            if (isset($date_node))
            {
                $date_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $content_object_id);
            }
        }
        
    //debug($nodes);
    }

    public function decorate_rights_copyright($ieeeLom, $content_object_id)
    {
        $copyright_node = $ieeeLom->get_copyright_and_other_restrictions();
        if (isset($copyright_node))
        {
            $copyright_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $content_object_id);
        }
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    public function merge_additional_metadata($additional_metadata)
    {
        if (isset($additional_metadata) && is_array($additional_metadata) && count($additional_metadata) > 0)
        {
            $this->additional_metadata = $additional_metadata;
            $this->merge_general();
            $this->merge_lifeCycle();
            $this->merge_rights();
            
        //TODO: add other IeeeLom sections here 
        }
    }

    private function merge_general()
    {
        $this->merge_general_identifier();
        $this->merge_general_title();
        $this->merge_general_language();
        $this->merge_general_description();
        
    //debug($this->ieeeLom->get_dom());
    }

    private function merge_lifeCycle()
    {
        $this->merge_lifeCycle_contribution();
    }

    public function merge_rights()
    {
        $this->merge_rights_description();
    }

    //1.1 Identifier------------------------------------------------------------
    private function merge_general_identifier()
    {
        $metadata_array = $this->get_additional_metadata(MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[');
        
        //debug($metadata_array);
        

        $datas = array();
        
        foreach ($metadata_array as $content_object_metadata)
        {
            $metadata_id = $content_object_metadata->get_id();
            $index = StringUtilities :: get_value_between_chars($content_object_metadata->get_property());
            $concern = StringUtilities :: get_value_between_chars($content_object_metadata->get_property(), 1);
            $value = $content_object_metadata->get_value();
            $override_id = $content_object_metadata->get_override_id();
            
            if (! isset($datas[$index]))
            {
                $datas[$index] = array();
            }
            
            if (! isset($datas[$index][$concern]))
            {
                $datas[$index][$concern] = array();
            }
            
            if (isset($override_id))
            {
                /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
                XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/general/identifier[@' . self :: ORIGINAL_ID_ATTRIBUTE . '=' . $override_id . ']');
            }
            
            $datas[$index][$concern]['value'] = $value;
            $datas[$index][$concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
            $datas[$index][$concern][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
        }
        ksort($datas);
        
        foreach ($datas as $data)
        {
            //debug($data);
            

            $catalog = isset($data['catalog']['value']) ? $data['catalog']['value'] : '';
            $catalog_metadata_id = isset($data['catalog'][self :: METADATA_ID_ATTRIBUTE]) ? $data['catalog'][self :: METADATA_ID_ATTRIBUTE] : '';
            
            $entry = isset($data['entry']['value']) ? $data['entry']['value'] : '';
            $entry_metadata_id = isset($data['entry'][self :: METADATA_ID_ATTRIBUTE]) ? $data['entry'][self :: METADATA_ID_ATTRIBUTE] : '';
            
            $catalog_override_id = isset($data['catalog'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $data['catalog'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] : '';
            $entry_override_id = isset($data['entry'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $data['entry'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] : '';
            
            $this->add_general_identifier($catalog, $entry, $catalog_metadata_id, $entry_metadata_id, $catalog_override_id, $entry_override_id);
        }
        
    //debug($this->get_dom(true));
    }

    public function add_general_identifier($catalog, $entry, $catalog_metadata_id = DataClass :: NO_UID, $entry_metadata_id = DataClass :: NO_UID, $catalog_override_id = DataClass :: NO_UID, $entry_override_id = DataClass :: NO_UID)
    {
        $new_node = $this->ieeeLom->add_identifier($catalog, $entry);
        
        if (isset($new_node))
        {
            $new_node->setAttribute(self :: METADATA_ID_CATALOG_ATTRIBUTE, $catalog_metadata_id);
            $new_node->setAttribute(self :: METADATA_ID_ENTRY_ATTRIBUTE, $entry_metadata_id);
            $new_node->setAttribute(self :: METADATA_OVERRIDE_ID, $catalog_override_id);
        }
        
        return $new_node;
    }

    public function get_identifier()
    {
        $identifier_nodes = $this->ieeeLom->get_identifier();
        
        $identifiers = array();
        foreach ($identifier_nodes as $identifier)
        {
            $catalog = XMLUtilities :: get_first_element_value_by_tag_name($identifier, IeeeLom :: CATALOG);
            $entry = XMLUtilities :: get_first_element_value_by_tag_name($identifier, IeeeLom :: ENTRY);
            
            $ident_array = array();
            $ident_array[IeeeLom :: CATALOG] = $catalog;
            $ident_array[IeeeLom :: ENTRY] = $entry;
            $ident_array[self :: METADATA_ID_CATALOG_ATTRIBUTE] = XMLUtilities :: get_attribute($identifier, self :: METADATA_ID_CATALOG_ATTRIBUTE, DataClass :: NO_UID);
            $ident_array[self :: METADATA_ID_ENTRY_ATTRIBUTE] = XMLUtilities :: get_attribute($identifier, self :: METADATA_ID_ENTRY_ATTRIBUTE, DataClass :: NO_UID);
            ;
            $ident_array[self :: ORIGINAL_ID_ATTRIBUTE] = XMLUtilities :: get_attribute($identifier, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
            ;
            ;
            $ident_array[self :: OVERRIDE_ID_ATTRIBUTE] = XMLUtilities :: get_attribute($identifier, self :: OVERRIDE_ID_ATTRIBUTE, DataClass :: NO_UID);
            ;
            ;
            
            $identifiers[] = $ident_array;
        }
        
        return $identifiers;
    }

    //1.2 Title-----------------------------------------------------------------
    private function merge_general_title()
    {
        $langstring_mappers = $this->get_lang_strings_to_merge(MetadataLomEditForm :: LOM_GENERAL_TITLE, '/lom/general/title');
        //debug($langstring_mapper->get_strings());
        $this->add_general_title($langstring_mappers[0]);
        
        foreach ($langstring_mappers[0]->get_strings() as $index => $string)
        {
            $string_override_id = $langstring_mappers[0]->get_string_override_id($index);
            if (isset($string_override_id) && $string_override_id != DataClass :: NO_UID)
            {
                /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
                XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/general/title/string[@' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . '=' . $string_override_id . ']');
                
                //Delete eventual empty description node
                XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/general/title[not(node())]');
            
            }
        }
    }

    /**
     * 
     * @param $langstring_mapper IeeeLomLangStringMapper
     * @return unknown_type
     */
    public function add_general_title($langstring_mapper)
    {
        $new_string_nodes = $this->ieeeLom->add_title($langstring_mapper);
        
        if (isset($new_string_nodes))
        {
            foreach ($new_string_nodes as $index => $string_node)
            {
                $string_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $langstring_mapper->get_string_metadata_id($index));
                $string_node->setAttribute(self :: METADATA_ID_LANG_ATTRIBUTE, $langstring_mapper->get_lang_metadata_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, $langstring_mapper->get_string_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, $langstring_mapper->get_lang_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $langstring_mapper->get_string_original_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_ORIGINAL_ID, $langstring_mapper->get_lang_original_id($index));
            }
        }
        
        return $new_string_nodes;
    }

    /**
     * @return IeeeLomLangStringMapper
     */
    public function get_titles()
    {
        //debug($this->ieeeLom->get_dom());
        

        $title_nodes = $this->ieeeLom->get_titles();
        
        $langstrings = new IeeeLomLangStringMapper();
        
        //debug($title_nodes);
        

        foreach ($title_nodes as $title)
        {
            $string_metadata_id = XMLUtilities :: get_attribute($title, self :: METADATA_ID_ATTRIBUTE, DataClass :: NO_UID);
            $language_metadata_id = XMLUtilities :: get_attribute($title, self :: METADATA_ID_LANG_ATTRIBUTE, DataClass :: NO_UID);
            $string_override_id = XMLUtilities :: get_attribute($title, IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, DataClass :: NO_UID);
            $language_override_id = XMLUtilities :: get_attribute($title, IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, DataClass :: NO_UID);
            $string_original_id = XMLUtilities :: get_attribute($title, IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, DataClass :: NO_UID);
            
            $langstrings->add_string($title->nodeValue, $title->getAttribute('language'), $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id);
        }
        
        return $langstrings;
    }

    //1.3 Language--------------------------------------------------------------
    private function merge_general_language()
    {
        $metadata_array = $this->get_additional_metadata(MetadataLomEditForm :: LOM_GENERAL_LANGUAGE . '[');
        
        $datas = array();
        
        foreach ($metadata_array as $content_object_metadata)
        {
            $metadata_id = $content_object_metadata->get_id();
            $index = StringUtilities :: get_value_between_chars($content_object_metadata->get_property());
            $value = $content_object_metadata->get_value();
            $override_id = $content_object_metadata->get_override_id();
            
            if (! isset($datas[$index]))
            {
                $datas[$index] = array();
            }
            
            if (isset($override_id))
            {
                /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
                XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/general/language[@' . self :: ORIGINAL_ID_ATTRIBUTE . '=' . $override_id . ']');
            }
            
            $datas[$index]['value'] = $value;
            $datas[$index][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
            $datas[$index][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
        }
        ksort($datas);
        
        foreach ($datas as $data)
        {
            //debug($data);
            

            $lang_code = isset($data['value']) ? $data['value'] : '';
            $lang_metadata_id = isset($data[self :: METADATA_ID_ATTRIBUTE]) ? $data[self :: METADATA_ID_ATTRIBUTE] : DataClass :: NO_UID;
            $lang_override_id = isset($data[ContentObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $data[ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] : DataClass :: NO_UID;
            
            $this->add_general_language($lang_code, $lang_metadata_id, $lang_override_id);
        }
    
    }

    /**
     * 
     * @param $lang_code string 
     * @param $lang_metadata_id integer
     * @param $lang_override_id integer
     * @return DOMNode The new added node
     */
    public function add_general_language($lang_code, $lang_metadata_id = DataClass :: NO_UID, $lang_override_id = DataClass :: NO_UID)
    {
        $new_node = $this->ieeeLom->add_language($lang_code);
        
        //debug($new_node);
        

        if (isset($new_node))
        {
            $new_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $lang_metadata_id);
            $new_node->setAttribute(self :: METADATA_OVERRIDE_ID, $lang_override_id);
        }
        
        return $new_node;
    }

    public function get_general_languages()
    {
        $language_nodes = $this->ieeeLom->get_languages();
        
        $languages = array();
        foreach ($language_nodes as $language)
        {
            //debug($language);
            

            $lang_array = array();
            $lang_array['language'] = $language->nodeValue;
            $lang_array[self :: METADATA_ID_ATTRIBUTE] = XMLUtilities :: get_attribute($language, self :: METADATA_ID_ATTRIBUTE, DataClass :: NO_UID);
            $lang_array[self :: ORIGINAL_ID_ATTRIBUTE] = XMLUtilities :: get_attribute($language, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
            $lang_array[self :: OVERRIDE_ID_ATTRIBUTE] = XMLUtilities :: get_attribute($language, self :: OVERRIDE_ID_ATTRIBUTE, DataClass :: NO_UID);
            
            $languages[] = $lang_array;
        }
        
        return $languages;
    }

    //1.4 Description-----------------------------------------------------------
    private function merge_general_description()
    {
        $langstring_mappers = $this->get_lang_strings_to_merge(MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION, '/lom/general/description');
        //debug($langstring_mappers);
        

        foreach ($langstring_mappers as $langstring_mapper)
        {
            $this->add_general_description($langstring_mapper);
            
            foreach ($langstring_mapper->get_strings() as $index => $string)
            {
                $string_override_id = $langstring_mapper->get_string_override_id($index);
                if (isset($string_override_id) && $string_override_id != DataClass :: NO_UID)
                {
                    /*
    	    	     * Remove the original node corresponding to the same id 
    	    	     */
                    XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/general/description/string[@' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . '=' . $string_override_id . ']');
                    
                    //Delete eventual empty description node
                    XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/general/description[not(node())]');
                
                }
            }
        }
        
    //debug($this->ieeeLom->get_dom());
    }

    public function add_general_description($langstring_mapper)
    {
        //debug($langstring_mapper->get_strings());
        $new_string_nodes = $this->ieeeLom->add_description($langstring_mapper);
        //debug($new_string_nodes);
        

        if (isset($new_string_nodes))
        {
            foreach ($new_string_nodes as $index => $string_node)
            {
                $string_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $langstring_mapper->get_string_metadata_id($index));
                $string_node->setAttribute(self :: METADATA_ID_LANG_ATTRIBUTE, $langstring_mapper->get_lang_metadata_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, $langstring_mapper->get_string_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, $langstring_mapper->get_lang_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $langstring_mapper->get_string_original_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_ORIGINAL_ID, $langstring_mapper->get_lang_original_id($index));
            }
        }
        
        return $new_string_nodes;
    }

    public function add_general_description_string($langstring_mapper)
    {
        $new_string_nodes = $this->ieeeLom->add_description_string($langstring_mapper);
        
        if (isset($new_string_nodes))
        {
            foreach ($new_string_nodes as $index => $string_node)
            {
                $string_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $langstring_mapper->get_string_metadata_id($index));
                $string_node->setAttribute(self :: METADATA_ID_LANG_ATTRIBUTE, $langstring_mapper->get_lang_metadata_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, $langstring_mapper->get_string_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, $langstring_mapper->get_lang_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $langstring_mapper->get_string_original_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_ORIGINAL_ID, $langstring_mapper->get_lang_original_id($index));
            }
        }
    }

    public function get_descriptions()
    {
        $description_nodes = $this->ieeeLom->get_descriptions();
        
        //debug($description_nodes);
        

        $langstrings = array();
        
        foreach ($description_nodes as $description)
        {
            $langstring = new IeeeLomLangStringMapper();
            
            $strings = $description->childNodes;
            
            //debug($strings, get_class($strings));
            

            foreach ($strings as $string)
            {
                if (is_a($string, 'DOMElement'))
                {
                    //debug($string, get_class($string));
                    

                    $string_metadata_id = XMLUtilities :: get_attribute($string, self :: METADATA_ID_ATTRIBUTE, DataClass :: NO_UID);
                    $language_metadata_id = XMLUtilities :: get_attribute($string, self :: METADATA_ID_LANG_ATTRIBUTE, DataClass :: NO_UID);
                    $string_override_id = XMLUtilities :: get_attribute($string, IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, DataClass :: NO_UID);
                    $language_override_id = XMLUtilities :: get_attribute($string, IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, DataClass :: NO_UID);
                    $string_original_id = XMLUtilities :: get_attribute($string, IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, DataClass :: NO_UID);
                    
                    $langstring->add_string($string->nodeValue, $string->getAttribute('language'), $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id);
                }
            }
            
            $langstrings[] = $langstring;
        }
        
        return $langstrings;
    }

    //2.3 Contribution----------------------------------------------------------
    private function merge_lifeCycle_contribution()
    {
        $metadata_array = $this->get_additional_metadata(MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[');
        //debug($metadata_array);
        

        //debug($this->ieeeLom->get_dom());
        

        $contributions = array();
        
        foreach ($metadata_array as $content_object_metadata)
        {
            //debug($content_object_metadata);
            

            $metadata_id = $content_object_metadata->get_id();
            $index = StringUtilities :: get_value_between_chars($content_object_metadata->get_property());
            $concern = StringUtilities :: get_value_between_chars($content_object_metadata->get_property(), 1);
            $value = $content_object_metadata->get_value();
            $override_id = $content_object_metadata->get_override_id();
            
            //debug($override_id);
            

            if (! isset($contributions[$index]))
            {
                $contributions[$index] = array();
            }
            
            if (! isset($contributions[$index][$concern]))
            {
                $contributions[$index][$concern] = array();
            }
            
            if (isset($override_id))
            {
                /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
                //XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/lifeCycle/contribute[@contribution_override_id=' . $override_id . ']');
                XMLUtilities :: delete_element_by_xpath($this->ieeeLom->get_dom(), '/lom/lifeCycle/contribute[@original_id=' . $override_id . ']');
            }
            
            if ($concern == 'entity')
            {
                $entity_index = StringUtilities :: get_value_between_chars($content_object_metadata->get_property(), 2);
                $entity_concern = StringUtilities :: get_value_between_chars($content_object_metadata->get_property(), 3);
                
                $contributions[$index][$concern][$entity_index][$entity_concern]['value'] = $value;
                $contributions[$index][$concern][$entity_index][$entity_concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
                $contributions[$index][$concern][$entity_index][$entity_concern][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
            }
            else
            {
                $contributions[$index][$concern]['value'] = $value;
                $contributions[$index][$concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
                $contributions[$index][$concern][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
            }
        }
        ksort($contributions);
        
        foreach ($contributions as $contribution)
        {
            $this->add_lifeCycle_contribution($contribution['role']['value'], $contribution['role']['metadata_id'], $contribution['role']['override_id'], DataClass :: NO_UID, $contribution['entity'], $contribution['date']['value'], $contribution['date']['metadata_id'], $contribution['date']['override_id'], DataClass :: NO_UID);
        }
        
    //debug($this->ieeeLom->get_dom());
    }

    public function add_lifeCycle_contribution($role_value, $role_metadata_id, $role_override_id, $role_original_id, $entity_values, $date_value, $date_metadata_id, $date_override_id, $date_original_id)
    {
        //        debug($role_value, '$role_value'); 
        //        debug($role_metadata_id, '$role_metadata_id'); 
        //        debug($role_override_id, '$role_override_id');
        //        debug($role_original_id, '$role_original_id');
        //        debug($entity_values, '$entity_values');
        //        debug($date_value, '$date_value');
        //        debug($date_metadata_id, '$date_metadata_id'); 
        //        debug($date_override_id, '$date_override_id'); 
        //        debug($date_original_id, '$date_original_id');
        

        $voc_role = new IeeeLomVocabulary($role_value);
        $lom_dt = new IeeeLomDatetime();
        $lom_dt->set_datetime_from_string($date_value);
        
        $entities = array();
        foreach ($entity_values as $entity)
        {
            $vcard = new Contact_Vcard_Build();
            $vcard->setFormattedName($entity['name']['value']);
            $vcard->setName($entity['name']['value'], null, null, null, null);
            $vcard->addEmail($entity['email']['value']);
            $vcard->addOrganization($entity['organisation']['value']);
            
            $entities[] = $vcard->fetch();
        }
        
        $new_node = $this->ieeeLom->add_contribute($voc_role, $entities, $lom_dt);
        
        if (isset($new_node))
        {
            $role_node = XMLUtilities :: get_first_element_by_tag_name($new_node, 'role');
            if (isset($role_node))
            {
                $role_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $role_metadata_id);
                $role_node->setAttribute(self :: OVERRIDE_ID_ATTRIBUTE, $role_override_id);
                $role_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $role_original_id);
            }
            
            $entity_nodes = XMLUtilities :: get_all_element_by_xpath($new_node, '/lom/lifeCycle/contribute/entity');
            if (isset($entity_nodes))
            {
                foreach ($entity_nodes as $index => $entity_node)
                {
                    $entity_node->setAttribute('name_metadata_id', $entity_values[$index]['name']['metadata_id']);
                    $entity_node->setAttribute('email_metadata_id', $entity_values[$index]['email']['metadata_id']);
                    $entity_node->setAttribute('organisation_metadata_id', $entity_values[$index]['organisation']['metadata_id']);
                    
                    $entity_node->setAttribute('name_override_id', $entity_values[$index]['name']['override_id']);
                    $entity_node->setAttribute('email_override_id', $entity_values[$index]['email']['override_id']);
                    $entity_node->setAttribute('organisation_override_id', $entity_values[$index]['organisation']['override_id']);
                    
                    $entity_node->setAttribute('name_original_id', $entity_values[$index]['name']['original_id']);
                    $entity_node->setAttribute('email_original_id', $entity_values[$index]['email']['original_id']);
                    $entity_node->setAttribute('organisation_original_id', $entity_values[$index]['organisation']['original_id']);
                    
                    /*
                     * The general original id is set as the name_original_id value, as if the name is an original value, email and org are also original
                     */
                    $entity_node->setAttribute('original_id', $entity_values[$index]['name']['original_id']);
                }
            }
            
            $date_node = XMLUtilities :: get_first_element_by_tag_name($new_node, 'date');
            if (isset($date_node))
            {
                $date_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $date_metadata_id);
                $date_node->setAttribute(self :: OVERRIDE_ID_ATTRIBUTE, $date_override_id);
                $date_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $date_original_id);
            }
        }
    }

    public function get_lifeCycle_contribution()
    {
        //$this->debug_dom();
        

        $contribution_nodes = $this->ieeeLom->get_contribute();
        
        $contributions = array();
        foreach ($contribution_nodes as $contribution)
        {
            //debug($contribution);
            

            $contribution_array = array();
            
            $role = XMLUtilities :: get_first_element_by_xpath($contribution, '/lom/lifeCycle/contribute/role');
            $entities = XMLUtilities :: get_all_element_by_xpath($contribution, '/lom/lifeCycle/contribute/entity');
            $date = XMLUtilities :: get_first_element_by_xpath($contribution, '/lom/lifeCycle/contribute/date');
            
            $contribution_array['contribution_override_id'] = XMLUtilities :: get_attribute($role, self :: OVERRIDE_ID_ATTRIBUTE, DataClass :: NO_UID);
            $contribution_array['contribution_original_id'] = XMLUtilities :: get_attribute($role, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
            
            foreach ($entities as $entity_index => $entity)
            {
                $vcard_parser = new Contact_Vcard_Parse();
                $vcard_array = $vcard_parser->fromText($entity->nodeValue);
                
                $vcard = new Contact_Vcard_Build();
                $vcard->setFromArray($vcard_array[0]);
                
                $contribution_array['entity'][$entity_index]['name']['value'] = $vcard->getValue('FN');
                $contribution_array['entity'][$entity_index]['name']['name_metadata_id'] = XMLUtilities :: get_attribute($entity, 'name_metadata_id', DataClass :: NO_UID); //$entity->getAttribute('name_metadata_id');
                $contribution_array['entity'][$entity_index]['email']['value'] = $vcard->getValue('EMAIL');
                $contribution_array['entity'][$entity_index]['email']['email_metadata_id'] = XMLUtilities :: get_attribute($entity, 'email_metadata_id', DataClass :: NO_UID); //$entity->getAttribute('email_metadata_id');
                $contribution_array['entity'][$entity_index]['organisation']['value'] = $vcard->getValue('ORG');
                $contribution_array['entity'][$entity_index]['organisation']['organisation_metadata_id'] = XMLUtilities :: get_attribute($entity, 'organisation_metadata_id', DataClass :: NO_UID); //$entity->getAttribute('organisation_metadata_id');
                $contribution_array['entity'][$entity_index]['entity_original_id'] = XMLUtilities :: get_attribute($entity, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
                
                /*
	             * Takes the name_override_id as general override_id, as if the name is an override, the other entries infos are an override as well
	             */
                $contribution_array['entity'][$entity_index]['entity_override_id'] = XMLUtilities :: get_attribute($entity, 'name_override_id', DataClass :: NO_UID);
            }
            
            if (isset($role))
            {
                $contribution_array['role'] = XMLUtilities :: get_first_element_value_by_relative_xpath($role, '/role/value');
                $contribution_array[self :: METADATA_ID_ROLE_ATTRIBUTE] = XMLUtilities :: get_attribute($role, self :: METADATA_ID_ATTRIBUTE, DataClass :: NO_UID);
                $contribution_array['role_original_id'] = XMLUtilities :: get_attribute($role, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
            }
            
            if (isset($date))
            {
                $contribution_array['date'] = $date->nodeValue;
                $contribution_array[self :: METADATA_ID_DATE_ATTRIBUTE] = XMLUtilities :: get_attribute($date, self :: METADATA_ID_ATTRIBUTE, DataClass :: NO_UID);
                $contribution_array['date_original_id'] = XMLUtilities :: get_attribute($date, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
            }
            
            $contributions[] = $contribution_array;
        }
        
        return $contributions;
    }

    //2.3.2 Contribution Entity
    public function add_lifeCycle_entity($contribute_index, $entity_value)
    {
        $new_node = $this->ieeeLom->add_lifeCycle_entity($contribute_index, $entity_value);
    }

    public function remove_lifeCycle_entity($contribute_index, $entity_index)
    {
        $new_node = $this->ieeeLom->remove_lifeCycle_entity($contribute_index, $entity_index);
    }

    //6.3 Rights description----------------------------------------------------
    public function merge_rights_description()
    {
        $langstring_mappers = $this->get_lang_strings_to_merge(MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION, '/lom/rights/description');
        //debug($langstring_mappers);
        

        if (isset($langstring_mappers) && count($langstring_mappers) > 0)
        {
            $this->add_rights_description($langstring_mappers[0]);
            
            /*
    	     * Add the copyrightAndOtherRestrictions node if there is at least one description
    	     */
            if (count($langstring_mappers[0]->get_strings()) > 0)
            {
                $copyright_and_other_restrictions = new IeeeLomVocabulary('yes');
            }
            else
            {
                $copyright_and_other_restrictions = new IeeeLomVocabulary('no');
            }
        }
        else
        {
            $copyright_and_other_restrictions = new IeeeLomVocabulary('no');
        }
        
        $this->ieeeLom->set_copyright_and_other_restrictions($copyright_and_other_restrictions);
    }

    public function add_rights_description($langstring_mapper)
    {
        $new_string_nodes = $this->ieeeLom->add_rights_description($langstring_mapper);
        
        if (isset($new_string_nodes))
        {
            foreach ($new_string_nodes as $index => $string_node)
            {
                $string_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $langstring_mapper->get_string_metadata_id($index));
                $string_node->setAttribute(self :: METADATA_ID_LANG_ATTRIBUTE, $langstring_mapper->get_lang_metadata_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, $langstring_mapper->get_string_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, $langstring_mapper->get_lang_override_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: STRING_ORIGINAL_ID, $langstring_mapper->get_string_original_id($index));
                $string_node->setAttribute(IeeeLomLangStringMapper :: LANGUAGE_ORIGINAL_ID, $langstring_mapper->get_lang_original_id($index));
            }
        }
        
        return $new_string_nodes;
    }

    /**
     * @return IeeeLomLangStringMapper
     */
    public function get_rights_description()
    {
        $description_nodes = $this->ieeeLom->get_rights_description();
        
        $langstrings = new IeeeLomLangStringMapper();
        
        foreach ($description_nodes as $description)
        {
            $string_metadata_id = XMLUtilities :: get_attribute($description, self :: METADATA_ID_ATTRIBUTE, DataClass :: NO_UID);
            $language_metadata_id = XMLUtilities :: get_attribute($description, self :: METADATA_ID_LANG_ATTRIBUTE, DataClass :: NO_UID);
            $string_override_id = XMLUtilities :: get_attribute($description, IeeeLomLangStringMapper :: STRING_OVERRIDE_ID, DataClass :: NO_UID);
            $language_override_id = XMLUtilities :: get_attribute($description, IeeeLomLangStringMapper :: LANGUAGE_OVERRIDE_ID, DataClass :: NO_UID);
            $string_original_id = XMLUtilities :: get_attribute($description, self :: ORIGINAL_ID_ATTRIBUTE, DataClass :: NO_UID);
            
            $langstrings->add_string($description->nodeValue, $description->getAttribute('language'), $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id);
        }
        
        return $langstrings;
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    /**
     * Save the values submitted by a form in the datasource 
     * 
     * @param $submitted_values Array of submitted values
     */
    public function save_submitted_values($submitted_values)
    {
        $this->constant_values = array();
        
        $this->get_metadata();
        
        $this->store_general_identifier_from_submitted_values($submitted_values);
        $this->store_general_title_from_submitted_values($submitted_values);
        $this->store_general_language_from_submitted_values($submitted_values);
        $this->store_general_description_from_submitted_values($submitted_values);
        $this->store_lifeCycle_contribution_from_submitted_values($submitted_values);
        $this->store_general_rights_description_from_submitted_values($submitted_values);
        
        //debug($this->ieeeLom->get_dom());
        

        $this->save_metadata();
        
        return (count($this->errors) == 0);
    }

    //1.1 Identifier-----------------------------------------------------------
    private function store_general_identifier_from_submitted_values($submitted_values)
    {
        $this->ieeeLom->clear_general_identifier();
        
        if (isset($submitted_values[MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER]))
        {
            foreach ($submitted_values[MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER] as $key => $data)
            {
                $this->add_general_identifier($data['catalog'], $data['entry'], $data[self :: METADATA_ID_CATALOG_ATTRIBUTE], $data[self :: METADATA_ID_ENTRY_ATTRIBUTE], $data[self :: ORIGINAL_ID_ATTRIBUTE], null);
            }
        }
    }

    //1.2 Title----------------------------------------------------------------
    private function store_general_title_from_submitted_values($submitted_values)
    {
        $this->ieeeLom->clear_general_title();
        
        if (isset($submitted_values[MetadataLomEditForm :: LOM_GENERAL_TITLE]))
        {
            foreach ($submitted_values[MetadataLomEditForm :: LOM_GENERAL_TITLE] as $key => $data)
            {
                $string = $data['string'];
                $string_metadata_id = isset($data['string_metadata_id']) ? $data['string_metadata_id'] : null;
                $string_override_id = isset($data['string_override_id']) ? $data['string_override_id'] : null;
                $string_original_id = isset($data['string_original_id']) ? $data['string_original_id'] : null;
                
                $language = $data['language'];
                $language_metadata_id = isset($data['language_metadata_id']) ? $data['language_metadata_id'] : null;
                $language_override_id = isset($data['language_override_id']) ? $data['language_override_id'] : null;
                $language_original_id = isset($data['language_original_id']) ? $data['language_original_id'] : null;
                
                $langstring_mapper = new IeeeLomLangStringMapper($string, $language, $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id, $language_original_id);
                
                $this->add_general_title($langstring_mapper);
            }
        }
    }

    //1.3 Language-------------------------------------------------------------
    private function store_general_language_from_submitted_values($submitted_values)
    {
        $this->ieeeLom->clear_general_language();
        
        if (isset($submitted_values[MetadataLomEditForm :: LOM_GENERAL_LANGUAGE]))
        {
            foreach ($submitted_values[MetadataLomEditForm :: LOM_GENERAL_LANGUAGE] as $key => $data)
            {
                //debug($data);
                $this->add_general_language($data['language'], $data[self :: METADATA_ID_ATTRIBUTE], $data[self :: METADATA_OVERRIDE_ID]);
            }
        }
    }

    //1.4 Description----------------------------------------------------------
    private function store_general_description_from_submitted_values($submitted_values)
    {
        $this->ieeeLom->clear_general_description();
        
        if (isset($submitted_values[MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION]))
        {
            $langstring_mapper = new IeeeLomLangStringMapper();
            
            foreach ($submitted_values[MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION] as $key => $descriptions)
            {
                foreach ($descriptions as $subkey => $data)
                {
                    $string = $data['string'];
                    $string_metadata_id = isset($data['string_metadata_id']) ? $data['string_metadata_id'] : null;
                    $string_override_id = isset($data['string_override_id']) ? $data['string_override_id'] : null;
                    $string_original_id = isset($data['string_original_id']) ? $data['string_original_id'] : null;
                    
                    $language = $data['language'];
                    $language_metadata_id = isset($data['language_metadata_id']) ? $data['language_metadata_id'] : null;
                    $language_override_id = isset($data['language_override_id']) ? $data['language_override_id'] : null;
                    $language_original_id = isset($data['language_original_id']) ? $data['language_original_id'] : null;
                    
                    $langstring_mapper->add_string($string, $language, $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id, $language_original_id);
                }
            }
            
            $this->add_general_description($langstring_mapper);
        }
    }

    //2.3 Contribution---------------------------------------------------------
    private function store_lifeCycle_contribution_from_submitted_values($submitted_values)
    {
        $this->ieeeLom->clear_lifeCycle_contribution();
        
        if (isset($submitted_values[MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION]))
        {
            foreach ($submitted_values[MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION] as $key => $data)
            {
                $role_value = $data['role'];
                $role_metadata_id = isset($data['role_metadata_id']) ? $data['role_metadata_id'] : DataClass :: NO_UID;
                $role_override_id = isset($data['role_override_id']) ? $data['role_override_id'] : DataClass :: NO_UID;
                $role_original_id = isset($data['role_original_id']) ? $data['role_original_id'] : DataClass :: NO_UID;
                
                $entity_values = array();
                if (isset($data['entity']))
                {
                    $subkey = 0;
                    foreach ($data['entity'] as $entity)
                    {
                        $entity_original_id = isset($entity['entity_original_id']) ? $entity['entity_original_id'] : DataClass :: NO_UID;
                        
                        $entity_values[$subkey]['name']['value'] = $entity['name'];
                        $entity_values[$subkey]['name'][self :: METADATA_ID_ATTRIBUTE] = isset($entity['name_metadata_id']) ? $entity['name_metadata_id'] : DataClass :: NO_UID;
                        $entity_values[$subkey]['name'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = isset($entity['override_id']) ? $entity['override_id'] : DataClass :: NO_UID;
                        $entity_values[$subkey]['name']['original_id'] = $entity_original_id;
                        
                        $entity_values[$subkey]['email']['value'] = $entity['email'];
                        $entity_values[$subkey]['email'][self :: METADATA_ID_ATTRIBUTE] = isset($entity['email_metadata_id']) ? $entity['email_metadata_id'] : DataClass :: NO_UID;
                        $entity_values[$subkey]['email'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = isset($entity['override_id']) ? $entity['override_id'] : DataClass :: NO_UID;
                        $entity_values[$subkey]['email']['original_id'] = $entity_original_id;
                        
                        $entity_values[$subkey]['organisation']['value'] = $entity['organisation'];
                        $entity_values[$subkey]['organisation'][self :: METADATA_ID_ATTRIBUTE] = isset($entity['organisation_metadata_id']) ? $entity['organisation_metadata_id'] : DataClass :: NO_UID;
                        $entity_values[$subkey]['organisation'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = isset($entity['override_id']) ? $entity['override_id'] : DataClass :: NO_UID;
                        $entity_values[$subkey]['organisation'][ContentObjectMetadata :: PROPERTY_OVERRIDE_ID] = $entity_original_id;
                        
                        $subkey ++;
                    }
                }
                
                $ieee_lom_dt = new IeeeLomDatetime();
                $ieee_lom_dt->set_day($data['date']['day']);
                $ieee_lom_dt->set_month($data['date']['month']);
                $ieee_lom_dt->set_year($data['date']['year']);
                $ieee_lom_dt->set_hour($data['date']['hour']);
                $ieee_lom_dt->set_min($data['date']['min']);
                $ieee_lom_dt->set_sec($data['date']['sec']);
                
                $date_metadata_id = isset($data['date']['date_metadata_id']) ? $data['date']['date_metadata_id'] : DataClass :: NO_UID;
                $date_override_id = isset($data['contribution_override_id']) ? $data['contribution_override_id'] : DataClass :: NO_UID;
                $date_original_id = isset($data['date']['date_original_id']) ? $data['date']['date_original_id'] : DataClass :: NO_UID;
                
                $this->add_lifeCycle_contribution($role_value, $role_metadata_id, $role_override_id, $role_original_id, $entity_values, $ieee_lom_dt->get_datetime(), $date_metadata_id, $date_override_id, $date_original_id);
            }
        }
    }

    //6.3 Rights description---------------------------------------------------
    private function store_general_rights_description_from_submitted_values($submitted_values)
    {
        $this->ieeeLom->clear_rights_description();
        
        if (isset($submitted_values[MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION]))
        {
            foreach ($submitted_values[MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION] as $key => $data)
            {
                //debug($data);
                

                $string = $data['string'];
                $string_metadata_id = isset($data['string_metadata_id']) ? $data['string_metadata_id'] : null;
                $string_override_id = isset($data['string_override_id']) ? $data['string_override_id'] : null;
                $string_original_id = isset($data['string_original_id']) ? $data['string_original_id'] : null;
                
                $language = $data['language'];
                $language_metadata_id = isset($data['language_metadata_id']) ? $data['language_metadata_id'] : null;
                $language_override_id = isset($data['language_override_id']) ? $data['language_override_id'] : null;
                $language_original_id = isset($data['language_original_id']) ? $data['language_original_id'] : null;
                
                $langstring_mapper = new IeeeLomLangStringMapper($string, $language, $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id, $language_original_id);
                
                $this->add_rights_description($langstring_mapper);
            }
        }
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    /*
	 * This section contains code allowing to save the content of the IeeeLom in the metadata table.
	 * The goal is to be able to use a IeeeLomMapper instance as an active record 
	 */
    public function save_metadata()
    {
        $this->save_general_identifier();
        $this->save_general_title();
        $this->save_general_language();
        $this->save_general_description();
        $this->save_lifeCycle_contribution();
        $this->save_rights_description();
        
        /*
	      * Refresh the XML Document to get the new generated ids
	      */
        $this->get_metadata();
    }

    //1.1 Identifier-----------------------------------------------------------
    private function save_general_identifier()
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[', IeeeLom :: VERSION);
        
        $identifiers = $this->get_identifier();
        
        $saved_metadata_id = array();
        foreach ($identifiers as $key => $identifier)
        {
            //debug($identifier);
            

            try
            {
                $override_id = isset($identifier[self :: OVERRIDE_ID_ATTRIBUTE]) && $identifier[self :: OVERRIDE_ID_ATTRIBUTE] != DataClass :: NO_UID ? $identifier[self :: OVERRIDE_ID_ATTRIBUTE] : $identifier[self :: ORIGINAL_ID_ATTRIBUTE];
                
                $meta_data = $this->get_new_content_object_metadata($identifier[self :: METADATA_ID_CATALOG_ATTRIBUTE], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][catalog]', $identifier['catalog'], $override_id);
                $meta_data->save();
                
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Store the new generated ID that could be used in a form 
        	     */
                $input_name_metadata_id = MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][' . self :: METADATA_ID_CATALOG_ATTRIBUTE . ']';
                $input_name_override_id = MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][' . self :: OVERRIDE_ID_ATTRIBUTE . ']';
                $input_name_original_id = MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][' . self :: ORIGINAL_ID_ATTRIBUTE . ']';
                
                $this->store_constant_value($input_name_metadata_id, $meta_data->get_id());
                $this->store_constant_value($input_name_override_id, $meta_data->get_override_id());
                $this->store_constant_value($input_name_original_id, DataClass :: NO_UID);
                
                try
                {
                    $meta_data = $this->get_new_content_object_metadata($identifier[self :: METADATA_ID_ENTRY_ATTRIBUTE], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][entry]', $identifier['entry'], $override_id);
                    $meta_data->save();
                    
                    $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                    
                    /*
            	     * Store the new generated ID that could used in a form 
            	     */
                    $input_name = MetadataLomEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][' . self :: METADATA_ID_ENTRY_ATTRIBUTE . ']';
                    $this->store_constant_value($input_name, $meta_data->get_id());
                }
                catch (Exception $ex)
                {
                    $this->add_error($ex->getMessage());
                }
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
        
        /*
         * Delete metadata in the datasource that do not exist anymore in memory
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }

    //1.2 Title----------------------------------------------------------------
    private function save_general_title()
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, MetadataLomEditForm :: LOM_GENERAL_TITLE . '[', IeeeLom :: VERSION);
        
        //debug($submitted_values);
        $titles = $this->get_titles();
        //debug($titles->get_strings());
        

        $saved_metadata_id = array();
        
        foreach ($titles->get_strings() as $key => $string)
        {
            try
            {
                $override_id = isset($string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID]) && $string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID] != DataClass :: NO_UID ? $string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID] : $string[IeeeLomLangStringMapper :: STRING_ORIGINAL_ID];
                $meta_data = $this->get_new_content_object_metadata($string[IeeeLomLangStringMapper :: STRING_METADATA_ID], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_TITLE . '[' . $key . ']' . '[string]', $string['string'], $override_id);
                $meta_data->save();
                
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Store the new generated ID that could be used in a form 
        	     */
                $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_TITLE . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $meta_data->get_id());
                $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_TITLE . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $meta_data->get_override_id());
                $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_TITLE . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . ']', DataClass :: NO_UID);
                
                if ($string['language'] != IeeeLomLangStringMapper :: NO_LANGUAGE)
                {
                    try
                    {
                        $meta_data = $this->get_new_content_object_metadata($string[IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_TITLE . '[' . $key . ']' . '[language]', $string['language'], $override_id);
                        $meta_data->save();
                        
                        $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                        
                        /*
                	     * Store the new generated ID that could be used in a form 
                	     */
                        $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_TITLE . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $meta_data->get_id());
                    }
                    catch (Exception $ex)
                    {
                        $this->add_error($ex->getMessage());
                    }
                }
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
        
        /*
         * Delete metadata in the datasource that do not exist anymore in memory
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }

    //1.3 Language-------------------------------------------------------------
    private function save_general_language()
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, MetadataLomEditForm :: LOM_GENERAL_LANGUAGE . '[', IeeeLom :: VERSION);
        
        $languages = $this->get_general_languages();
        
        $saved_metadata_id = array();
        foreach ($languages as $key => $language)
        {
            try
            {
                //so far, there is no info in the standard datasource tables to override for language 
                $override_id = DataClass :: NO_UID;
                
                $meta_data = $this->get_new_content_object_metadata($language[self :: METADATA_ID_ATTRIBUTE], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_LANGUAGE . '[' . $key . '][language]', $language['language'], $language[self :: OVERRIDE_ID_ATTRIBUTE], $override_id);
                $meta_data->save();
                
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Store the new generated ID that could be used in a form 
        	     */
                $input_name_metadata_id = MetadataLomEditForm :: LOM_GENERAL_LANGUAGE . '[' . $key . '][' . self :: METADATA_ID_ATTRIBUTE . ']';
                $input_name_override_id = MetadataLomEditForm :: LOM_GENERAL_LANGUAGE . '[' . $key . '][' . self :: OVERRIDE_ID_ATTRIBUTE . ']';
                $input_name_original_id = MetadataLomEditForm :: LOM_GENERAL_LANGUAGE . '[' . $key . '][' . self :: ORIGINAL_ID_ATTRIBUTE . ']';
                
                $this->store_constant_value($input_name_metadata_id, $meta_data->get_id());
                $this->store_constant_value($input_name_override_id, $meta_data->get_override_id());
                $this->store_constant_value($input_name_original_id, DataClass :: NO_UID);
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
        
        /*
         * Delete metadata in the datasource that do not exist anymore in memory
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }

    //1.4 Description----------------------------------------------------------
    private function save_general_description()
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[', IeeeLom :: VERSION);
        
        $descriptions = $this->get_descriptions();
        //debug($descriptions);
        

        $saved_metadata_id = array();
        
        foreach ($descriptions as $key => $langstring_mapper)
        {
            //debug($langstring_mapper);
            foreach ($langstring_mapper->get_strings() as $subkey => $string)
            {
                try
                {
                    $override_id = isset($string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID]) && $string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID] != DataClass :: NO_UID ? $string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID] : $string[IeeeLomLangStringMapper :: STRING_ORIGINAL_ID];
                    $meta_data = $this->get_new_content_object_metadata($string[IeeeLomLangStringMapper :: STRING_METADATA_ID], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[' . $key . '][' . $subkey . ']' . '[string]', $string['string'], $override_id);
                    $meta_data->save();
                    
                    $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                    
                    /*
            	     * Store the new generated ID that could be used in a form 
            	     */
                    $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[' . $key . ']' . '[' . $subkey . ']' . '[' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $meta_data->get_id());
                    $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[' . $key . ']' . '[' . $subkey . ']' . '[' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $meta_data->get_override_id());
                    $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[' . $key . ']' . '[' . $subkey . ']' . '[' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . ']', DataClass :: NO_UID);
                    
                    if ($string['language'] != IeeeLomLangStringMapper :: NO_LANGUAGE)
                    {
                        try
                        {
                            $meta_data = $this->get_new_content_object_metadata($string[IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[' . $key . '][' . $subkey . ']' . '[language]', $string['language'], $override_id);
                            $meta_data->save();
                            
                            $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                            
                            /*
                    	     * Store the new generated ID that could be used in a form 
                    	     */
                            $this->store_constant_value(MetadataLomEditForm :: LOM_GENERAL_DESCRIPTION . '[' . $key . ']' . '[' . $subkey . ']' . '[' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $meta_data->get_id());
                        }
                        catch (Exception $ex)
                        {
                            $this->add_error($ex->getMessage());
                        }
                    }
                }
                catch (Exception $ex)
                {
                    $this->add_error($ex->getMessage());
                }
            }
        }
        
        /*
         * Delete metadata in the datasource that do not exist anymore in memory
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }

    //2.3 Contribution---------------------------------------------------------
    private function save_lifeCycle_contribution()
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[', IeeeLom :: VERSION);
        
        $life_cycles = $this->get_lifeCycle_contribution();
        
        //debug($life_cycles);
        

        $saved_metadata_id = array();
        
        foreach ($life_cycles as $key => $life_cycle)
        {
            //debug($life_cycle);
            

            /*
	         * Save role
	         */
            try
            {
                $role_original_id = isset($life_cycle['role_override_id']) && $life_cycle['role_override_id'] != DataClass :: NO_UID ? $life_cycle['role_override_id'] : $life_cycle['role_original_id'];
                
                $meta_data = $this->get_new_content_object_metadata($life_cycle['role_metadata_id'], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role]', $life_cycle['role'], $role_original_id);
                $meta_data->save();
                
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Store the new generated ID that could be used in a form 
        	     */
                $input_name_metadata = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role_metadata_id]';
                $input_name_original = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role_original_id]';
                $input_name_override = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role_override_id]';
                
                $this->store_constant_value($input_name_metadata, $meta_data->get_id());
                $this->store_constant_value($input_name_original, DataClass :: NO_UID);
                $this->store_constant_value($input_name_override, $meta_data->get_override_id());
                
                /*
                  * Store the general override_id and original_id. (based on the role, because if the role is an override, the rest is as well)
                  */
                $input_name_contri_override = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][contribution_override_id]';
                $input_name_contri_original = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][contribution_original_id]';
                
                $this->store_constant_value($input_name_contri_override, $meta_data->get_override_id());
                $this->store_constant_value($input_name_contri_original, DataClass :: NO_UID);
            
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
            
            /*
	         * Save entities
	         */
            foreach ($life_cycle['entity'] as $entity_index => $entity)
            {
                //debug($entity);
                

                $input_name_name = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][name]';
                $input_name_email = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][email]';
                $input_name_org = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][organisation]';
                
                $entity_original_id = isset($entity['entity_override_id']) && $entity['entity_override_id'] != DataClass :: NO_UID ? $entity['entity_override_id'] : $entity['entity_original_id'];
                
                $meta_data_name = $this->get_new_content_object_metadata($entity['name']['name_metadata_id'], IeeeLom :: VERSION, $input_name_name, $entity['name']['value'], $entity_original_id);
                $meta_data_email = $this->get_new_content_object_metadata($entity['email']['email_metadata_id'], IeeeLom :: VERSION, $input_name_email, $entity['email']['value'], $entity_original_id);
                $meta_data_org = $this->get_new_content_object_metadata($entity['organisation']['organisation_metadata_id'], IeeeLom :: VERSION, $input_name_org, $entity['organisation']['value'], $entity_original_id);
                
                try
                {
                    if (strlen($meta_data_name->get_value()) > 0)
                    {
                        $meta_data_name->save();
                        $saved_metadata_id[$meta_data_name->get_id()] = $meta_data_name;
                    }
                    
                    if (strlen($meta_data_email->get_value()) > 0)
                    {
                        $meta_data_email->save();
                        $saved_metadata_id[$meta_data_email->get_id()] = $meta_data_email;
                    }
                    
                    if (strlen($meta_data_org->get_value()) > 0)
                    {
                        $meta_data_org->save();
                        $saved_metadata_id[$meta_data_org->get_id()] = $meta_data_org;
                    }
                    
                    /*
            	     * Store the new generated ID that could be used in a form 
            	     */
                    $input_name_name_metadata = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][name_metadata_id]';
                    $input_name_email_metadata = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][email_metadata_id]';
                    $input_name_org_metadata = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][organisation_metadata_id]';
                    $input_name_original_id = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][entity_original_id]';
                    $input_name_override_id = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][' . self :: OVERRIDE_ID_ATTRIBUTE . ']';
                    
                    $this->store_constant_value($input_name_name_metadata, $meta_data_name->get_id());
                    $this->store_constant_value($input_name_email_metadata, $meta_data_email->get_id());
                    $this->store_constant_value($input_name_org_metadata, $meta_data_org->get_id());
                    $this->store_constant_value($input_name_original_id, DataClass :: NO_UID);
                    /*
                     * The override id for the name is taken as the genral override id as if the name is an override, it is the same for email and org
                     */
                    $this->store_constant_value($input_name_override_id, $meta_data_name->get_override_id());
                }
                catch (Exception $ex)
                {
                    $this->add_error($ex->getMessage());
                }
            }
            
            /*
	         * Save date
	         */
            try
            {
                $date_original_id = isset($life_cycle['date_override_id']) && $life_cycle['date_override_id'] != DataClass :: NO_UID ? $life_cycle['date_override_id'] : $life_cycle['date_original_id'];
                
                $meta_data = $this->get_new_content_object_metadata($life_cycle['date_metadata_id'], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date]', $life_cycle['date'], $date_original_id);
                $meta_data->save();
                
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Store the new generated ID that could be used in a form 
        	     */
                $input_name_metadata = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date][date_metadata_id]';
                $input_name_original = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date][date_original_id]';
                $input_name_override = MetadataLomEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date][date_override_id]';
                
                $this->store_constant_value($input_name_metadata, $meta_data->get_id());
                $this->store_constant_value($input_name_original, DataClass :: NO_UID);
                $this->store_constant_value($input_name_override, $meta_data->get_override_id());
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
        
        /*
         * Delete metadata in the datasource that do not exist anymore in memory
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }

    //6.3 Rights description---------------------------------------------------
    private function save_rights_description()
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[', IeeeLom :: VERSION);
        
        //debug($submitted_values);
        $rights = $this->get_rights_description();
        //debug($titles->get_strings());
        

        $saved_metadata_id = array();
        
        foreach ($rights->get_strings() as $key => $string)
        {
            try
            {
                $override_id = isset($string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID]) && $string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID] != DataClass :: NO_UID ? $string[IeeeLomLangStringMapper :: STRING_OVERRIDE_ID] : $string[IeeeLomLangStringMapper :: STRING_ORIGINAL_ID];
                $meta_data = $this->get_new_content_object_metadata($string[IeeeLomLangStringMapper :: STRING_METADATA_ID], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[' . $key . ']' . '[string]', $string['string'], $override_id);
                $meta_data->save();
                
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Store the new generated ID that could be used in a form 
        	     */
                $this->store_constant_value(MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $meta_data->get_id());
                $this->store_constant_value(MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $meta_data->get_override_id());
                $this->store_constant_value(MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . ']', DataClass :: NO_UID);
                
                if ($string['language'] != IeeeLomLangStringMapper :: NO_LANGUAGE)
                {
                    try
                    {
                        $meta_data = $this->get_new_content_object_metadata($string[IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID], IeeeLom :: VERSION, MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[' . $key . ']' . '[language]', $string['language'], $override_id);
                        $meta_data->save();
                        
                        $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                        
                        /*
                	     * Store the new generated ID that could be used in a form 
                	     */
                        $this->store_constant_value(MetadataLomEditForm :: LOM_RIGHTS_DESCRIPTION . '[' . $key . ']' . '[' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $meta_data->get_id());
                    }
                    catch (Exception $ex)
                    {
                        $this->add_error($ex->getMessage());
                    }
                }
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
        
        /*
         * Delete metadata in the datasource that do not exist anymore in memory
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    /**
     * Get a IeeeLomLangStringMapper to merge with the original metadata
     * 
     * @param $start_value The beginning of the stored existing metadata in the datasource. Used to retrieve the existing metadata
     * @param $xpath_to_langstring XPATH to the langstring node. Used to remove original nodes if some metadata override them.
     * @return IeeeLomLangstringMapper
     */
    private function get_lang_strings_to_merge($start_value, $xpath_to_langstring)
    {
        $metadata_array = $this->get_additional_metadata($start_value . '[');
        
        //-----//---------------//---------------//-------------------------
        $multilevel_array = array();
        foreach ($metadata_array as $content_object_metadata)
        {
            $multilevel_array[$content_object_metadata->get_property()] = $content_object_metadata;
        }
        $sorted_metadata = StringUtilities :: to_multilevel_array($multilevel_array);
        //debug($sorted_metadata);
        

        $langstring_mappers = array();
        
        $only_one_level = true;
        if (count($sorted_metadata) > 0)
        {
            if (isset($sorted_metadata[0]['string']) && is_a($sorted_metadata[0]['string'], 'ContentObjectMetadata'))
            {
                /*
	             * $sorted_metadata contains only one section of strings
	             */
                $only_one_level = true;
            }
            else
            {
                /*
	             * $sorted_metadata contains only many sections of strings
	             */
                $only_one_level = false;
            }
            
            if ($only_one_level)
            {
                $langstring_mappers[] = $this->get_langstring_mapper($sorted_metadata);
            }
            else
            {
                foreach ($sorted_metadata as $index => $sorted_metadata_object)
                {
                    $langstring_mappers[] = $this->get_langstring_mapper($sorted_metadata_object);
                }
            }
            
            //debug($langstring_mappers);
            

            return $langstring_mappers;
        }
        else
        {
            return null;
        }
    }

    private function get_langstring_mapper($metadata_array)
    {
        //debug($metadata_array);
        

        $langstring_mapper = new IeeeLomLangStringMapper();
        
        foreach ($metadata_array as $langstring_content_objects)
        {
            $string = null;
            $string_metadata_id = DataClass :: NO_UID;
            $string_override_id = DataClass :: NO_UID;
            
            $language = null;
            $lang_metadata_id = DataClass :: NO_UID;
            $lang_override_id = DataClass :: NO_UID;
            
            if (isset($langstring_content_objects['string']))
            {
                $content_object_metadata = $langstring_content_objects['string'];
                $string = $content_object_metadata->get_value();
                $string_metadata_id = $content_object_metadata->get_id();
                $string_override_id = $content_object_metadata->get_override_id();
            }
            
            if (isset($langstring_content_objects['language']))
            {
                $content_object_metadata = $langstring_content_objects['language'];
                $language = $content_object_metadata->get_value();
                $lang_metadata_id = $content_object_metadata->get_id();
                $lang_override_id = $content_object_metadata->get_override_id();
            }
            
            $langstring_mapper->add_string($string, $language, $string_metadata_id, $lang_metadata_id, $string_override_id, $lang_override_id);
        }
        
        return $langstring_mapper;
    }

    /**
     * Save new metadata langstring values in the datasource.
     * 
     * @param $submitted_values Array containing the new metadata to save
     * @param $start_value The beginning of the stored existing metadata in the datasource. Used to retrieve the existing metadata and to save the new ones
     */
    private function save_lang_strings($submitted_values, $start_value)
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(ContentObjectMetadata :: PROPERTY_PROPERTY, $start_value . '[', IeeeLom :: VERSION);
        
        $saved_metadata_id = array();
        
        if (isset($submitted_values[$start_value]))
        {
            //debug($submitted_values[$start_value]);
            

            $this->save_recursive_langstrings($submitted_values[$start_value], $start_value, $saved_metadata_id);
            
            /*
             * Delete metadata that were not sent back for saving
             */
            $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
        }
    }

    private function save_recursive_langstrings($submitted_values, $property_name, &$saved_metadata_id)
    {
        if (count($submitted_values) > 0 && isset($submitted_values[0]['string']))
        {
            foreach ($submitted_values as $index => $description)
            {
                $meta_data = $this->save_langstring($property_name . '[' . $index . ']', $description, $saved_metadata_id);
                
                if (isset($meta_data))
                {
                    $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                }
            }
        }
        else
        {
            foreach ($submitted_values as $index => $sub_values)
            {
                $this->save_recursive_langstrings($sub_values, $property_name . '[' . $index . ']', $saved_metadata_id);
            }
        }
    }

    private function save_langstring($property_name, $langstring_description, &$saved_metadata_id)
    {
        //debug($langstring_description);
        

        $meta_data = $this->get_new_content_object_metadata($langstring_description[IeeeLomLangStringMapper :: STRING_METADATA_ID], IeeeLom :: VERSION, $property_name . '[string]', $langstring_description['string'], $langstring_description[IeeeLomLangStringMapper :: STRING_ORIGINAL_ID], IeeeLom :: VERSION);
        
        try
        {
            $meta_data->save();
            $saved_metadata_id[$meta_data->get_id()] = $meta_data;
            
            /*
    	     * Set the new generated ID in the form 
    	     */
            
            $this->store_constant_value($property_name . '[' . IeeeLomLangStringMapper :: STRING_METADATA_ID . ']', $meta_data->get_id());
            $this->store_constant_value($property_name . '[' . IeeeLomLangStringMapper :: STRING_OVERRIDE_ID . ']', $meta_data->get_override_id());
            $this->store_constant_value($property_name . '[' . IeeeLomLangStringMapper :: STRING_ORIGINAL_ID . ']', DataClass :: NO_UID);
        }
        catch (Exception $ex)
        {
            $this->add_error($ex->getMessage());
            
            return null;
        }
        
        if (isset($langstring_description['language']) && strlen($langstring_description['language']) > 0)
        {
            if ($langstring_description['language'] == '0')
            {
                $langstring_description['language'] = IeeeLom :: NO_LANGUAGE;
            }
            
            $meta_data = $this->get_new_content_object_metadata($langstring_description[IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID], IeeeLom :: VERSION, $property_name . '[language]', $langstring_description['language'], IeeeLom :: VERSION);
            
            try
            {
                $meta_data->save();
                $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                
                /*
        	     * Set the new generated ID in the form 
        	     */
                $this->store_constant_value($property_name . '[' . IeeeLomLangStringMapper :: LANGUAGE_METADATA_ID . ']', $meta_data->get_id());
                
                return $meta_data;
            }
            catch (Exception $ex)
            {
                $this->add_error($ex->getMessage());
                
                return null;
            }
        }
        else
        {
            return $meta_data;
        }
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    private function store_constant_value($input_name, $input_value)
    {
        $this->constant_values[] = array('name' => $input_name, 'value' => $input_value);
    }

    public function get_constant_values()
    {
        return $this->constant_values;
    }

    /****************************************************************************************/
    /****************************************************************************************/
    /****************************************************************************************/
    
    function debug_dom($title = null)
    {
        debug($this->ieeeLom->get_dom(), $title, 2);
    }
}
?>