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
use repository\ContentObjectDisplay;
use common\libraries\Display;

require_once dirname(__FILE__) . '/../handbook_manager.class.php';
//require_once dirname(__FILE__) . '/../../forms/handbook_publication_form.class.php';
//require_once dirname(__FILE__) . '/handbook_alternatives_picker/handbook_alternatives_picker_table.class.php';
//require_once dirname(__FILE__) . '/../../../../../context_linker/php/lib/context_linker_manager/component/context_link_browser/context_link_browser_table.class.php';

/**
 * Display an item topic. That is the default's html for the content object with small header/footer.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author lopprecht
 * @package handbook.block
 */
class HandbookManagerHandbookItemSimpleViewerComponent extends HandbookManager {

    /**
     * Runs this component and displays its output.
     */
    function run() {
        $this->display_header(null, false);
        $this->display_content();
        $this->display_footer();
    }

    function has_menu(){
        return false;
    }

    function display_header() {
        Display :: small_header();
    }

    function display_footer(){
        Display::small_footer();
    }

    function display_content(){
        $id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
        $child = RepositoryDataManager::get_instance()->retrieve_content_object($id);
        $display = ContentObjectDisplay :: factory($child);
        echo $display->get_full_html();
    }

    function get_menu(){
        return '';
    }

}

?>