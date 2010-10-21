<?php
/**
 * @package application.handbook.handbook.component
 */

require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_publication.class.php';

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



            echo '<a href="' . $this->get_create_handbook_publication_url() . '">' . Translation :: get('CreateHandbookPublication') . '</a>';
            echo '<br /><br />';

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
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_create_handbook_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));


        return $action_bar;
    }

    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[Application :: PARAM_APPLICATION] = 'handbook';
        $parameters[Application :: PARAM_ACTION] =  HandbookManager:: ACTION_BROWSE;


        $table = new HandbookPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'handbook', Application :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE), $this->get_condition());

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
            $conditions[] = new PatternMatchCondition(Handbook::PROPERTY_TITLE, '*' . $search . '*');
        }

//        $conditions[] = new SubselectCondition(ContentObject::PROPERTY_ID, HandbookPublication::PROPERTY_CONTENT_OBJECT_ID, HandbookPublication::get_table_name(), null, null, HandbookDataManager::get_instance());

        $condition = new AndCondition($conditions);
        return $condition;
    }


}
?>