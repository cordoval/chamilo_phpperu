<?php

namespace home;

use common\libraries\Translation;
use common\libraries\AdministrationComponent;
use common\libraries\Block;
use common\libraries\Display;

use common\libraries\Application;
use common\libraries\CoreApplication;
use common\libraries\WebApplication;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\BreadcrumbTrail;
use common\libraries\AndCondition;
use common\libraries\Authentication;
use common\libraries\Theme;

/**
 * @package home.lib.home_manager.component
 *
 */
class HomeManagerWidgetViewerComponent extends HomeManager {

    /**
     *
     * @return Block
     */
    function get_block() {
        $id = Request :: get(HomeManager :: PARAM_HOME_ID);
        $block_info = $id ? $this->retrieve_home_block($id) : null;
        $block_component = $block_info ? Block :: factory($this, $block_info) : null;
        return $block_component;
    }

    /**
     * Runs this component and displays its output.
     */
    function run() {
        $block = $this->get_block();

        if (empty($block)) {
            $this->display_error_page(htmlentities(Translation :: get('NoHomeBlockSelected')));
            return;
        }

        $url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_CONFIGURE_HOME, HomeManager :: PARAM_HOME_ID => $id));

        $icon = $block->get_icon();
        //$header = '<link rel="shortcut icon" href="' . $icon .  '" type="image/x-icon"/>';

        Display::small_header(null, 'body {background-color:white; padding:0px;}');
        echo $block->is_visible() ? $block->as_html(Block::WIDGET_VIEW) : '';
        Display::small_footer();
    }

    function get_additional_parameters() {
        return array(HomeManager :: PARAM_HOME_ID);
    }

}

?>