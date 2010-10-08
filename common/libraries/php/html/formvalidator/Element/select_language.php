<?php
/**
 * @package common.html.formvalidator.Element
 */
// $Id: select_language.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/select.php');
/**
 * A dropdownlist with all languages to use with QuickForm
 */
class HTML_QuickForm_Select_Language extends HTML_QuickForm_select
{

    /**
     * Class constructor
     */
    function HTML_QuickForm_Select_Language($elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        parent :: HTML_QuickForm_Select($elementName, $elementLabel, $options, $attributes);
        // Get all languages
        $adm = AdminDataManager :: get_instance();
        $languages = $adm->retrieve_languages();
        $this->_options = array();
        $this->_values = array();
        
        while ($language = $languages->next_result())
        {
            $this->addOption($language->get_english_name(), $language->get_folder());
        }
    }
}
?>