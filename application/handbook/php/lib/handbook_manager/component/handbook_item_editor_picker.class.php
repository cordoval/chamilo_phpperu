<?php
namespace application\handbook;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ActionBarRenderer;
use common\libraries\ActionBarSearchForm;
use common\libraries\Application;
use common\libraries\PatternMatchCondition;
use common\libraries\AndCondition;
use repository\ContentObject;

require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../forms/handbook_publication_form.class.php';
require_once dirname(__FILE__).'/handbook_alternatives_picker/handbook_alternatives_picker_table.class.php';
/**
 * Component to pick a version of a handbook-topic to edit
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookItemEditorPickerComponent extends HandbookManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
            $this->action_bar = $this->get_action_bar();
            $table = $this->get_table();
            $html[] = $table;
            $this->display_header();
            echo implode("\n", $html);
            $this->display_footer();
	}


function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }

        private function get_table()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[Application :: PARAM_APPLICATION] = 'handbook';
        $parameters[Application :: PARAM_ACTION] = HandbookManager :: ACTION_BROWSE;

        $table = new HandbookAlternativesPickerItemTable($this, array(
                Application :: PARAM_APPLICATION => 'handbook',
                Application :: PARAM_ACTION => HandbookManager :: ACTION_PICK_ITEM_TO_EDIT), $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {

        $conditions = array();

        //        $conditions[] = new EqualityCondition('1', '1');


        $search = $this->action_bar->get_query();

        if (isset($search) && $search != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject:: PROPERTY_TITLE, '*' . $search . '*');
        }

        //        $conditions[] = new SubselectCondition(ContentObject::PROPERTY_ID, HandbookPublication::PROPERTY_CONTENT_OBJECT_ID, HandbookPublication::get_table_name(), null, null, HandbookDataManager::get_instance());


        $condition = new AndCondition($conditions);
        return $condition;
    }
}
?>