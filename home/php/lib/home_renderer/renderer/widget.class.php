<?php

namespace home;

use common\libraries\BasicApplication;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\Theme;
use common\libraries\Block;
use common\libraries\PlatformSetting;
use common\libraries\Path;
use user\User;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\Application;
use common\libraries\Session;

/**
 * Dislay a block in Widget view. That to be displayed in a third part application/portal. I.e. displays only the widget.
 */
class WidgetHomeRenderer extends HomeRenderer {

    function get_user_id() {
        return Session::get_user_id();
    }

    /**
     *
     * @return HomeBlock|null
     */
    function get_home_block($id) {
        $result =  HomeDataManager :: get_instance()->retrieve_home_block($id);
        return $result;
    }

    /**
     *
     * @return Block|null
     */
    function get_block(BlockInfo $block_info) {
        if(empty($block_info)){
            return null;
        }
        //i.e. user setttings can be overwritten by passing them in the url.
        $items = $block_info->get_configuration();
        //foreach($items as $key=>$value){
        //    $items[$key] = Request::get($key, $value);
        //}
        $result = $block_info ? Block :: factory($this, $block_info, $configuration) : null;
        return $result;
    }

    function is_login_required($block_info) {
        return $block_info->get_user() != 0 || $block_info->get_user() != $this->get_user_id();
    }

    function is_anonymous() {
        return $this->get_user_id() == 0;
    }

    function is_authorized($block_info) {
        return $block_info->get_user() == 0 || $block_info->get_user() == $this->get_user_id();
    }

    /**
     * @return string
     */
    function render() {
        $id = Request :: get(HomeRenderer :: PARAM_WIDGET_ID);
        if (empty($id)) {
            $this->display_error_page(htmlentities(Translation :: get('NoHomeBlockSelected')));
            return;
        }

        $block_info = $this->get_home_block($id);
        if (empty($block_info)) {
            $this->display_error_page(htmlentities(Translation :: get('InvalidBlockId')));
            return;
        }
        if ($this->is_login_required($block_info) && $this->is_anonymous()) {
            $this->display_login_page();
            return;
        }

        if (!$this->is_authorized($block_info)) {
            $this->display_error_page(htmlentities(Translation :: get('NotAuthorized')));
            return;
        }

        $block = $this->get_block($block_info);
        $this->display_block($block);
    }

    function retrieve_home_block($id) {
        return HomeDataManager :: get_instance()->retrieve_home_block($id);
    }

    /**
     * Displays an error page.
     * @param string $message The message.
     */
    function display_error_page($message) {
        Display::small_header();
        Display::error_message($message);
        Display::small_footer();
    }

    function display_login_page() {
        Display::small_header();
        $_SESSION['request_uri'] = $_SERVER['REQUEST_URI']; //required to avoid being redirected to home page
        echo Display::display_login_form();
        Display::small_footer();
        return;
    }

    function display_block($block) {
        //$url = $this->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_CONFIGURE_HOME, HomeRenderer :: PARAM_WIDGET_ID => $id));
        //$icon = $block->get_icon();
        Display::small_header(null, 'body {background-color:white; padding:0px;}');
        echo $block->is_visible() ? $block->as_html(Block::WIDGET_VIEW) : '';
        Display::small_footer();
    }

    function get_additional_parameters() {
        return array(HomeRenderer :: PARAM_WIDGET_ID);
    }
}

?>