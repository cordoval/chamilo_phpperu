<?php
/**
 * $Id: laika_graph_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

require_once Path :: get_application_path() . 'lib/laika/laika_data_manager.class.php';

require_once Path :: get_plugin_path() . '/pChart/pChart/pChart.class';
require_once Path :: get_plugin_path() . '/pChart/pChart/pData.class';

class LaikaGraphRenderer
{
    const RENDER_GRAPH_AND_TABLE = 'gt';
    const RENDER_GRAPH = 'g';
    const RENDER_TABLE = 't';

    const RENDER_ATTEMPT_LAST = SORT_DESC;
    const RENDER_ATTEMPT_FIRST = SORT_ASC;
    const RENDER_ATTEMPT_ALL = 'all';

    private $groups;
    private $codes;
    private $scales;
    private $start_date;
    private $end_date;

    private $type;
    private $save;
    private $attempt;
    private $url_format;

    private $current_group;

    function LaikaGraphRenderer($groups = array(), $scales = array(), $codes = array())
    {
        $this->set_groups($groups);
        $this->current_group = null;
        $this->set_codes($codes);
        $this->set_scales($scales);
        $this->start_date = strtotime("-1 year");
        $this->end_date = time();

        $this->type = self :: RENDER_GRAPH_AND_TABLE;
        $this->attempt = self :: RENDER_ATTEMPT_FIRST;
        $this->save = false;

        $this->url_format = '?application=laika&go=browse&filter_scale=%s&filter_code=%s&filter_group=%s';
    }

    function set_groups($groups)
    {
        if ($groups == 0)
        {
            $this->groups = array();
        }
        else
        {
            if (! is_array($groups))
            {
                $groups = array($groups);
            }

            $gdm = GroupDataManager :: get_instance();
            $condition = new InCondition(Group :: PROPERTY_ID, $groups);

            $this->groups = $gdm->retrieve_groups($condition);
        }
    }

    function set_group($group)
    {
        $this->groups = array($group);
    }

    function set_current_group($group)
    {
        $this->current_group = $group;
    }

    function set_codes($codes)
    {
        $this->codes = $codes;
    }

    function set_scales($scales = array())
    {
        if (! is_array($scales))
        {
            $scales = array($scales);
        }

        if (empty($scales))
        {
            $scales = $ldm->retrieve_laika_scales();
        }
        else
        {
            $ldm = LaikaDataManager :: get_instance();
            $condition = new InCondition(LaikaScale :: PROPERTY_ID, $scales);

            $scales = $ldm->retrieve_laika_scales($condition, null, null, new ObjectTableOrder(LaikaScale :: PROPERTY_TITLE));
        }

        $this->scales = $scales->as_array();
    }

    function set_start_date($start_date)
    {
        $this->start_date = $start_date;
    }

    function set_end_date($end_date)
    {
        $this->end_date = $end_date;
    }

    function set_type($type)
    {
        $this->type = $type;
    }

    function set_attempt($attempt)
    {
        $this->attempt = $attempt;
    }

    function get_groups()
    {
        return $this->groups;
    }

    function get_current_group()
    {
        return $this->current_group;
    }

    function get_codes()
    {
        return $this->codes;
    }

    function get_scales()
    {
        return $this->scales;
    }

    function get_start_date()
    {
        return $this->start_date;
    }

    function get_end_date()
    {
        return $this->end_date;
    }

    function get_type()
    {
        return $this->type;
    }

    function get_attempt()
    {
        return $this->attempt;
    }

    function save()
    {
        $this->save = true;
    }

    function render_graphs()
    {
        $gdm = GroupDataManager :: get_instance();
        $groups = $this->get_groups();

        $html = array();

        if (count($groups) == 0)
        {
            $html[] = $this->render_result();
        }
        else
        {
            while ($group = $groups->next_result())
            {
                $this->set_current_group($group);
                $html[] = $this->render_result();
            }
        }

        $display_html = implode("\n", $html);

        $user_id = Session :: get_user_id();
        $save = $this->save;

        if ($save)
        {
            $repo_path = Path :: get(SYS_REPO_PATH);
            $owner_path = $repo_path . $user_id;
            Filesystem :: create_dir($owner_path);

            $filename = Translation :: get('LaikaResults') . '.html';
            $filename = Filesystem :: create_unique_name($owner_path, $filename);
            $path = $user_id . '/' . $filename;
            $full_path = $repo_path . $path;
            Filesystem :: write_to_file($full_path, strip_tags($display_html, '<table><tr><td><th><div><span><img>'));

            $html_object = new Document();
            $html_object->set_title(Translation :: get('LaikaResults'));
            $html_object->set_description(Translation :: get('LaikaResultsHTML'));
            $html_object->set_parent_id(0);
            $html_object->set_owner_id($user_id);
            $html_object->set_path($path);
            $html_object->set_filename($filename);
            $html_object->set_filesize(Filesystem :: get_disk_space($full_path));
            $html_object->create();
        }

        return $display_html;
    }

    function render_result()
    {
        $graph_data = array();
        $graph_data[] = $this->get_data();
        $graph_data[] = $this->get_config();

        $type = $this->type;

        $html = array();
        $html[] = '<div class="configuration_form">';
        $html[] = '<span class="category">' . $graph_data[1]['Title'] . '</span>';
        $html[] = '<div class="clear"></div>';

        switch ($type)
        {
            case self :: RENDER_GRAPH_AND_TABLE :
                $html[] = $this->render_table($graph_data);
                $html[] = $this->render_graph($graph_data);
                break;
            case self :: RENDER_GRAPH :
                $html[] = $this->render_graph($graph_data);
                break;
            case self :: RENDER_TABLE :
                $html[] = $this->render_table($graph_data);
                break;
            default :
                $html[] = $this->render_table($graph_data);
                $html[] = $this->render_graph($graph_data);
                break;
        }
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    function render_table($graph_data)
    {
        $url_format = $this->url_format;
        $table_data = array();

        // Set the headers
        $table_header = array();
        $table_header[] = array(Translation :: get('Scale'), false, 'class="graph_header"');
        $codes = $this->get_codes();
        foreach ($codes as $code)
        {
            $table_header[] = array($code, false, 'class="graph_header"');
        }

        // Set the table data
        $scales = $this->get_scales();
        foreach ($scales as $scale)
        {
            $row = array();
            $row[] = $scale->get_title();
            foreach ($codes as $code)
            {
                $current_group = $this->get_current_group();
                if (is_null($current_group))
                {
                    $group_id = 0;
                }
                else
                {
                    $group_id = $current_group->get_id();
                }
                $row[] = '<a href="' . sprintf($url_format, $scale->get_id(), $code, $group_id) . '">' . $graph_data[0][$code - 1][$scale->get_id()] . '</a>';
            }
            $table_data[] = $row;
        }

        $table = new SortableTableFromArray($table_data, 0, 20, '');
        foreach ($table_header as $index => $header_item)
        {
            $table->set_header($index, $header_item[0], $header_item[1], $header_item[2], $header_item[3]);
        }

        return '<div style="float: left; margin-right: 20px;">' . $table->as_html() . '</div>';
    }

    function render_graph($graph_data)
    {
        $user_id = Session :: get_user_id();
        $save = $this->save;

        $image_id = md5(serialize($graph_data));
        $image_path = Path :: get(SYS_FILE_PATH) . 'temp/';
        $image_file = 'chart_laika_' . $image_id . '.png';

        $alt_title = Translation :: get('LaikaResults') . ' - ' . $graph_data[1]['Title'];

        if (! file_exists($image_path . $image_file))
        {
            $font = Path :: get_plugin_path() . '/pChart/Fonts/tahoma.ttf';

            $graph = new pChart(840, 460);
            $graph->loadColorPalette(Path :: get(SYS_LAYOUT_PATH) . Theme :: get_theme() . '/plugin/pchart/tones.txt');
            $graph->setFontProperties($font, 8);
            $graph->setGraphArea(70, 50, 625, 400);
            $graph->drawFilledRoundedRectangle(7, 7, 833, 453, 5, 240, 240, 240);
            $graph->drawRoundedRectangle(5, 5, 835, 455, 5, 230, 230, 230);
            $graph->drawGraphArea(255, 255, 255, TRUE);
            $graph->drawScale($graph_data[0], $graph_data[1], SCALE_START0, 150, 150, 150, TRUE, 0, 2, TRUE);
            $graph->drawGrid(4, TRUE, 230, 230, 230, 50);

            // Draw the 0 line
            $graph->setFontProperties($font, 6);
            $graph->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

            // Draw the bar graph
            $graph->drawBarGraph($graph_data[0], $graph_data[1], TRUE, 75);

            // Finish the graph
            $graph->setFontProperties($font, 8);
            $graph->drawLegend(660, 50, $graph_data[1], 255, 255, 255);
            $graph->setFontProperties($font, 10);
            $graph->drawTitle(50, 32, $graph_data[1]['Title'], 50, 50, 50, 585);

            $graph->Render($image_path . $image_file);
        }

        if ($save)
        {
            $repo_path = Path :: get(SYS_REPO_PATH);
            $owner_path = $repo_path . $user_id;
            Filesystem :: create_dir($owner_path);

            $filename = Filesystem :: create_unique_name($owner_path, $image_file);
            $path = $user_id . '/' . $filename;
            $full_path = $repo_path . $path;
            copy($image_path . $image_file, $full_path) or die('Failed to create "' . $full_path . '"');

            $setting = 0777;
            /*$ad = PlatformSetting :: get('permissions_new_files');
			if($ad && $ad != '')
				$setting = $ad;*/

            chmod($full_path, $setting);
            $object = new Document();
            $object->set_title($alt_title);
            $object->set_parent_id(0);
            $object->set_owner_id($user_id);
            $object->set_path($path);
            $object->set_filename($filename);
            $object->set_filesize(Filesystem :: get_disk_space($full_path));
            $object->create();

            $web_path = Path :: get(WEB_REPO_PATH) . $path;
        }
        else
        {
            $web_path = Path :: get(WEB_FILE_PATH) . 'temp/' . $image_file;
        }

        return '<div style="float: left;"><img src="' . $web_path . '" border="0" alt="' . $alt_title . '" title="' . $alt_title . '" /></div>';
    }

    function get_config()
    {
        $config = array();

        $group = $this->get_current_group();

        if (! is_null($group))
        {
            $config['Title'] = $group->get_name();
        }
        else
        {
            $config['Title'] = Translation :: get('AllGroups');
        }

        $config['Position'] = 'Name';
        $config['Axis'] = array('X' => Translation :: get('PercentileCodes'), 'Y' => Translation :: get('Number'));
        $config['Values'] = array();
        $config['Description'] = array();

        $scales = $this->get_scales();

        foreach ($scales as $scale)
        {
            $config['Values'][] = $scale->get_id();
            $config['Description'][$scale->get_id()] = html_entity_decode($scale->get_title());
        }

        return $config;
    }

    function get_data()
    {
        $ldm = LaikaDataManager :: get_instance();

        $data = array();

        $group = $this->get_current_group();
        $codes = $this->get_codes();
        $scales = $this->get_scales();

        $attempt = $this->attempt;

        if (! is_null($group))
        {
            $users = $group->get_users(true, true);

            if (count($users) == 0)
            {
                $users = array('0');
            }

            $user_condition = new InCondition(LaikaAttempt :: PROPERTY_USER_ID, $users, LaikaAttempt :: get_table_name());
        }

        if ($attempt != self :: RENDER_ATTEMPT_ALL)
        {
            $attempts = $ldm->retrieve_statistical_attempts($users, $attempt);

            $attempt_ids = array();

            while ($attempt = $attempts->next_result())
            {
                $attempt_ids[] = $attempt->get_id();
            }

            $attempt_condition = new InCondition(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $attempt_ids);
        }

        foreach ($codes as $key => $code)
        {
            $data[$key] = array('Name' => $code);
        }

        foreach ($scales as $scale)
        {
            foreach ($codes as $key => $code)
            {
                $conditions = array();
                $conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_SCALE_ID, $scale->get_id());
                $conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_PERCENTILE_CODE, $code);
                $conditions[] = new InEqualityCondition(LaikaAttempt :: PROPERTY_DATE, InEqualityCondition :: GREATER_THAN_OR_EQUAL, $this->get_start_date(), LaikaAttempt :: get_table_name());
                $conditions[] = new InEqualityCondition(LaikaAttempt :: PROPERTY_DATE, InEqualityCondition :: LESS_THAN_OR_EQUAL, $this->get_end_date(), LaikaAttempt :: get_table_name());

                if ($attempt != self :: RENDER_ATTEMPT_ALL)
                {
                    $conditions[] = $attempt_condition;
                }

                // Don't limit the groups when none were set
                if (! is_null($group))
                {
                    $conditions[] = $user_condition;
                }

                $condition = new AndCondition($conditions);

                $count = $ldm->count_laika_table_calculated_results($condition);
                $data[$key][$scale->get_id()] = $count;
            }
        }

        return $data;
    }
}
?>