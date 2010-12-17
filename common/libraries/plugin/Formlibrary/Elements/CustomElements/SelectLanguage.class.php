<?php
class SelectLanguage extends Select
{

    /**
     * Class constructor
     */
    public function SelectLanguage($elementName = null, $elementLabel = null, $options = null)
    {
        parent :: Select($elementName, $elementLabel, $options);
        // Get all languages
        $adm = AdminDataManager :: get_instance();
        $languages = $adm->retrieve_languages();
        $this->options = array();
        
        while ($language = $languages->next_result())
        {
            $this->options[$language->get_english_name()] = $language->get_folder();
        }
    }
}
?>