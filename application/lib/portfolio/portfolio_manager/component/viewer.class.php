<?php
/**
 * $Id: viewer.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */

require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../portfolio_manager_component.class.php';
require_once dirname(__FILE__) . '/../../portfolio_menu.class.php';
require_once dirname(__FILE__) . '/../../forms/portfolio_publication_form.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerViewerComponent extends PortfolioManagerComponent
{
    private $action_bar;
    private $selected_object;
    private $publication;
    private $portfolio_item;
    private $cid;
    private $pid;

    const PROPERTY_PID = 'pid';
    const PROPERTY_CID = 'cid';
    const PROPERTY_ROOT = 'root';

    const ACTION_VIEW = 'view';
    const ACTION_FEEDBACK = 'feedback';
    const ACTION_EDIT = 'edit';
    const ACTION_PERMISSIONS = 'properties';


    function run()
    {
        $publisher_user_id = Request :: get('user_id');
        $pid = Request :: get(self::PROPERTY_PID);
        $this->pid = $pid;
        $cid = Request :: get(self::PROPERTY_CID);
        $this->cid = $cid;
        
        $rdm = RepositoryDataManager :: get_instance();

        $possible_types = array();
        if($cid)
        {
            $portfolio_identifier = $cid;
            $possible_types[] = portfolioRights::TYPE_PORTFOLIO_ITEM;
            $possible_types[] = portfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
            
        }
        else if($pid)
        {
            $portfolio_identifier = $pid;
            $possible_types[]= portfolioRights::TYPE_PORTFOLIO_FOLDER;
            
        }
        else
        {
            $portfolio_identifier = self::PROPERTY_ROOT;
        }
        //$rights = array();
        $current_user_id = $this->get_user_id();

        //TODO:find a performant way to cache these rights instead of doing the check over and over again
        if($portfolio_identifier != self::PROPERTY_ROOT)
        {
            $rights = portfolioRights::get_rights($current_user_id, $portfolio_identifier, $possible_types);
        }
        $viewing_right = $rights[portfolioRights::VIEW_RIGHT];
        $editing_right = $rights[portfolioRights::EDIT_RIGHT];
        $feedback_viewing_right = $rights[portfolioRights::VIEW_FEEDBACK_RIGHT];
        $feedback_giving_right = $rights[portfolioRights::GIVE_FEEDBACK_RIGHT];
        $permission_setting_right = $rights[portfolioRights::SET_PERMISSIONS_RIGHT];
        $actions = array();

        if($portfolio_identifier == self::PROPERTY_ROOT)
        {
            //root can be seen by everybody
             $viewing_right = true;
        }
        if($viewing_right)
        {
             $actions[] = self::ACTION_VIEW;
            if($editing_right)
            {
              $actions[] = self::ACTION_EDIT;
            }
            if($feedback_viewing_right)
            {
              $actions[] = self::ACTION_FEEDBACK;
            }
            if($feedback_giving_right)
            {
              //how to allow feedback viewing and feedback giving seperately???
            }
            if($permission_setting_right)
            {
              $actions[] = self::ACTION_PERMISSIONS;
            }
            //get the object
            if ($pid && $cid)
            {
                $wrapper = $rdm->retrieve_complex_content_object_item($cid);
                $this->selected_object = $rdm->retrieve_content_object($wrapper->get_ref());
                if ($this->selected_object->get_type() == portfolioRights::TYPE_PORTFOLIO_ITEM)
                {
                    $this->portfolio_item = $this->selected_object;
                    $this->selected_object = $rdm->retrieve_content_object($this->selected_object->get_reference());
                }
            }
            elseif ($pid && ! $cid)
            {
                $publication = PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($pid);
                $this->publication = $publication;
                $this->selected_object = $rdm->retrieve_content_object($publication->get_content_object());
            }
        }
        else
        {
            //no rights so no object should be retrieved
        }
     
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_USER_ID => $publisher_user_id)), Translation :: get('ViewPortfolio')));
             
        if ($publisher_user_id == $this->get_user_id())
        {
            
            $this->action_bar = $this->get_action_bar();
            $html[] = $this->action_bar->as_html();
        }
          
        $html[] = '<div id="action_bar_browser">';
        
        $html[] = '<div style="width: 18%; float: left; overflow: auto;">';
        
        if (PlatformSetting :: get('display_user_picture', 'portfolio'))
        {
            $user = UserDataManager :: get_instance()->retrieve_user($publisher_user_id);
            
            $html[] = '<div style="text-align: center;">';
            $html[] = '<img src="' . $user->get_full_picture_url() . '" />';
            $html[] = '</div><br />';
        }
        $menu = new PortfolioMenu($this->get_user(), 'run.php?go=view_portfolio&application=portfolio&user_id=' . $publisher_user_id . '&pid=%s&cid=%s', $pid, $cid, $publisher_user_id);
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        
        $html[] = '<div style="width: 80%; overflow: auto;">';
        $html[] = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        $current_action = Request :: get('action') ? Request :: get('action') : 'view';        
        foreach ($actions as $action)
        {
            $html[] = '<li><a';
            if ($action == $current_action)
            {
                $html[] = ' class="current"';
            }
            
            $html[] = ' href="' . $this->get_url(array('pid' => $pid, 'cid' => $cid, 'user_id' => $user_id, 'action' => $action)) . '">' . htmlentities(Translation :: get(ucfirst($action) . 'Title'));
            if ($action == 'feedback')
            {
                $html[] = '[' . AdminDataManager :: get_instance()->count_feedback_publications($pid, $cid, PortfolioManager :: APPLICATION_NAME) . ']';
            }
            $html[] = '</a></li>';
        }
        
        $html[] = '</ul><div class="tabbed-pane-content">';
        $html[] = call_user_func(array($this, 'display_' . $current_action . '_page'));
        $html[] = '</div></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_USER_ID => $user_id)), Translation :: get('ViewPortfolio')));
        
        $this->display_header($trail);
        echo implode("\n", $html);
        $this->display_footer();
    }

    function add_actionbar_item($item)
    {
        if($this->action_bar!= null)
        {
            $this->action_bar->add_tool_action($item);
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishNewPortfolio'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_portfolio_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if ($this->selected_object && $this->selected_object->get_type() == Portfolio :: get_type_name())
        {
            if($this->cid)
            {
                $portfolio = $this->cid;
            }
            else
            {
                $portfolio = $this->pid  ;
            }
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddNewItemToPortfolio'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_portfolio_item_url($this->selected_object->get_id(), $portfolio), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if ($this->selected_object)
        {
            if (! $this->cid)
            {
                $url = $this->get_delete_portfolio_publication_url($this->pid);
            }
            else
            {
                $url = $this->get_delete_portfolio_item_url($this->cid);
            }
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        return $action_bar;
    }

    function display_view_page()
    {
        $html = array();
        
        if ($this->selected_object)
        {
            $display = ContentObjectDisplay :: factory($this->selected_object);
            $html[] = $display->get_full_html();
        }
        else
        {
            $html[] = Translation :: get('PortfolioIntroduction');
        }
        
        return implode("\n", $html);
    }

    function display_feedback_page()
    {
        $this->set_parameter('action', Request :: get('action'));
        $this->set_parameter('user_id', Request :: get('user_id'));
        $html = array();
        $fbm = new FeedbackManager($this, PortfolioManager :: APPLICATION_NAME);
        $html[] = $fbm->as_html();
        
        return implode("\n", $html);
    }

    function display_validation_page()
    {
        
        $html = array();
        $fbm = new ValidationManager($this, PortfolioManager :: APPLICATION_NAME);
        $html[] = $fbm->as_html();
        
        return implode("\n", $html);
    }

    function display_edit_page()
    {
        $html = array();
        
        $allow_new_version = ($this->selected_object->get_type() != Portfolio :: get_type_name());
        
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $this->selected_object, 'content_object_form', 'post', $this->get_url(array('user_id' => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'edit')), null, null, $allow_new_version);
        
        if ($form->validate())
        {
            $success = $form->update_content_object();
            
            if ($form->is_version())
            {
                $object = $form->get_content_object();
                if ($this->publication)
                {
                    $this->publication->set_content_object($object->get_latest_version()->get_id());
                    $this->publication->update(false);
                }
                else
                {
                    $this->portfolio_item->set_reference($object->get_latest_version()->get_id());
                    $this->portfolio_item->update();
                }
            }
            
            $this->redirect($success ? Translation :: get('PortfolioUpdated') : Translation :: get('PortfolioNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
        }
        else
        {
            $html[] = $form->display();
        }
        
        return implode("\n", $html);
    }

    function display_properties_page()
    {
        $html = array();
        if ($this->cid)
        {
             if($this->selected_object->get_type() != Portfolio :: get_type_name())
             {
                 $type = portfolioRights::TYPE_PORTFOLIO_ITEM;
             }
             else
             {
                 $type = portfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
             }
            
        }
        else
        {
            $type = portfolioRights::TYPE_PORTFOLIO_FOLDER;
        }
       //TODO CHECK FOR ITEM --> NO NEED TO MAKE PERMISSIONS TAB
       $form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_EDIT, $this->publication, $this->get_url(array('user_id' => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'properties')), $this->get_user(), $type);

        if ($form->validate())
        {
            $success = $form->update_portfolio_publication();
            $this->redirect($success ? Translation :: get('PortfolioPropertiesUpdated') : Translation :: get('PortfolioPropertiesNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_USER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
        }
        else
        {
            $html[] = $form->display();
        }
        return implode("\n", $html);
    }

}
?>