<?php
/**
 * $Id: block_list.php 227 2009-11-13 14:45:05Z kariboe $
 * @package home.ajax
 */
$this_section = 'home';

require_once dirname(__FILE__) . '/../../../common/global.inc.php';

Utilities :: set_application($this_section);

$user_home_allowed = PlatformSetting :: get('allow_user_home', HomeManager :: APPLICATION_NAME);

// TODO: Add styles to css instead of leaving them hardcoded.

if ($user_home_allowed && Authentication :: is_valid())
{
    $user_id = Session :: get_user_id();

    $blocks = Block :: get_platform_blocks();
    $applications = $blocks['applications'];
    $components = $blocks['components'];

    echo '<div id="addBlock" class="block" style="margin-bottom: 1%; display: none; background-color: #F6F6F6; padding: 15px; -moz-border-radius: 10px;">';
    echo '<div class="title">';
    echo Translation :: get('AddNewBlocks');
    echo '</div>';

    echo '<div style="clear: both;">';

    echo '<div id="applications" style="float: left; margin-right: 30px;">';
    echo '<div id="show_all" style="clear: both; margin-bottom: 5px;"><a href="#">::&nbsp;' . Translation :: get('ShowAll') . '&nbsp;::</a></div>';
    foreach ($applications as $application_key => $application_value)
    {
        echo '<div class="application" id="' . $application_key . '" style="clear: both; margin-bottom: 5px;"><a href="#">' . $application_value . '</a></div>';
    }
    echo '</div>';

    echo '<div id="components">';
    foreach ($applications as $application_key => $application_value)
    {
        echo '<div id="components_' . $application_key . '" style="float: left;">';
        $application_components = array();
        foreach ($components[$application_key] as $component_key => $component_value)
        {
            $component_title = $application_value . ' > ' . $component_value;
            $component_id = $application_key . '.' . $component_key;

            echo '<div class="component" id="' . $component_id . '" style="float: left; background: url(' . Theme :: get_image_path() . 'background_ajax_component.png) #e7e7e7 repeat-x; margin-right: 5px; margin-bottom: 5px; height: 75px; width: 75px; overflow: hidden; text-align: center; font-size: 75%; font-weight: bolder; border: 1px solid white;">';
            echo '<img style="margin: 5px;" src="' . Theme :: get_image_path('admin') . 'place_' . $application_key . '.png" alt="' . $component_title . '" title="' . $component_title . '"/>';
            echo '<br />';
            echo $component_value;
            echo '</div>';

            $application_components[] = Translation :: get($component_value);
        }
        echo '</div>';
    }
    echo '</div>';

    echo '<div class="clear">&nbsp;</div>';
    echo '</div>';

    echo '<div style="position: relative; bottom: -15px; padding: 5px 0px 5px 0px; margin: 0px -15px 0px -15px; text-align: center; background: url(' . Theme :: get_common_image_path() . 'background_ajax_hide.png)#F6F6F6 no-repeat top center;">';
    echo '<a class="closeScreen" href="#"><img src="' . Theme :: get_common_image_path() . 'action_ajax_hide.png" alt="' . Translation :: get('close') . '" title="' . Translation :: get('close') . '" /></a>';
    echo '</div>';
    echo '</div>';
}
?>