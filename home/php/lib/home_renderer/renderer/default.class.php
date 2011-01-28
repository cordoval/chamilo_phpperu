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
        $homepage = $this->render_homepage();
        
        $this->display_header();
        echo $homepage;
        $this->display_footer();
    }

    function render_homepage()
    {
        $current_tab = $this->get_current_tab();
        $user = $this->get_user();
        $user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

        // Get user id
        if ($user_home_allowed && $user instanceof User)
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
        if ($user_home_allowed && $user instanceof User && $tabs->size() == 0)
        {
            $this->create_user_home();

            $tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, $user->get_id());
            $tabs = HomeDataManager :: get_instance()->retrieve_home_tabs($tabs_condition);
        }

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

        if ($user_home_allowed && $user instanceof User)
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
//                    dump($column);
                    $columns_position = $columns->position();
                    
//                    dump($columns_position);

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
                            $block_component = Block :: factory($this, $block);
                              if ($block_component->is_visible())
                              {
                                    $html[] = $block_component->as_html();
                              }
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

        if ($user_home_allowed && $user instanceof User)
        {
            $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'libraries/resources/javascript/home_ajax.js' . '"></script>';
        }

        return implode("\n", $html);
    }

    function create_user_home()
    {
        $user = $this->get_user();

        $tabs_condition = new EqualityCondition(HomeTab :: PROPERTY_USER, '0');
        $tabs = HomeDataManager :: get_instance()->retrieve_home_tabs($tabs_condition);

        while ($tab = $tabs->next_result())
        {
            $old_tab_id = $tab->get_id();
            $tab->set_user($user->get_id());
            $tab->create();

            $rows_conditions = array();
            $rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_TAB, $old_tab_id);
            $rows_conditions[] = new EqualityCondition(HomeRow :: PROPERTY_USER, '0');
            $rows_condition = new AndCondition($rows_conditions);
            $rows = HomeDataManager :: get_instance()->retrieve_home_rows($rows_condition);

            while ($row = $rows->next_result())
            {
                $old_row_id = $row->get_id();
                $row->set_user($user->get_id());
                $row->set_tab($tab->get_id());
                $row->create();

                $conditions = array();
                $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $old_row_id);
                $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, '0');
                $condition = new AndCondition($conditions);

                $columns = HomeDataManager :: get_instance()->retrieve_home_columns($condition);

                while ($column = $columns->next_result())
                {
                    $old_column_id = $column->get_id();
                    $column->set_user($user->get_id());
                    $column->set_row($row->get_id());
                    $column->create();

                    $conditions = array();
                    $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_COLUMN, $old_column_id);
                    $conditions[] = new EqualityCondition(HomeBlock :: PROPERTY_USER, '0');
                    $condition = new AndCondition($conditions);

                    $blocks = HomeDataManager :: get_instance()->retrieve_home_blocks($condition);

                    while ($block = $blocks->next_result())
                    {
                        $block->set_user($user->get_id());
                        $block->set_column($column->get_id());
                        $block->create();
                    }
                }
            }
        }
    }
}
?>