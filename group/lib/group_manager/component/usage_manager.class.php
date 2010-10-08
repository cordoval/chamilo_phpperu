<?php
class GroupManagerUsageManagerComponent extends GroupManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $menu = $this->get_menu_html();
        $output = $this->get_user_html();

        $this->display_header();
        echo $menu;
        echo $output;
        $this->display_footer();
    }

    function get_menu_html()
    {
        //$group_menu = new GroupMenu($this->get_group());
        //        $group_menu = new TreeMenu('GroupUsageTreeMenu', new GroupUsageTreeMenuDataProvider($this->get_url(), $this->get_group()));
        $html = array();
        $html[] = '<div style="float: left; width: 18%; overflow: auto; height: 500px;">';
        //        $html[] = $group_menu->render_as_tree();
        $html[] = 'MENU GOES HERE';
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_user_html()
    {
        $html = array();
        $html[] = '<div style="float: right; width: 80%;">';
        $html[] = 'CONTENT GOES HERE';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';

        return implode($html, "\n");
    }
}
?>