<?php
/**
 * $Id: ieee_lom_langstring.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.metadata.ieee_lom
 */
/**
 * A LangString field used in IEEE LOM.
 * This object zero or more strings in different languages
 */
class IeeeLomLangString
{
    const STRING = 'string';
    const LANGUAGE = 'language';
    const NO_LANGUAGE = 'x-none';
    
    /**
     * Array containing all strings
     */
    protected $strings;

    /**
     * Constructor
     * @param string|null $string The text
     * @param string|null $language The language of the $string parameters
     */
    public function IeeeLomLangString($string = null, $language = self :: NO_LANGUAGE)
    {
        $this->strings = array();
        
        if (isset($string))
        {
            $this->add_string($string, $language);
        }
    }

    /**
     * Adds a new string to the set of strings
     * @param string|null $string The text
     * @param string|null $language The language of the $string parameters
     */
    public function add_string($string = null, $language = self :: NO_LANGUAGE)
    {
        $new_string[self :: STRING] = $string;
        
        if (isset($language) && strlen($language) > 0)
        {
            $new_string[self :: LANGUAGE] = $language;
        }
        else
        {
            $new_string[self :: LANGUAGE] = self :: NO_LANGUAGE;
        }
        
        $this->strings[] = $new_string;
    }

    /**
     * Gets the strings
     * @return array This array is of the form
     * <pre>
     *  [0]['language'] = 'XX';
     *  [0]['string'] = 'XXXXXX';
     *  [1]['language'] = ...;
     *  ...
     * </pre>
     */
    public function get_strings()
    {
        return $this->strings;
    }

    public function get_string($index)
    {
        return $this->strings[$index][self :: STRING];
    }

    public function get_language($index)
    {
        return $this->strings[$index][self :: LANGUAGE];
    }
}
?>