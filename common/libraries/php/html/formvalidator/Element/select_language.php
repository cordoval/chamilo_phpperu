<?php
use admin\AdminDataManager;
/**
 * @package common.html.formvalidator.Element
 */
/**
 * A dropdownlist with all languages to use with QuickForm
 */
class HTML_QuickForm_Select_Language extends HTML_QuickForm_select
{

    /**
     * Class constructor
     */
    function __construct($elementName = null, $elementLabel = null, $options = null, $attributes = null)
    {
        parent :: __construct($elementName, $elementLabel, $options, $attributes);
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