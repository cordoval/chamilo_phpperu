<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * this component will show an overview of the permissions set on all the published portfolio-items and folders
 * of a specific user. it will not display the chamilo header and footer so it can be displayed in a popup window
 *
 * @author Nathalie Blocr
 */

require_once dirname(__FILE__) . '/../portfolio_manager.class.php';

class PortfolioManagerRightsOverviewComponent extends PortfolioManager
{


    //TODO: LAYOUT!!
    function run()
    {
        $overview = $this->get_publications();



        echo self::to_html($overview);
       
    }

     private function get_publications()
    {
        $overview = array();

        $pdm = PortfolioDataManager :: get_instance();
        $rdm = RepositoryDataManager :: get_instance();

        $condition = new EqualityCondition(PortfolioPublication :: PROPERTY_OWNER_ID, $this->get_user_id());
        $publications = $pdm->retrieve_portfolio_publications($condition);

        while ($publication = $publications->next_result())
        {

                $portfolio = $rdm->retrieve_content_object($publication->get_content_object());

                $pub = array();
                $pub['title'] = $portfolio->get_title();
                $pub['rights'] = $this->get_portfolio_rights($publication);
                $pub['class'] = 'portfolio';
                $pub['sub'] = $this->get_portfolio_items($publication->get_content_object(), $publication->get_id());
                $overview[] = $pub;
        }

        return $overview;
    }

    private function get_portfolio_items($parent, $pub_id)
    {
        $overview = array();
        $rdm = RepositoryDataManager :: get_instance();

        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));

        while ($child = $children->next_result())
        {
            $pi = $rdm->retrieve_content_object($child->get_ref());

            $portfolio_item = array();
            if($pi->get_type() == PortfolioItem::get_type_name())
            {
                $pi = $rdm->retrieve_content_object($pi->get_reference());
            }
            if ($pi->get_type() == Portfolio :: get_type_name())
            {
                $items = $this->get_portfolio_items($pi->get_id(), $pub_id);
                if (count($items) > 0)
                    $portfolio_item['sub'] = $items;
            }

            $portfolio_item['title'] = $pi->get_title();
            $portfolio_item['rights'] = $this->get_portfolio_item_rights($child->get_id());
            $portfolio_item['class'] = $pi->get_type();
            $overview[] = $portfolio_item;
        }

        return $overview;
    }

    function get_portfolio_rights($publication)
    {
        $rights = array();
        $rights = PortfolioRights::get_all_actual_rights($publication->get_location());
        return $rights;
    }

    function get_portfolio_item_rights($id)
    {
        $type = array(PortfolioRights::TYPE_PORTFOLIO_ITEM, PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER);
        $location = PortfolioRights::get_portfolio_location($id, $type, $this->get_user_id());
        $rights = PortfolioRights::get_all_actual_rights($location);
        return $rights;
    }

    static function to_html($overview_array)
    {
        $html = array();

        $html_header = array();
            $html_header[]= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
            $html_header[]= '<html>';

            
            $html_header[]=  '<style type="text/css" media="screen,projection"> /*<![CDATA[*/ @import "'.Theme::get_css_path().'"; /*]]>*/ </style>';

             

            
            $html_header[]= '<head>';
            $html_header[]= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            $html_header[]= '</head>';
            $html_header[]= '<body>';
            $html_header[]= '<div id="PortfolioPermissions">';


            $html_footer = array();

            $html_footer[] = '</div>';
            $html_footer[] = '</body>';
            $html_footer[] = '</html>';

        

        $html[] = self::array_to_info($overview_array);


        return implode("\n", $html_header).implode("\n", $html).implode("\n", $html_footer);
    }

    static function array_to_info($info_array)
    {

        foreach ($info_array as $pub)
        {
            $html[] = '<div class="PermissionsItem">';

            $html[] = self::print_info($pub);
            
            
            if((isset($pub['sub'])) && ($pub['sub'] != ''))
            {
                $html[] = '<div class="subs">';

                $html[] = self::array_to_info($pub['sub']);

                 $html[] = '</div>';
            }
           
            $html[] = '</div>';



            
        }
        return implode("\n", $html);

    }

    static function print_info($pub_array)
    {
        $html = array();

        $html[] = '<div class ="permissionsInfo" >';
        $html[] = '<div class ="titlePermissions" >';
        $html[] = $pub_array['title'];
        $html[] = '  :  ';
        $html[] = '</div>';
        $html[] = self::visualize_rights($pub_array['rights']);
        $html[] = '</div>';
        return implode("\n", $html);
    }

    static function visualize_rights($array)
    {
        $html = array();

        if(isset($array['inherit_set']) && $array['inherit_set']['option'] == true)
        {
            $html[] = 'inherit';
        }
        else
        {
            $html[] = '<div class ="permissions"  >';

            $html[] = '<div class ="permissionsIcon"   >';
            $html[] = '<img HEIGHT = 20 WIDTH = 20 alt ="'.Translation :: get('view');
            $html[] = '" title="'. Translation :: get('view');
            $html[] = '" src="'.Theme ::get_image_path('portfolio'). '/' .'view.png'. '"  class="labeled">';
            $html[] = ':';
            $html[] = self::return_correct_icon($array['view']['option']);
            $html[] = '</div>';
            
            $html[] = '<div class ="permissionsIcon">';
            $html[] = '<img HEIGHT = 20 WIDTH = 20 alt ="'.Translation :: get('edit');
            $html[] = '" title="'.Translation :: get('edit');
            $html[] = '" src="'.Theme ::get_image_path('portfolio'). '/' .'edit.png'. '"  class="labeled">';
            $html[] = ':';
            $html[] = self::return_correct_icon($array['edit']['option']);
            $html[] = '</div>';
            
            $html[] = '<div class ="permissionsIcon" >';
            $html[] = '<img HEIGHT = 20 WIDTH = 20 alt ="'.Translation :: get('viewFeedback');
            $html[] = '" title="'.Translation :: get('viewFeedback');
            $html[] = '" src="'.Theme ::get_image_path('portfolio'). '/' .'view_feedback.png'. '"  class="labeled">';
            $html[] = ':';
            $html[] = self::return_correct_icon($array['viewFeedback']['option']);
            $html[] = '</div>';

            $html[] = '<div class ="permissionsIcon">';
            $html[] = '<img HEIGHT = 20 WIDTH = 20 alt ="'.Translation :: get('giveFeedback');
            $html[] = '" title="'.Translation :: get('giveFeedback');;
            $html[] = '" src="'.Theme ::get_image_path('portfolio'). '/' .'give_feedback.png'. '"  class="labeled">';
            $html[] = ':';
            $html[] = self::return_correct_icon($array['giveFeedback']['option']);
            $html[] = '</div>';

            $html[] = '</div>';

           

        }



        return implode("\n", $html);

    }
    
    static function return_correct_icon($option)
    {

        switch ($option) {
            case PortfolioRights::RADIO_OPTION_ALLUSERS:
                $icon = 'system_users.png';
                $text = Translation :: get(PortfolioRights::RADIO_OPTION_ALLUSERS);
                break;
            case PortfolioRights::RADIO_OPTION_ANONYMOUS:
                $icon = 'All_users.png';
                $text = Translation :: get(PortfolioRights::RADIO_OPTION_ANONYMOUS);
                break;
            case PortfolioRights::RADIO_OPTION_ME:
                $icon = 'only_me.png';
                $text = Translation :: get(PortfolioRights::RADIO_OPTION_ME);
                break;
            case PortfolioRights::RADIO_OPTION_GROUPS_USERS:
                $icon = 'specific_users.png';
                $text = Translation :: get(PortfolioRights::RADIO_OPTION_GROUPS_USERS);
                break;

            default:
                $icon = 'All_users.png';
                $text = Translation :: get(PortfolioRights::RADIO_OPTION_ANONYMOUS);
                break;

            return $icon;
        }


         return '<img HEIGHT = 20 WIDTH = 20 alt ="'. $text .'" title="'. $text .'" src="'.Theme ::get_image_path('portfolio'). '/' .$icon. '"  class="labeled">';
    }


}
?>
