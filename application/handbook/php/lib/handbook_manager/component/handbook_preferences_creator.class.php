<?php


namespace application\handbook;

use common\libraries\Request;
use application\context_linker\ContextLinkForm;
use common\libraries\Translation;

require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../forms/handbook_preference_form.class.php';

/**
 * Description of handbook_preferences_viewer
 *
 * @author nblocry
 */
class HandbookManagerHandbookPreferencesCreatorComponent extends HandbookManager
{
   

    /**
    * Runs this component and displays its output.
    */
    function run()
    {
        $publication_id = Request::get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
        //for now just display the hardcoded preferences
        //DISPLAY PREFERENCES
        //TODO: add buttons to remove and change importance
        $preference_importance = HandbookManager::get_publication_preferences_importance($publication_id);
        $html = array();
//        foreach ($preference_importance as $key=>$preference)
//        {
//            $html[] = $key . ' - ' .$preference . '</br>';
//        }
        $i=1;
        $max = count($preference_importance);
        while($i<=$max)
        {
            $html[] = $i . ' - ' .$preference_importance[$i] . '</br>';
            $i++;
        }

        //DISPLAY FORM TO ADD PREFERENCES
        $action = $this->get_url(array(HandbookManager::PARAM_ACTION => HandbookManager::ACTION_CREATE_PREFERENCE, HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID=>$publication_id));
        $preference_form = new HandbookPreferenceForm($publication_id, $action);

         if($preference_form->validate())
        {
            $metadata_id = $preference_form->exportValue('metadata');

            $pref = new HandbookPreference();
            $pref->set_metadata_property_type_id($metadata_id);
            $pref->set_handbook_publication_id($publication_id);
            $success = $pref->create();
            $this->redirect($success ? Translation :: get('PreferenceAdded'): Translation :: get('PreferenceNotAdded'), !$success, array(HandbookManager::PARAM_ACTION => HandbookManager::ACTION_CREATE_PREFERENCE, HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID=>$publication_id));

        }
        else
        {
            $this->display_header();
            $html[] =  $preference_form->toHtml();
            echo implode("\n", $html);

            $this->display_footer();
        }
//           

    }

    function create_preference($preference_form)
    {
       $values = $preference_form->exportValues();
       //TODO implement
       return true;
    }
}
?>
