<?php
/**
 * $Id: laika_utilities.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika
 */

require_once dirname(__FILE__) . '/laika_data_manager.class.php';

class LaikaUtilities
{

    function get_laika_results_html($attempt, $url_format = '?application=laika&go=info&scale=%s')
    {
        
        $ldm = LaikaDataManager :: get_instance();
        $html = array();
        
        $html[] = '<div>';
        
        // Table headers
        $html[] = '<div style="margin-bottom: 5px; font-weight: bold;">';
        $html[] = '<div style="float: left; width: 20%;">';
        $html[] = Translation :: get('Cluster');
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 78%;">';
        $html[] = '<div>';
        $html[] = '<div style="float: left; width: 47%; margin-right: 1%;">';
        $html[] = Translation :: get('Scale');
        $html[] = '</div>';
        $html[] = '<div style="float: left; width: 10%;">';
        $html[] = Translation :: get('Code');
        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 40%;">';
        $html[] = Translation :: get('Initiatives');
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        $clusters = $ldm->retrieve_laika_clusters(null, null, null, new ObjectTableOrder(LaikaCluster :: PROPERTY_TITLE));
        while ($cluster = $clusters->next_result())
        {
            $html[] = '<div style="margin-bottom: 5px;">';
            $html[] = '<div style="float: left; width: 20%;">';
            $html[] = $cluster->get_title();
            $html[] = '</div>';
            
            $scale_condition = new EqualityCondition(LaikaScale :: PROPERTY_CLUSTER_ID, $cluster->get_id());
            $scales = $ldm->retrieve_laika_scales($scale_condition, null, null, new ObjectTableOrder(LaikaScale :: PROPERTY_TITLE));
            
            $html[] = '<div style="float: right; width: 78%;">';
            while ($scale = $scales->next_result())
            {
                $html[] = '<div id="scale_' . $scale->get_id() . '">';
                $calculated_result_conditions = array();
                $calculated_result_conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $attempt->get_id());
                $calculated_result_conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_SCALE_ID, $scale->get_id());
                
                $calculated_result_condition = new AndCondition($calculated_result_conditions);
                
                $calculated_result = $ldm->retrieve_laika_calculated_results($calculated_result_condition, null, 1)->next_result();
                
                $percentile_code = $calculated_result->get_percentile_code();
                
                $html[] = '<div style="float: left; width: 47%; margin-right: 1%;">';
                $html[] = $scale->get_title();
                $html[] = '</div>';
                
                $html[] = '<div style="float: left; width: 10%;">';
                $html[] = $percentile_code;
                $html[] = '</div>';
                
                $html[] = '<div style="float: right; width: 40%;">';
                if ($percentile_code < 2)
                {
                    
                    $html[] = '<a class="showInfo" id="scale_info_' . $scale->get_id() . '" href="' . htmlentities(sprintf($url_format, $scale->get_id())) . '">' . Translation :: get('ScaleToTrack') . '</a>';
                }
                $html[] = '</div>';
                
                $html[] = '<div class="clear">&nbsp;</div>';
                $html[] = '</div>';
            }
            
            $html[] = '</div>';
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }
        
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/laika.js');
        
        return implode("\n", $html);
    }

    function get_laika_results_array($attempt, $url_format = '?application=laika&go=info&scale=%s')
    {
        $ldm = LaikaDataManager :: get_instance();
        $results = array();
        
        $clusters = $ldm->retrieve_laika_clusters(null, null, null, array(LaikaCluster :: PROPERTY_TITLE));
        while ($cluster = $clusters->next_result())
        {
            $results[$cluster->get_title()] = array();
            
            $scale_condition = new EqualityCondition(LaikaScale :: PROPERTY_CLUSTER_ID, $cluster->get_id());
            $scales = $ldm->retrieve_laika_scales($scale_condition, null, null, array(LaikaScale :: PROPERTY_TITLE));
            
            while ($scale = $scales->next_result())
            {
                $results[$cluster->get_title()][$scale->get_title()] = array();
                
                $calculated_result_conditions = array();
                $calculated_result_conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_ATTEMPT_ID, $attempt->get_id());
                $calculated_result_conditions[] = new EqualityCondition(LaikaCalculatedResult :: PROPERTY_SCALE_ID, $scale->get_id());
                
                $calculated_result_condition = new AndCondition($calculated_result_conditions);
                
                $calculated_result = $ldm->retrieve_laika_calculated_results($calculated_result_condition, null, 1)->next_result();
                
                $results[$cluster->get_title()][$scale->get_title()] = $calculated_result->get_percentile_code();
                
            //				if ($percentile_code < 2)
            //				{
            //
            //					$html[] = '<a href="' . htmlentities(sprintf($url_format, $scale->get_id())) . '">' . Translation :: get('ScaleToTrack') . '</a>';
            //				}
            }
        }
        
        return $results;
    }

    function get_laika_admin_menu($browser)
    {
        $html = array();
        
        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';
        
        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_right_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_right_show.png" /></a>';
        $html[] = '</div>';
        
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        
        // Result Links
        $html[] = '<li class="tool_list_menu title">' . Translation :: get('LaikaResults') . '</li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_browser.png)"><a href="' . $browser->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_BROWSE_RESULTS)) . '">' . Translation :: get('BrowseResults') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_chart.png)"><a href="' . $browser->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_RENDER_GRAPH)) . '">' . Translation :: get('RenderGraphs') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_users.png)"><a href="' . $browser->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_BROWSE_USERS)) . '">' . Translation :: get('BrowseUsers') . '</a></li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_statistics.png)"><a href="' . $browser->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_VIEW_STATISTICS)) . '">' . Translation :: get('ViewStatistics') . '</a></li>';
        
        $html[] = '<div class="splitter"></div>';
        
        // Test Links
        $html[] = '<li class="tool_list_menu title">' . Translation :: get('LaikaTest') . '</li>';
        $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'action_start.png)"><a href="' . $browser->get_url(array(Application :: PARAM_ACTION => LaikaManager :: ACTION_TAKE_TEST)) . '">' . Translation :: get('TakeLaika') . '</a></li>';
        
        $html[] = '</ul>';
        $html[] = '</div>';
        
        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';
        
        return implode("\n", $html);
    }

    function get_question_answer_text($answer)
    {
        switch ($answer)
        {
            case '1' :
                return Translation :: get('NotTypical');
                break;
            case '2' :
                return Translation :: get('NotVeryTypical');
                break;
            case '3' :
                return Translation :: get('SomewhatTypical');
                break;
            case '4' :
                return Translation :: get('FairlyTypical');
                break;
            case '5' :
                return Translation :: get('VeryTypical');
                break;
            default :
                return '-';
                break;
        }
    }
}
?>