<?php
/**
 * @author Hans De Bisschop
 */
class DefaultHomeRenderer extends HomeRenderer
{

    /**
     * @return string
     */
    function render()
    {
        $this->display_header();
        echo $this->render_homepage();
        $this->display_footer();
    }

    function render_homepage()
    {
        $current_tab = $this->get_current_tab();
        $user = $this->get_user();

        $user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

        // Get user id
        if ($user_home_allowed && Authentication :: is_valid())
        {
            $user_id = $user->get_id();
        }
        else
        {
            $user_id = '0';
        }

        $tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user_id);
        $tabs = HomeDataManager :: get_instance()->retrieve_home_tabs($tabs_condition);

        // If the homepage can be personalised but we have no rows, get the
        // default (to prevent lockouts) and display a warning / notification
        // which tells the user he can personalise his homepage
        if ($user_home_allowed && Authentication :: is_valid() && $tabs->size() == 0)
        {
            $this->create_user_home();

            $tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user->get_id());
            $tabs = HomeDataManager :: get_instance()->retrieve_home_tabs($tabs_condition);
        }

        //if ($tabs->size() > 1)
        //{
        $html[] = '<div id="tab_menu"><ul id="tab_elements">';
        while ($tab = $tabs->next_result())
        {
            $tab_id = $tab->get_id();

            if (($tab_id == $current_tab) || ($tabs->position() == 'single') || (! isset($current_tab) && $tabs->position() == 'first'))
            {
                $class = 'current';
            }
            else
            {
                $class = 'normal';
            }

            $html[] = '<li class="' . $class . '" id="tab_select_' . $tab->get_id() . '"><a class="tabTitle" href="' . $this->get_home_tab_viewing_url($tab) . '">' . $tab->get_title() . '</a><a class="deleteTab"><img src="' . Theme :: get_image_path() . 'action_delete_tab.png" /></a></li>';
        }
        $html[] = '</ul>';

        if ($user_home_allowed && Authentication :: is_valid())
        {
            $html[] = '<div id="tab_actions">';
            $html[] = '<a class="addTab" href="#"><img src="' . Theme :: get_image_path() . 'action_add_tab.png" />&nbsp;' . Translation :: get('NewTab') . '</a>';
            $html[] = '<a class="addColumn" href="#"><img src="' . Theme :: get_image_path() . 'action_add_column.png" />&nbsp;' . Translation :: get('NewColumn') . '</a>';
            $html[] = '<a class="addEl" style="display: none;" href="#"><img src="' . Theme :: get_image_path() . 'action_add_block.png" />&nbsp;' . Translation :: get('NewBlock') . '</a>';
            $html[] = '</div>';
        }

        $html[] = '<div style="font-size: 0px; clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
        //}


        $tabs = HomeDataManager :: get_instance()->retrieve_home_tabs($tabs_condition);

        while ($tab = $tabs->next_result())
        {
            $html[] = '<div class="tab" id="tab_' . $tab->get_id() . '" style="display: ' . (((! isset($current_tab) && ($tabs->position() == 'first' || $tabs->position() == 'single')) || $current_tab == $tab->get_id()) ? 'block' : 'none') . ';">';

            $rows_conditions = array();
            $rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_TAB, $tab->get_id());
            $rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_USER, $user_id);
            $rows_condition = new AndCondition($rows_conditions);
            $rows = HomeDataManager :: get_instance()->retrieve_home_rows($rows_condition);

            while ($row = $rows->next_result())
            {
                $rows_position = $rows->position();
                $html[] = '<div class="row" id="row_' . $row->get_id() . '" style="' . ($rows_position != 'last' ? 'margin-bottom: 1%;' : '') . '">';

                $conditions = array();
                $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row->get_id());
                $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
                $condition = new AndCondition($conditions);

                // Get the user or platform columns
                $columns = HomeDataManager :: get_instance()->retrieve_home_columns($condition);

                while ($column = $columns->next_result())
                {
                    $columns_position = $columns->position();

                    $html[] = '<div class="column" id="column_' . $column->get_id() . '" style="width: ' . $column->get_width() . '%;' . ($columns_position != 'last' ? ' margin-right: 1%;' : '') . '">';

                    $conditions = array();
                    $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $column->get_id());
                    $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, $user_id);
                    $condition = new AndCondition($conditions);

                    $blocks = HomeDataManager :: get_instance()->retrieve_home_blocks($condition);

                    if ($blocks->size() > 0)
                    {
                        while ($block = $blocks->next_result())
                        {
                            $html[] = Block :: factory($this, $block)->as_html();
//                            $html[] = $block->get_application();
                            //                            $application = $block->get_application();
                        //                            $application_class = Application :: application_to_class($application);
                        //
                        //                            if (! WebApplication :: is_application($application))
                        //                            {
                        //                                $sys_app_path = CoreApplication :: get_application_path($application) . '/lib/' . $application . '_manager' . '/' . $application . '_manager.class.php';
                        //                                require_once $sys_app_path;
                        //
                        //                                $application_class .= 'Manager';
                        //
                        //                                if (! is_null($this->get_user()))
                        //                                {
                        //                                    $app = new $application_class($this->get_user());
                        //                                    $html[] = $app->render_block($block);
                        //                                }
                        //                                elseif (($application == 'user' && $block->get_component() == 'login') || ($application == 'admin' && $block->get_component() == 'portal_home'))
                        //                                {
                        //                                    $app = new $application_class($this->get_user());
                        //                                    $html[] = $app->render_block($block);
                        //                                }
                        //                            }
                        //                            else
                        //                            {
                        //                                require_once WebApplication :: get_application_manager_path($application);
                        //
                        //                                if (! is_null($this->get_user()))
                        //                                {
                        //                                    $app = Application :: factory($application, $this->get_user());
                        //                                    $html[] = $app->render_block($block);
                        //                                }
                        //                            }
                        }
                    }
                    else
                    {
                        $html[] = '<div class="empty_column">';
                        $html[] = Translation :: get('EmptyColumnText');
                        $html[] = '<div class="deleteColumn"></div>';
                        $html[] = '</div>';
                    }

                    $html[] = '</div>';
                }

                $html[] = '</div>';
                $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';
            }

            $html[] = '</div>';
        }

        $html[] = '<div style="clear: both; height: 0px; line-height: 0px;">&nbsp;</div>';

        if ($user_home_allowed && Authentication :: is_valid())
        {
            //$html[] = '<script type="text/javascript" src="' . BasicApplication::get_application_resources_javascript_path(HomeManager::APPLICATION_NAME) . 'home_ajax.js' . '"></script>';
        }

        return implode("\n", $html);
    }
}
?>