<?php
/**
 * $Id: column_add.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
$this_section = 'home';

require_once dirname(__FILE__) . '/../../common/global.inc.php';

Utilities :: set_application($this_section);

$json_result = array();

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();
    $row_data = explode('_', $_POST['row']);
    $row_id = $row_data[1];

    if (isset($_POST['row']))
    {
        // Retrieve the columns of the current row to alter their width
        $conditions = array();
        $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row_id);
        $conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
        $conditions[] = new InequalityCondition(HomeColumn :: PROPERTY_WIDTH, InequalityCondition :: GREATER_THAN_OR_EQUAL, '25');
        $condition = new AndCondition($conditions);

        $width_conditions = array();
        $width_conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row_id);
        $width_conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
        $width_condition = new AndCondition($width_conditions);

        $hdm = HomeDataManager :: get_instance();
        $columns_width = $hdm->retrieve_home_columns($width_condition);
        $width_total = $columns_width->size() - 1;
        while ($col = $columns_width->next_result())
        {
            $width_total += $col->get_width();
        }
        $columns = $hdm->retrieve_home_columns($condition, null, null, array(new ObjectTableOrder(HomeColumn :: PROPERTY_WIDTH, SORT_DESC)));

        // Create the new column + a dummy block for it
        $new_column = new HomeColumn();
        $new_column->set_row($row_id);
        $new_column->set_title(Translation :: get('NewColumn'));
        $new_column->set_width('19');
        $new_column->set_user($user_id);
        if (! $new_column->create())
        {
            $json_result['success'] = '0';
            $json_result['message'] = Translation :: get('ColumnNotAdded');
        }

        $block = new HomeBlock();
        $block->set_column($new_column->get_id());
        $block->set_title(Translation :: get('DummyBlock'));
        $block->set_application('repository');
        $block->set_component('linker');
        $block->set_visibility('1');
        $block->set_user($user_id);
        if (! $block->create())
        {
            $json_result['success'] = '0';
            $json_result['message'] = Translation :: get('ColumnBlockNotAdded');
        }

        	$user = UserDataManager :: get_instance()->retrieve_user($user_id);
//    $usermgr = new UserManager($user_id);
//    $user = $usermgr->get_user();
        

        $application = $block->get_application();
        $application_class = Application :: application_to_class($application);

        if (! WebApplication :: is_application($application))
        {
            $path = Path :: get(SYS_PATH) . $application . '/lib/' . $application . '_manager' . '/' . $application . '_manager.class.php';
            require_once $path;
            $application_class .= 'Manager';
            $app = new $application_class($user);
        }
        else
        {
            $path = Path :: get_application_path() . 'lib' . '/' . $application . '/' . $application . '_manager' . '/' . $application . '_manager.class.php';
            require_once $path;
            $app = Application :: factory($application, $user);
        }

        // Render the actual html to be displayed
        $html[] = '<div class="column" id="column_' . $new_column->get_id() . '" style="width: ' . $new_column->get_width() . '%;">';
        $html[] = $app->render_block($block);
        $html[] = '</div>';

        // Start writing the JSON response object
        $json_result['html'] = implode("\n", $html);

        // Update the older columns width and add them to the JSON object
        $counter = 20;
        if ($width_total < 100)
        {
            $counter = $counter - (100 - $width_total);
        }

        while ($column = $columns->next_result())
        {
            if ($counter > 0)
            {
                if ($columns->size() > 1)
                {
                    if ($counter >= 10)
                    {
                        $column->set_width(($column->get_width() - 10));
                        $counter = $counter - 10;
                    }
                    else
                    {
                        $column->set_width(($column->get_width() - $counter));
                        $counter = 0;
                    }
                }
                else
                {
                    $column->set_width(($column->get_width() - $counter));
                }
                $column->update();
            }
        }

        $width_conditions = array();
        $width_conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_ROW, $row_id);
        $width_conditions[] = new EqualityCondition(HomeColumn :: PROPERTY_USER, $user_id);
        $width_condition = new AndCondition($width_conditions);

        $columns_width = $hdm->retrieve_home_columns($width_condition);
        while ($col = $columns_width->next_result())
        {
            $json_result['width']['column_' . $col->get_id()] = $col->get_width();
        }

        // Finally add the new column we added
        $json_result['success'] = '1';
        $json_result['message'] = Translation :: get('ColumnAdded');
    }
}
else
{
    $json_result['success'] = '0';
    $json_result['message'] = Translation :: get('NotAuthorized');
}
echo json_encode($json_result);
?>