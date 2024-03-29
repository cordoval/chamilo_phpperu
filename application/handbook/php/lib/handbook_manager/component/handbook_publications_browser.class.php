<?php

namespace application\handbook;

use repository\content_object\handbook\Handbook;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use common\libraries\ActionBarSearchForm;
use common\libraries\Application;
use common\libraries\PatternMatchCondition;
use common\libraries\AndCondition;
use rights\RightsUtilities;

require_once dirname(__FILE__) . '/../handbook_manager.class.php';
require_once dirname(__FILE__) . '/../../handbook_publication.class.php';

/**
 * handbook component which allows the user to browse his handbook_publications
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookPublicationsBrowserComponent extends HandbookManager
{

    private $action_bar;

    function run()
    {
        $this->action_bar = $this->get_action_bar();
        $output = $this->get_publications_html();

        //
        //            echo '<a href="' . $this->get_create_handbook_publication_url() . '">' . Translation :: get('CreateObject', array('OBJECT' => Translation::get('HandbookPublication')), Utilities::COMMON_LIBRARIES) . '</a>';
        //            echo '<br /><br />';


        $this->display_header();

        echo $this->action_bar->as_html();
        echo '<div class="clear"></div>';

        echo '<div style="width: 12%; overflow: auto; float: left;">';

        echo '</div><div style="width: 85%; float: right;">';
        echo $output;
        echo '</div>';

        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());



        $user_id = $this->get_user_id();
        $publish_right = RightsUtilities::is_allowed(HandbookRights::PUBLISH_RIGHT, 0, 0, self::APPLICATION_NAME, $user_id);
        if ($publish_right)
        {

            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishObject', array(
                                'OBJECT' => Translation :: get('HandbookPublication')), Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_handbook_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Import'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_IMPORT))));
        }

        

        return $action_bar;
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[Application :: PARAM_APPLICATION] = 'handbook';
        $parameters[Application :: PARAM_ACTION] = HandbookManager :: ACTION_BROWSE;

        $table = new HandbookPublicationBrowserTable($this, array(
                    Application :: PARAM_APPLICATION => 'handbook',
                    Application :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE), $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        $conditions = array();
        $search = $this->action_bar->get_query();
        if (isset($search) && $search != '')
        {
            $conditions[] = new PatternMatchCondition(Handbook :: PROPERTY_TITLE, '*' . $search . '*');
        }
        $condition = new AndCondition($conditions);
        return $condition;
    }

}

?>