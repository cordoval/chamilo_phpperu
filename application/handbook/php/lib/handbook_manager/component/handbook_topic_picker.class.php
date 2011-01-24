<?php
namespace application\handbook;
use common\libraries\Request;
use common\libraries\Display;

require_once dirname(__FILE__) . '/handbook_viewer.class.php';

/**
 * Component to view a handbook and it's "light" content (only textual alternatives)
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookTopicPickerComponent extends HandbookManagerHandbookViewerComponent
{

    protected $handbook_id;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->handbook_id = Request::get('top');
        var_dump($this->handbook_id);

        if ($this->handbook_id == null)
        {
            //VOORLOPIG OM TE TESTEN FF
            $this->handbook_id = 41;
            
        }
        $menu = new HandbookMenu($this->handbook_id);
        $html[] = $menu->render_as_tree();

       
        $this->display_header();
        echo implode("\n", $html);
            $this->display_footer();
    }

    function display_header()
    {
        Display :: small_header();
    }

    function display_footer()
    {

        Display :: small_footer();
    }

}

?>