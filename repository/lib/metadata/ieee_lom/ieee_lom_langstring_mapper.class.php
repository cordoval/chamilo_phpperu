<?php
/**
 * $Id: ieee_lom_langstring_mapper.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
require_once dirname(__FILE__) . '/../../metadata/ieee_lom/ieee_lom_langstring.class.php';

class IeeeLomLangStringMapper extends IeeeLomLangString
{
    const STRING_METADATA_ID = 'string_metadata_id';
    const STRING_OVERRIDE_ID = 'string_override_id';
    const STRING_ORIGINAL_ID = 'string_original_id';
    const LANGUAGE_METADATA_ID = 'language_metadata_id';
    const LANGUAGE_OVERRIDE_ID = 'language_override_id';
    const LANGUAGE_ORIGINAL_ID = 'language_original_id';

    public function IeeeLomLangStringMapper($string = null, $language = null, $string_metadata_id = null, $language_metadata_id = null, $string_override_id = null, $language_override_id = null, $string_original_id = null, $language_original_id = null)
    {
        parent :: IeeeLomLangString($string, $language);
        
        if (isset($this->strings[0]))
        {
            $this->strings[0][self :: STRING_METADATA_ID] = $string_metadata_id;
            $this->strings[0][self :: LANGUAGE_METADATA_ID] = $language_metadata_id;
            $this->strings[0][self :: STRING_OVERRIDE_ID] = $string_override_id;
            $this->strings[0][self :: LANGUAGE_OVERRIDE_ID] = $language_override_id;
            $this->strings[0][self :: STRING_ORIGINAL_ID] = $string_original_id;
            $this->strings[0][self :: LANGUAGE_ORIGINAL_ID] = $language_original_id;
            
        //$string_original_id = null, $language_original_id
        }
    }

    /**
     * Adds a new string to the set of strings with the corresponding metadata ids
     * @param string|null $string The text
     * @param string|null $language The language of the $string parameters
     */
    public function add_string($string = null, $language = null, $string_metadata_id = null, $language_metadata_id = null, $string_override_id = null, $language_override_id = null, $string_original_id = null, $language_original_id = null)
    {
        $new_string = array();
        $new_string[parent :: STRING] = $string;
        
        if (isset($language) && strlen($language) > 0)
        {
            $new_string[parent :: LANGUAGE] = $language;
        }
        else
        {
            $new_string[parent :: LANGUAGE] = parent :: NO_LANGUAGE;
        }
        
        $this->set_id_property($new_string, self :: STRING_METADATA_ID, $string_metadata_id);
        $this->set_id_property($new_string, self :: LANGUAGE_METADATA_ID, $language_metadata_id);
        
        $this->set_id_property($new_string, self :: STRING_OVERRIDE_ID, $string_override_id);
        $this->set_id_property($new_string, self :: LANGUAGE_OVERRIDE_ID, $language_override_id);
        
        $this->set_id_property($new_string, self :: STRING_ORIGINAL_ID, $string_original_id);
        $this->set_id_property($new_string, self :: LANGUAGE_ORIGINAL_ID, $language_original_id);
        
        $this->strings[] = $new_string;
    }

    private function set_id_property(&$new_string, $property_name, $value)
    {
        $new_string[$property_name] = (isset($value) && strlen($value) > 0 && $value != 0) ? $value : DataClass :: NO_UID;
    }

    public function get_string_metadata_id($index)
    {
        return $this->strings[$index][self :: STRING_METADATA_ID];
    }

    public function get_lang_metadata_id($index)
    {
        return $this->strings[$index][self :: LANGUAGE_METADATA_ID];
    }

    public function get_string_override_id($index)
    {
        return $this->strings[$index][self :: STRING_OVERRIDE_ID];
    }

    public function get_lang_override_id($index)
    {
        return $this->strings[$index][self :: LANGUAGE_OVERRIDE_ID];
    }

    public function get_string_original_id($index)
    {
        return $this->strings[$index][self :: STRING_ORIGINAL_ID];
    }

    public function get_lang_original_id($index)
    {
        return $this->strings[$index][self :: LANGUAGE_ORIGINAL_ID];
    }

}
?>