<?php
/**
 * $Id: viewer.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */

require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../../portfolio_menu.class.php';
require_once dirname(__FILE__) . '/../../forms/portfolio_publication_form.class.php';
require_once dirname(__FILE__) . '/../../../../common/feedback_manager/component/browser.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerViewerComponent extends PortfolioManager
{
    private $action_bar;
    private $selected_object;
    private $publication;
    private $portfolio_item;
    private $cid;
    private $pid;
    private $owner_user_id ;
    private $viewing_right = true;
    private $feedback_giving_right = true;
    private $feedback_viewing_right = false;
    private $permission_setting_right;

    private $specific_html;
    private $portfolio_identifier;
    private $current_user_id;

    private $actions = array();

    const PROPERTY_PID = 'pid';
    const PROPERTY_CID = 'cid';
    const PROPERTY_ROOT = 'root';

    const ACTION_VIEW = 'view';
    const ACTION_FEEDBACK = 'feedback';
    const ACTION_EDIT = 'edit';
    const ACTION_PERMISSIONS = 'properties';


    function run()
    {
    	$this->owner_user_id = Request :: get(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID);
        $this->pid = Request :: get(self::PROPERTY_PID);
        $this->cid = Request :: get(self::PROPERTY_CID);


        $rdm = RepositoryDataManager :: get_instance();

    	$possible_types = array();
        if($this->cid)
        {
            $this->portfolio_identifier = $this->cid;
            $possible_types[] = PortfolioRights::TYPE_PORTFOLIO_ITEM;
            $possible_types[] = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;

        }
        else if($this->pid)
        {
            $this->portfolio_identifier = $this->pid;
            $possible_types[]= PortfolioRights::TYPE_PORTFOLIO_FOLDER;

        }
        else
        {
            $this->portfolio_identifier = self::PROPERTY_ROOT;
        }

        if(is_a($this->get_user(), User::CLASS_NAME))
        {
            $this->current_user_id = $this->get_user_id();
        }
        else
        {
            //anonymous user
            $this->current_user_id = 1;
        }

        
        if($this->portfolio_identifier != self::PROPERTY_ROOT)
        {
            $rights = PortfolioRights::get_rights($this->current_user_id, $this->portfolio_identifier, $possible_types);
        }
            $this->viewing_right = $rights[PortfolioRights::VIEW_RIGHT];
            $this->editing_right = $rights[PortfolioRights::EDIT_RIGHT];
            $this->feedback_viewing_right = $rights[PortfolioRights::VIEW_FEEDBACK_RIGHT];
            $this->feedback_giving_right = $rights[PortfolioRights::GIVE_FEEDBACK_RIGHT];
            $this->permission_setting_right = $rights[PortfolioRights::SET_PERMISSIONS_RIGHT];


    	
    	if($this->portfolio_identifier == self::PROPERTY_ROOT && $this->current_user_id != 1)
        {
            //root can be seen by every user
            $this->viewing_right = true;
        }

        if($this->viewing_right)
        {
             $this->actions[] = self::ACTION_VIEW;
            if($this->editing_right)
            {
              $this->actions[] = self::ACTION_EDIT;
            }
            if($this->feedback_viewing_right || $this->feedback_giving_right)
            {
              $this->actions[] = self::ACTION_FEEDBACK;
            }
            if($this->permission_setting_right)
            {
              $this->actions[] = self::ACTION_PERMISSIONS;
            }
            //get the object
            if ($this->pid && $this->cid)
            {
                //get complex_content_object
                $wrapper = $rdm->retrieve_complex_content_object_item($this->cid);
                //get portfolio_item
                $this->selected_object = $rdm->retrieve_content_object($wrapper->get_ref());
                if ($this->selected_object->get_type() == PortfolioItem :: get_type_name())
                {
                    //get content object
                    $this->portfolio_item = $this->selected_object;
                    $this->selected_object = $rdm->retrieve_content_object($this->selected_object->get_reference());
                }
            }
            elseif ($this->pid && ! $this->cid)
            {
                $publication = PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($this->pid);
                $this->publication = $publication;
                $this->selected_object = $rdm->retrieve_content_object($publication->get_content_object());
            }
        }
        $current_action = Request :: get('action') ? Request :: get('action') : 'view';
        call_user_func(array($this, 'display_' . $current_action . '_page'));
    }

    function display_header()
    {
        $rdm = RepositoryDataManager::get_instance();

        $trail = BreadcrumbTrail::get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
        $user = UserDataManager :: get_instance()->retrieve_user($this->owner_user_id);
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->current_user_id)), Translation :: get('ViewPortfolio') . ' ' . $user->get_fullname()));


        if($current_action == self::ACTION_VIEW)
        {
            $trail->add_help('portfolio viewer');
        }
        else if($current_action == self::ACTION_FEEDBACK)
        {
            $trail->add_help('portfolio feedback');
        }
        if($current_action == self::ACTION_PERMISSIONS)
        {
            $trail->add_help('portfolio permissions');
        }
         if($current_action == self::ACTION_EDIT)
        {
            $trail->add_help('portfolio edit');
        }
        else
        {
            $trail->add_help('portfolio general');
        }


         if(is_a($this->get_user(), User::CLASS_NAME))
        {
            $this->current_user_id = $this->get_user_id();
        }
        else
        {
            //anonymous user
            $this->current_user_id = 1;
        }



        

        if ($this->owner_user_id == $this->current_user_id || $this->get_user()->is_platform_admin())
        {

            $this->action_bar = $this->get_action_bar();
            $html[] = $this->action_bar->as_html();
        }

        $html[] = '<div id="action_bar_browser">';
        $html[] = '<div style="width: 18%; float: left; overflow: auto;">';

        if (PlatformSetting :: get('display_user_picture', 'portfolio'))
        {
            $user = UserDataManager :: get_instance()->retrieve_user($this->owner_user_id);

            $html[] = '<div style="text-align: center;">';
            $html[] = '<img src="' . $user->get_full_picture_url() . '" />';
            $html[] = '</div><br />';
        }
        $menu = new PortfolioMenu($this->get_user(), 'run.php?go='.self::ACTION_VIEW_PORTFOLIO.'&application=portfolio&'. PortfolioManager::PARAM_PORTFOLIO_OWNER_ID.'=' . $this->owner_user_id . '&pid=%s&cid=%s', $this->pid, $this->cid, $this->owner_user_id);
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="width: 80%; overflow: auto;">';
        $html[] = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        $current_action = Request :: get('action') ? Request :: get('action') : 'view';
        foreach ($this->actions as $action)
        {
            $html[] = '<li><a';
            if ($action == $current_action)
            {
                $html[] = ' class="current"';
            }

            $html[] = ' href="' . $this->get_url(array('pid' => $this->pid, 'cid' => $this->cid, PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $this->owner_user_id, 'action' => $action)) . '">' . htmlentities(Translation :: get(ucfirst($action) . 'Title'));
            if ($action == 'feedback')
            {
                $html[] = '[' . AdminDataManager :: get_instance()->count_feedback_publications($this->pid, $this->cid, PortfolioManager :: APPLICATION_NAME) . ']';
            }
            $html[] = '</a></li>';
        }

        $html[] = '</ul><div class="tabbed-pane-content">';


        parent::display_header();
        echo implode ("\n", $html);
        if($this->specific_html)
        {
            echo $this->specific_html;
        }

    }

    function display_footer()
    {
//        if(is_a($this->get_user(), User::CLASS_NAME))
//        {
//            $this->current_user_id = $this->get_user_id();
//        }
//        else
//        {
//            //anonymous user
//            $this->current_user_id = 1;
//        }

        $current_action = Request :: get('action') ? Request :: get('action') : 'view';

        $html = array();

        $html[]= '<div></div>';
        $html[]= '</div>';
        $html[]= '</div>';
        $html[]= '</div>';
        $html[]= '</div>';

       
            $trail = BreadcrumbTrail::get_instance();
        
        


        $udm = UserDataManager::get_instance();
        $user = $udm->retrieve_user($this->owner_user_id);
        if($current_user_id != 1)
        {
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $current_user_id)), Translation :: get('ViewPortfolio') . ' ' . $user->get_fullname()));
        }

        echo implode("\n", $html);
        parent::display_footer();

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

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishNewPortfolio'), Theme :: get_common_image_path() . 'content_object/portfolio.png', $this->get_create_portfolio_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if($this->portfolio_identifier == self::PROPERTY_ROOT)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ChangeIntroductionText'), Theme :: get_common_image_path() . 'content_object/portfolio.png', $this->get_create_portfolio_introduction_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }


        if ($this->selected_object ) {

            if($this->selected_object->get_type() == Portfolio :: get_type_name()) {
                if($this->cid ) {
                    $portfolio = $this->cid;
                }
                else {
                    $portfolio = $this->pid  ;
                }
                $parent = $this->selected_object->get_id();
            }
            else {
                $portfolio = $this->pid  ;
                $parent = PortfolioManager::get_co_id_from_portfolio_publication_wrapper($portfolio);
            }
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('AddNewItemToPortfolio'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_portfolio_item_url($parent, $portfolio), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if ($this->selected_object) {
            if (! $this->cid) {
                $url = $this->get_delete_portfolio_publication_url($this->pid);
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete_portfolio_publication'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            else {
                $url = $this->get_delete_portfolio_item_url($this->cid);
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete_portfolio_item'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }


        }
        return $action_bar;
    }

    function display_view_page()
    {
        $html = array();

        if ($this->selected_object)
        {
            //display information on the portfolio publication
            $display = ContentObjectDisplay :: factory($this->selected_object);
            $html[] = $display->get_full_html();
        }
        else if ($this->viewing_right == false)
        {
            //display a warning that the user does not have viewing rights on the item
            $html[] = Translation :: get('NoPermissionToViewItem');
        }
        else
        {
            $dm = PortfolioDataManager :: get_instance();
            $info = $dm->retrieve_portfolio_information_by_user($this->owner_user_id);
            if($info)
            {
                $html[] = $info->get_portfolio_info_text();
            }
            else
             {
                $html[] = '<div>';
                $html[] = Translation :: get('PortfolioNotUpdatedYet');
                $html[] = '<br />';
                $html[] = '<br />';
                $html[] = '</div>';
                $html[] = '<div class="portfolio_introduction">';
                $html[]= Translation :: get('PortfolioIntroductionStandardText');
                $html[] = '</div>';
            }

        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }


    //TODO refactor this code to work with the new submanager structure of the feedback manager????
    function display_feedback_page()
    {
    	$this->set_parameter('action', Request :: get('action'));
        $this->set_parameter(self::PROPERTY_CID, $this->cid);
        $this->set_parameter(self::PROPERTY_PID, $this->pid);
        $this->set_parameter(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID, Request :: get(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID));

        $my_right = "nothing";

        if($this->feedback_viewing_right)
        {
            $my_right = "view";
        }
        else
        {
            $html[] = '<br /><div id="no_rights">';
            $html[] = Translation :: get('NoPermissionToViewFeedback');
            $html[] = '</div><br />';
        }
        if(!isset($this->feedback_giving_right) || $this->feedback_giving_right)
        {
            if($my_right == "view")
            {
                $my_right = "view_give";
            }
            else
            {
                $my_right = "give";
            }

        }
        else
        {
            $html[] = '<div id="no_rights">';
            $html[] = Translation :: get('NoPermissionToGiveFeedback');
            $html[] = '</div><br />';
        }

        if($html)
        {
            $this->specific_html = implode("\n", $html);
        }
        
        $feedback_manager;
        switch ($my_right)
        {
           case 'nothing':
                   $feedback_manager = null;
                   break;
           case 'view':
               if(($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_UPDATE_FEEDBACK) || ($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_DELETE_FEEDBACK))
               {
                    $this->set_parameter(FeedbackManager::PARAM_ACTION, FeedbackManager::ACTION_BROWSE_ONLY_FEEDBACK) ;
               }
               $feedback_manager = new FeedbackManager($this, PortfolioManager::APPLICATION_NAME, $this->pid, $this->cid);
               break;
           case 'give':
               if(($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_UPDATE_FEEDBACK )|| ($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_DELETE_FEEDBACK))
               {
                   $this->set_parameter(FeedbackManager::PARAM_ACTION, FeedbackManager::ACTION_CREATE_ONLY_FEEDBACK) ;
               }
               $feedback_manager = new FeedbackManager($this, PortfolioManager::APPLICATION_NAME, $this->pid, $this->cid);
               break;
           case 'view_give':
               if(($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_UPDATE_FEEDBACK) || ($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_DELETE_FEEDBACK))
               {
                   $this->set_parameter(FeedbackManager::PARAM_ACTION, FeedbackManager::ACTION_BROWSE_FEEDBACK) ;
               }
               $feedback_manager = new FeedbackManager($this, PortfolioManager::APPLICATION_NAME, $this->pid, $this->cid);
               break;
           default:
               if(($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_UPDATE_FEEDBACK) || ($this->get_parameter(FeedbackManager::PARAM_ACTION) != FeedbackManager::ACTION_DELETE_FEEDBACK))
               {
                   $this->set_parameter(FeedbackManager::PARAM_ACTION, FeedbackManager::ACTION_BROWSE_FEEDBACK) ;
               }
               $feedback_manager = new FeedbackManager($this, PortfolioManager::APPLICATION_NAME, $this->pid, $this->cid);
                break;
        }
        if($feedback_manager != null)
        {
            $feedback_manager->run();
        }


    }



    function display_edit_page()
    {
        $html = array();
        $success = true;
        $allow_new_version = ($this->selected_object->get_type() != Portfolio :: get_type_name());

        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $this->selected_object, 'content_object_form', 'post', $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $this->owner_user_id, 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'edit')), null, null, $allow_new_version);

        if ($form->validate())
        {
            if ($this->cid)
            {
                 if($this->selected_object->get_type() != Portfolio :: get_type_name())
                 {
                     $type = PortfolioRights::TYPE_PORTFOLIO_ITEM;
                 }
                 else
                 {
                     $type = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
                 }

            }
            else
            {
                $type = PortfolioRights::TYPE_PORTFOLIO_FOLDER;
            }
            $success &= $form->update_content_object();
            $success &=  PortfolioManager::update_portfolio_info($this->selected_object->get_id(), $type, PortfolioInformation::ACTION_EDITED, $this->owner_user_id);


            if ($form->is_version())
            {
                $object = $form->get_content_object();
                if ($this->publication)
                {
                    $this->publication->set_content_object($object->get_latest_version()->get_id());
                    $success &= $this->publication->update(false);
                }
                else
                {
                    $this->portfolio_item->set_reference($object->get_latest_version()->get_id());
                    $success &= $this->portfolio_item->update();
                }
            }

            $this->redirect($success ? Translation :: get('PortfolioUpdated') : Translation :: get('PortfolioNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->owner_user_id, 'pid' => $this->pid, 'cid' => $this->cid));
        }
        else
        {
            $html[] = $form->toHtml();
        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

    function display_properties_page()
    {
        $html = array();
        if ($this->cid)
        {
             if($this->selected_object->get_type() != Portfolio :: get_type_name())
             {
                $type = PortfolioRights::TYPE_PORTFOLIO_ITEM;
            }
             else
             {
                $type = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
            }

        }
        else
        {
            $type = PortfolioRights::TYPE_PORTFOLIO_FOLDER;
        }

        $form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_EDIT, $this->publication, $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'properties')), $this->get_user(), $type);

        if ($form->validate())
        {
            $success = $form->update_portfolio_publication($type);
            $this->redirect($success ? Translation :: get('PortfolioPropertiesUpdated') : Translation :: get('PortfolioPropertiesNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
        }
        else
        {
            $html[] = $form->toHtml();
        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

}
?>