<?php
namespace application\context_linker;
use common\libraries\Application;
require_once dirname(__FILE__) . '/content_object_browser/content_object_browser_table.class.php';
/**
 * context_linker component which allows the user to browse his context_links
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class ContextLinkerManagerContentObjectsBrowserComponent extends ContextLinkerManager
{

    function run()
    {
        $this->display_header();

        $html = array();

        $html[] = $this->get_table();

        echo implode("\n", $html);

        $this->display_footer();
    }

    function get_table()
    {
        $table = new ContentObjectBrowserTable($this, array(Application :: PARAM_APPLICATION => 'context_linker', Application :: PARAM_ACTION => ContextLinkerManager :: ACTION_BROWSE_CONTENT_OBJECTS));
        return $table->as_html();
    }
}
?>