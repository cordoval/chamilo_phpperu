<?php


namespace application\handbook;

use common\libraries\Request;
use application\context_linker\ContextLinkForm;

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
        //for now just display the hardcoded preferences
        //DISPLAY PREFERENCES
        $preference_importance = HandbookManager::get_publication_preferences_importance();
        $html = array();
        foreach ($preference_importance as $key=>$preference)
        {
            $html[] = $key+1 . ' - ' .$preference . '</br>';
        }

        //DISPLAY FORM TO ADD PREFERENCES
//        //todo_implement!
//        $preference_form = new HandbookPreferenceForm();
//
//         if($preference_form->validate())
//        {
//
//        }
//        else
//        {
//            $this->display_header();
//            echo $preference_form->toHtml();
//
//            $this->display_footer();
//        }
            $this->display_header();
            echo implode("\n", $html);
            $this->display_footer();

    }

    function create_preference($preference_form)
    {
       $values = $preference_form->exportValues();
       //TODO implement
       return true;
    }
}
?>
