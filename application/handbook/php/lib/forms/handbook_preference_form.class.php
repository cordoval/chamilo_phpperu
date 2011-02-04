<?php

namespace application\handbook;

use common\libraries\FormValidator;
use common\libraries\Translation;
use application\metadata\MetadataDataManager;
use application\metadata\MetadataPropertyValue;
use application\metadata\MetadataForm;
use common\libraries\Request;
use application\context_linker\ContextLink;
use application\metadata\MetadataPropertyType;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Utilities;

class HandbookPreferenceForm extends FormValidator
{
    const TYPE = 'content_object';
    private $publication_id;
    private $preferences;

    function __construct($publication_id, $action)
    {
        parent :: __construct('handbook_publication_preferences', 'post', $action);
        $this->publication_id = $publication_id;
        $this->build_basic_form();
    }

    function build_basic_form()
    {
        //TODO: implement
        $this->get_current_preferences();
         $this->add_select('metadata', Translation :: get('Metadata'), $this->get_all_available_metadata_elements());
       $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities::COMMON_LIBRARIES), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities::COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);




    }

    function get_all_available_metadata_elements()
    {

        $mdm = MetadataDataManager::get_instance();
        $prefixes_array = $mdm->retrieve_prefixes();
        $element_set = $mdm->retrieve_metadata_property_types();
        $elements_array= array();
        $elements_array[] = Translation::get('ChooseAnElement');

        if ($element_set != false)
        {
            while ($element = $element_set->next_result())
            {
                $ns_id = $element->get_namespace();
                $ns_name = $prefixes_array[$ns_id];
                $element_name = $element->get_name();
                $combined = $ns_name.':'.$element_name;
                if(!in_array($combined, $this->preferences))
                {
                    $elements_array[$element->get_id()] = $ns_name.':'.$element_name;
                }

            }
        }

        return $elements_array;
    }

    function get_current_preferences()
    {
        $preference_importance = HandbookManager::get_publication_preferences_importance($this->publication_id);
        $html = array();
        foreach ($preference_importance as $key=>$preference)
        {
             $html[] = $key+1 . ' - ' .$preference . '</br>';
        }
        $this->preferences = $preference_importance;
    }

}

?>