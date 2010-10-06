<?php

/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';


/**
 * Component to view a handbook and it's content
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookViewerComponent extends HandbookManager
{

    private $handbook_id;

	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);

        parent::display_header();

        $html[] = '<div style="width: 18%; float: left; overflow: auto;">';
        $menu = new HandbookMenu( 'run.php?go='.self::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_HANDBOOK_ID.'='.$this->handbook_id, $this->handbook_id);
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="width: 18%; float: right; overflow: auto;">';
        $html[] = 'koekoek';
        $html[] = '</div>';

        echo implode ("\n", $html);
        parent::display_footer();
    }

    function get_allowed_content_object_types()
    {
        return array(Handbook :: get_type_name());
    }
}
?>