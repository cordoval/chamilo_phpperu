<?php
namespace application\handbook;
use common\libraries\Request;
use common\libraries\Display;

require_once dirname(__FILE__) . '/handbook_viewer.class.php';

/**
 * Component to view a handbook and it's "light" content (only textual alternatives)
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookLightViewerComponent extends HandbookManagerHandbookViewerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        var_dump('run');
        //GET CONTENT OBJECTS TO DISPLAY
        $this->get_rights();
        if ($this->view_right)
        {
            $this->get_content_objects();
            $this->get_preferences($this->handbook_id);

            $this->display_header();


            //SHOW TEXT CONTENT
            $html[] = '<div>';
            $html[] = $this->display_text_content();
            $html[] = '</div>';


            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $this->display_header();
            $html[] = '<div>';
            $html[] = $this->display_not_allowed();
            $html[] = '</div>';
            echo implode("\n", $html);
            $this->display_footer();
        }
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