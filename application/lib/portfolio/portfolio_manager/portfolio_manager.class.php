<?php
/**
 * $Id: portfolio_manager.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_data_manager.class.php';
require_once dirname(__FILE__) . '/../portfolio_publication.class.php';
require_once dirname(__FILE__) . '/../portfolio_information.class.php';

/**
 * A portfolio manager
 * @author Sven Vanpoucke
 */
class PortfolioManager extends WebApplication
{

    const APPLICATION_NAME = 'portfolio';
    
    const PARAM_PORTFOLIO_PUBLICATION = 'portfolio_publication';
    const PARAM_PORTFOLIO_ITEM = 'portfolio_item';
    const PARAM_PORTFOLIO_OWNER_ID = 'poid';
    const PARAM_PARENT = 'parent';
    const PARAM_PARENT_PORTFOLIO = 'parent_portfolio';

    const PROPERTY_PID = 'pid';
    const PROPERTY_CID = 'cid';
    
    const ACTION_DELETE_PORTFOLIO_PUBLICATION = 'delete_portfolio_publication';
    const ACTION_DELETE_PORTFOLIO_ITEM = 'delete_portfolio_item';
    const ACTION_CREATE_PORTFOLIO_PUBLICATION = 'create_portfolio_publication';
    const ACTION_CREATE_PORTFOLIO_ITEM = 'create_portfolio_item';
    const ACTION_VIEW_PORTFOLIO = 'view_portfolio';
    const ACTION_BROWSE = 'browse';
    const ACTION_SET_PORTFOLIO_DEFAULTS = 'set_defaults';
    const ACTION_SHOW_PORTFOLIO_RIGHTS_OVERVIEW = "rights_overview";


    const PARAM_PUBLISH_SELECTED = 'repoviewer_selected';
    const ACTION_PUBLISHER = 'publisher';

    const SYSTEM_SETTINGS_INFO_FILE_LOCATION = 'application/lib/portfolio/rights/';
    const SYSTEM_SETTINGS_INFO_FILE_NAME = 'system_settings.html';

    /**
     * Constructor
     * @param User $user The current user
     */
    function PortfolioManager($user = null)
    {
        parent :: __construct($user);
    }

    /**
     * Run this portfolio manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_VIEW_PORTFOLIO :
                $component = $this->create_component('Viewer');
                break;
            case self :: ACTION_DELETE_PORTFOLIO_PUBLICATION :
                $component = $this->create_component('PortfolioPublicationDeleter');
                break;
            case self :: ACTION_DELETE_PORTFOLIO_ITEM :
                $component = $this->create_component('PortfolioItemDeleter');
                break;
            case self :: ACTION_CREATE_PORTFOLIO_PUBLICATION :
                $component = $this->create_component('PortfolioPublicationCreator');
                break;
            case self :: ACTION_CREATE_PORTFOLIO_ITEM :
                $component = $this->create_component('PortfolioItemCreator');
                break;
            case self :: ACTION_BROWSE :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_SET_PORTFOLIO_DEFAULTS :
                $component = $this->create_component('AdminDefaultSettingsCreator');
                break;
            case self :: ACTION_SHOW_PORTFOLIO_RIGHTS_OVERVIEW :
                $component = $this->create_component('RightsOverview');
                break;
            default :
                if (PlatformSetting :: get('first_page', 'portfolio') == 0)
                {
                    $this->set_action(self :: ACTION_BROWSE);
                    $component = $this->create_component('Browser');
                }
                else
                {
                    $this->set_action(self :: ACTION_VIEW_PORTFOLIO);
                    $component = $this->create_component('Viewer');
                    $_GET[self::PARAM_PORTFOLIO_OWNER_ID] = $this->get_user_id();
                }
        
        }
        $component->run();
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

   /**
     * Gets the available links to display in the platform admin
     * @retun array of links and actions
     */
    public function get_application_platform_admin_links()
    {
        $links = array();
        $links[] = new DynamicAction(Translation :: get('SetPortfolioDefaults'), Translation :: get('SetPortfolioDefaultsDescription'), Theme :: get_image_path() . 'browse_list.png', $this->get_link(array(self :: PARAM_ACTION => self :: ACTION_SET_PORTFOLIO_DEFAULTS)));
        
        $info = parent :: get_application_platform_admin_links();
        $info['links'] = $links;
        return $info;
    }


    

    function count_portfolio_publications($condition)
    {
        return PortfolioDataManager :: get_instance()->count_portfolio_publications($condition);
    }

    static function retrieve_portfolio_publications($condition = null, $offset = null, $count = null, $order_property = null)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publications($condition, $offset, $count, $order_property);
    }

    static function retrieve_portfolio_publication($id)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($id);
    }
    static function retrieve_portfolio_item($id)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $complex_object = $rdm->retrieve_complex_content_object_item($id);
        if($complex_object)
        {
            $portfolio_item = $rdm->retrieve_content_object($complex_object->get_ref());
            if ($portfolio_item->get_type() == PortfolioItem :: get_type_name())
            {
                     $content = $rdm->retrieve_content_object($portfolio_item->get_reference());
            }
        }
        if(!isset($content))
        {
            $content = false;
        }
        return $content;
    }

    static function retrieve_portfolio_publication_publisher($pid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_publisher($pid);
    }
    static function retrieve_portfolio_publication_owner($pid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_owner($pid);
    }

    static function retrieve_portfolio_item_publisher($cid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_item_publisher($cid);
    }
    static function retrieve_portfolio_item_owner($cid)
    {
        return PortfolioDataManager :: get_instance()->retrieve_portfolio_item_owner($cid);
    }


    function get_create_portfolio_publication_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PORTFOLIO_PUBLICATION));
    }

    function get_delete_portfolio_publication_url($portfolio_publication)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PORTFOLIO_PUBLICATION, self :: PARAM_PORTFOLIO_PUBLICATION => $portfolio_publication));
    }

    function get_create_portfolio_item_url($parent_id, $pid)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PORTFOLIO_ITEM, self :: PARAM_PARENT => $parent_id, self :: PARAM_PARENT_PORTFOLIO => $pid));
    }

    function get_delete_portfolio_item_url($portfolio_item_cid)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PORTFOLIO_ITEM, self :: PARAM_PORTFOLIO_ITEM => $portfolio_item_cid));
    }

    function get_view_portfolio_url($user)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PORTFOLIO, self :: PARAM_PORTFOLIO_OWNER_ID => $user));
    }

    function get_browse_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE));
    }

    function content_object_is_published($object_id)
    {
        return PortfolioDataManager :: get_instance()->content_object_is_published($object_id);
    }

    function any_content_object_is_published($object_ids)
    {
        return PortfolioDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return PortfolioDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    function get_content_object_publication_attribute($publication_id)
    {
        return PortfolioDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

	function count_publication_attributes($user = null, $object_id = null, $condition = null)
    {
        return PortfolioDataManager :: get_instance()->count_publication_attributes($user, $object_id, $condition);
    }

    function delete_content_object_publications($object_id)
    {
        return PortfolioDataManager :: get_instance()->delete_content_object_publications($object_id);
    }
    
	function delete_content_object_publication($publication_id)
    {
    	 return PortfolioDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    function update_content_object_publication_id($publication_attr)
    {
        return PortfolioDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(Portfolio :: get_type_name());
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(__CLASS__);
            return $locations;
        }
        
        return array();
    }

    /**
     * this method is used to publish a portfolio item with the wizard from the repository
     * @param <type> $content_object
     * @param <type> $location
     * @param <type> $owner_id
     * @return <type>
     */
    function publish_content_object($content_object, $location, $owner_id = null)
    {
        $success = true;
        $publication = new PortfolioPublication();
        $publication->set_content_object($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());

        if($owner_id == null)
        {
            $owner_id = Session :: get_user_id();
        }

        $publication->set_owner($owner_id);
        $publication->set_published(time());
        $success &= $publication->create();
//        $pub_location = $publication->get_location();
//        if($pub_location)
//        {
//            $parent_location_id = $pub_location->get_id();
//
//            $children_set = PortfolioManager::get_portfolio_children($publication->get_content_object(), false, false);
//            if($children_set != false)
//            {
//                $pdm = PortfolioDataManager::get_instance();
//                $success &= $pdm->create_locations_for_children($children_set, $parent_location_id, $owner_id);
//            }
//        }
//        else
//        {
//            $success &= false;
//        }

        $success &= self::update_portfolio_info($publication->get_id(), PortfolioRights::TYPE_PORTFOLIO_FOLDER, PortfolioInformation::ACTION_PORTFOLIO_ADDED, $owner_id);

        if($success)
        {
            return Translation :: get('PublicationCreated');
        }
        else
        {
            return Translation :: get('ProblemWithPublicationCreation');
        }
    }


    /**
     *
     * @param <integer> $content_object_id
     * @param <bool> $pid: is the content object id a portfolio publication id
     * @param <bool> $cid: is the content object id  a complex content object item id
     * @return <result-set>  returen the set of children if there are any, false if there aren't any
     */
    static function get_portfolio_children($content_object_id, $pid = true, $cid = false)
    {

        if($pid)
        {
            $object_id = self::get_co_id_from_portfolio_publication_wrapper($content_object_id);
        }
        else if($cid)
        {
            $object_id = self::get_co_id_from_complex_wrapper($content_object_id);
        }
        else
        {
            $object_id = $content_object_id;
        }

        if($object_id)
        {
            $pdm = PortfolioDataManager::get_instance();
            $children_set = $pdm->retrieve_portfolio_children($object_id);
            return $children_set;
        }
        else
        {
            return false;
        }
  
    }




     


   

    /**
     * get the portfolio content object a portfolio publication holds a reference to
     * @param <integer> $pid id of the portfolio publication
     * @param <object> $portfolio_publication : the actual object
     * @return <integer> the id of the portfolio content object
     */
    static function get_co_id_from_portfolio_publication_wrapper($pid, $portfolio_publication = null)
    {
        $pdm = PortfolioDataManager::get_instance();
        if($portfolio_publication == null)
        {
            $portfolio_publication = $pdm->retrieve_portfolio_publication($pid);
        }

        return $portfolio_publication->get_content_object();
    }


    /**
     *
     * @param <item> $complex_item_object Id
     * @param <object> $complex_item_object: the actual wrapper object
     * @return <type> content object id
     */
     static function get_co_id_from_complex_wrapper($cid, $complex_item_object = null)
    {
        $rdm = RepositoryDataManager::get_instance();
        if($complex_item_object == null)
        {
            $rdm = RepositoryDataManager::get_instance();
            $complex_item_object = $rdm->retrieve_complex_content_object_item($cid);
        }

        $portfolio_item = $rdm->retrieve_content_object($complex_item_object->get_ref());

        if($portfolio_item)
        {
            $type = $portfolio_item->get_type();
            if($type == PortfolioItem::get_type_name())
            {
                $content_object_id = $portfolio_item->get_reference();
            }
            else if($type == Portfolio::get_type_name())
                {
                 $content_object_id = $portfolio_item->get_id();
            }
            else
               {
                $content_object_id = false;
            }
            
        }
        else
        {
            $content_object_id = false;
        }
        return $content_object_id;
    }

     /**
     *gets the portfolio-info-object of the portfolio's owner (wich contains update-information etc.)
     * @return portfolioInfo object
     */
    static function get_portfolio_info($user_id)
    {
        $dm = PortfolioDataManager :: get_instance();
        $info = $dm->retrieve_portfolio_information_by_user($user_id);
        if(!$info)
        {
            $info = new PortfolioInformation();
            $info->set_last_updated_date(time());
            $info->set_user_id($user_id);
            $info->set_last_updated_item_id('0');
            $info->set_last_updated_item_type(PortfolioRights::TYPE_PORTFOLIO_FOLDER);
            $info->set_last_action(PortfolioInformation::ACTION_FIRST_PORTFOLIO_CREATED);
            $dm->create_portfolio_information($info);
        }
        return $info;
    }


    static function update_portfolio_info($content_object_id, $type, $action, $user_id)
    {
        $success = true;
        $info = self::get_portfolio_info($user_id);
       
        $info->set_last_updated_date(time());
        $info->set_last_updated_item_id($content_object_id);
        $info->set_last_updated_item_type($type);
        $info->set_last_action($action);
        $success &= $info->update();
        

        return $success;
    }

     
    /**
     *create locations for the sub-items of a published portfolio
     * @param <reulst_set> $children_set: set of children of this portfolio
     * @param <integer> $parent_location_id: location id of the parent portfolio
     * @param <integer> $owner: owner of the portfolio_tree
     * @return <bool> $success
     */
    static function create_locations_for_children($children_set, $parent_location_id, $owner)
    {
        $success = true;
        while($child = $children_set->next_result())
        {
            $object_id = $child->get_id();
            $grand_children = self::get_portfolio_children($object_id, false, true);
            $child_location= PortfolioRights::create_location_in_portfolio_tree(PortfolioRights::TYPE_PORTFOLIO_ITEM, PortfolioRights::TYPE_PORTFOLIO_ITEM, $object_id, $parent_location_id, $owner, true, false, true);
            if($child_location && $grand-children)
            {
                $success &= self::create_locations_for_children($grand_children, $child_location->get_id(), $owner);
            }
            if($child_location == false)
            {
                $success = false;
            }
        }
        return $success;
    }


    function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_publication_ids = $_POST(ContentObjectTable::DEFAULT_NAME, ObjectTable::CHECKBOX_NAME_SUFFIX);

            if(!is_array($selected_publication_ids))
            {
                $selected_publication_ids = array($selected_publication_ids);
            }

            switch ($_POST['action'])
            {
                case self::PARAM_PUBLISH_SELECTED:
                $this->set_action(self::ACTION_PUBLISHER);
                Request::set_get(RepoViewer::PARAM_ID, $selected_publication_ids);
                break;
            }
        }
    }


    static function get_portfolio_system_settings_page()
    {
        //TODO: support for multiple languates
        return  Configuration::get_instance()->get_parameter('general', 'root_web').self::SYSTEM_SETTINGS_INFO_FILE_LOCATION.Translation::get_instance()->get_language().'_'.self::SYSTEM_SETTINGS_INFO_FILE_NAME;

    }

     static function set_portfolio_system_settings_page($settings_text, $language)
    {
        
       $settings_file = @fopen(self::SYSTEM_SETTINGS_INFO_FILE_LOCATION.$language.'_'.self::SYSTEM_SETTINGS_INFO_FILE_NAME, "w");
       fwrite($settings_file, $settings_text);
       fclose($settings_file);


    }

    static function create_portfolio_system_settings_page($view, $edit, $view_feedback, $give_feedback)
    {
        $system_languages_list = AdminDataManager::get_instance()->retrieve_languages();

        while($language = $system_languages_list->next_result())
        {
            if($view==1)
            {
                $view="OnlyCertainUsersAndGroups";
            }
            if($edit==1)
            {
                $edit="OnlyCertainUsersAndGroups";
            }
            if($view_feedback==1)
            {
                $view_feedback="OnlyCertainUsersAndGroups";
            }
            if($give_feedback==1)
            {
                $give_feedback="OnlyCertainUsersAndGroups";
            }
            $html_header = array();
            $html_header[]= '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
            $html_header[]= '<html>';
            $html_header[]= '<head>';
            $html_header[]= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            $html_header[]= '</head>';
            $html_header[]= '<body>';
            $html_header[]= '<ul>';


            $html_footer = array();
            $html_footer[] = '</ul>';
            $html_footer[] = '</body>';
            $html_footer[] = '</html>';

            $trans = Translation::get_instance();
            $trans->set_language($language->get_folder());

            $html = array();
            $html[] = '<div id= "portfolioSystemSettings">';
            $html[] = "<H1>";
            $html[] = $trans->get('SystemsSettingsOverview');
            $html[] = "</H1>";

            
            $html[] = "<li>";
            $html[] = $trans->get(view). " = ". $trans->get($view);
            $html[] = "</li>";
            $html[] = "<li>";
            $html[] = $trans->get(edit). " = ". $trans->get($edit);
            $html[] = "</li>";
            $html[] = "<li>";
            $html[] = $trans->get(viewFeedback). " = ". $trans->get($view_feedback);
            $html[] = "</li>";
            $html[] = "<li>";
            $html[] = $trans->get(giveFeedback). " = ". $trans->get($give_feedback);
            $html[] = "</li>";
            $html[] = "</div>";

            self::set_portfolio_system_settings_page(implode("\n", $html_header).implode("\n", $html).implode("\n", $html_footer), $language->get_folder());
        }
        

    }

    static function display_system_settings_link()
    {
        //show settings
        $html[] = '<div align="right">';
        $html[] ='<a class="help" target="about:blank" href="';
        $html[] = PortfolioManager::get_portfolio_system_settings_page();
        $html[] ='" title="';
        $html[] = Translation :: get('SeeSystemSettings');
        $html[] ='   ">';
        $html[] = '<img HEIGHT = 15 WIDTH = 15 src="'.Theme ::get_image_path('portfolio'). 'help.png"  class="labeled">';
        $html[] ='<span>';
        $html[] =    Translation :: get('SeeSystemSettings');
        $html[] =    '</span>';
        $html[] ='</a>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

      static function display_all_portfolio_settings_link()
    {
        //show settings for all user's portfolio
        $link = Configuration::get_instance()->get_parameter('general', 'root_web') . 'run.php?' . Application :: PARAM_APPLICATION . '=' . PortfolioManager::APPLICATION_NAME. '&' . Application::PARAM_ACTION. '=' . self::ACTION_SHOW_PORTFOLIO_RIGHTS_OVERVIEW;

        $html[] = '<div align="right">';
        $html[] ='<a class="help" target="about:blank" href="';
        $html[] = $link;

        $html[] ='" title="';
        $html[] = Translation :: get('SeeAllPortfolioPermissions');
        $html[] ='   ">';
        $html[] = '<img HEIGHT = 15 WIDTH = 15 src="'.Theme ::get_image_path('portfolio'). 'help.png"  class="labeled">';
        $html[] ='<span>';
        $html[] =    Translation :: get('SeeAllPortfolioPermissions');
        $html[] =    '</span>';
        $html[] ='</a>';
        $html[] = '</div>';

        return implode("\n", $html);
    }




   
}
?>