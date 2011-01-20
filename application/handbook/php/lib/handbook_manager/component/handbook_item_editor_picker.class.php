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
use repository\RepositoryDataManager;
use application\context_linker\ContextLinkerManager;
use application\context_linker\ContextLinkBrowserTable;

require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../forms/handbook_publication_form.class.php';
require_once dirname(__FILE__).'/handbook_alternatives_picker/handbook_alternatives_picker_table.class.php';
require_once dirname(__FILE__).'/../../../../../context_linker/php/lib/context_linker_manager/component/context_link_browser/context_link_browser_table.class.php';


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
//            $table = $this->get_choices();
            $html[] = $table;
            $this->display_header();
            echo implode("\n", $html);
            $this->display_footer();
	}

        function display_header()
        {

            $trail = new BreadcrumbTrail;
            $trail->add(new Breadcrumb($this->get_url(array(
                HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS)),
                    Translation :: get('Handbook')));
            $trail->add(new Breadcrumb($this->get_url(array(
                HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_VIEW_HANDBOOK,
                HandbookManager::PARAM_HANDBOOK_SELECTION_ID => Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID),
                HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID => Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID),
                HandbookManager::PARAM_TOP_HANDBOOK_ID => Request :: get(HandbookManager::PARAM_TOP_HANDBOOK_ID),
                HandbookManager::PARAM_HANDBOOK_ID => Request :: get(HandbookManager::PARAM_HANDBOOK_ID),
                HandbookManager::PARAM_COMPLEX_OBJECT_ID => Request :: get(HandbookManager::PARAM_COMPLEX_OBJECT_ID))),
                    Translation :: get('ViewHandbook')));
            $trail->add(new Breadcrumb($this->get_url(array(
                HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_PICK_ITEM_TO_EDIT,
                HandbookManager::PARAM_HANDBOOK_SELECTION_ID => Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID),
                HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID => Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID),
                HandbookManager::PARAM_TOP_HANDBOOK_ID => Request :: get(HandbookManager::PARAM_TOP_HANDBOOK_ID),
                HandbookManager::PARAM_HANDBOOK_ID => Request :: get(HandbookManager::PARAM_HANDBOOK_ID),
                HandbookManager::PARAM_COMPLEX_OBJECT_ID => Request :: get(HandbookManager::PARAM_COMPLEX_OBJECT_ID))),
                    Translation :: get('PickItemToEdit')));

            parent::display_header($trail);
        }


function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        return $action_bar;
    }

    private function get_choices()
    {
        $rdm = RepositoryDataManager::get_instance();
        $item_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
        $selected_object = $rdm->retrieve_content_object($item_id);
        $co_id = $selected_object->get_reference();
        $handbook_id = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
         $alternatives_array = HandbookManager::get_alternative_items($co_id);

        $html[] = 'test';
         $html[] = '</br>';
         $i=1;
         while ($alternatives_array != false && (count($alternatives_array) > 0) && list($key, $item) = each($alternatives_array))
        {

            $alternative_co = $rdm->retrieve_content_object($item[ContextLinkerManager :: PROPERTY_ALT_ID]);

            if ($alternative_co)
            {
                 $html[] = $i.': '. $alternative_co->get_title();
                 $html[] ='</br>';
                 $i++;
            }

        }
        return implode("\n", $html);
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

//        $rdm = RepositoryDataManager::get_instance();
//        $item_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
//        $selected_object = $rdm->retrieve_content_object($item_id);
//        $co_id = $selected_object->get_reference();
//
//            $table = new ContextLinkBrowserTable($this,
//                    array(Application :: PARAM_APPLICATION => 'handbook',
//                        Application :: PARAM_ACTION => HandbookManager :: ACTION_PICK_ITEM_TO_EDIT),
//                    $co_id);
//            return $table->as_html();
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