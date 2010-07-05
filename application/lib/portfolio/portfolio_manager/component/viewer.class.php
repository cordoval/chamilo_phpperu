<?php
/**
 * $Id: viewer.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */

require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../../portfolio_menu.class.php';
require_once dirname(__FILE__) . '/../../forms/portfolio_publication_form.class.php';
require_once dirname(__FILE__) . '/../../../../common/feedback_manager/component/browser.class.php';
require_once dirname(__FILE__) . '/../../../../../repository/lib/content_object/portfolio/builder/portfolio_builder.class.php';

require_once dirname(__FILE__) . '/../../../../../repository/lib/content_object/portfolio/builder/component/browser.class.php';

/**
 * portfolio component which allows the user to browse his portfolio_publications
 * @author Sven Vanpoucke
 */
class PortfolioManagerViewerComponent extends PortfolioManager {
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
    private $additional_html;

    const PROPERTY_PID = 'pid';
    const PROPERTY_CID = 'cid';
    const PROPERTY_ROOT = 'root';

    const ACTION_VIEW = 'view';
    const ACTION_FEEDBACK = 'feedback';
    const ACTION_EDIT = 'edit';
    const ACTION_PERMISSIONS = 'properties';


    function run() {
    	$owner_user_id = Request :: get(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID);
        $this->owner_user_id = $owner_user_id;
        $pid = Request :: get(self::PROPERTY_PID);
        $this->pid = $pid;
        $cid = Request :: get(self::PROPERTY_CID);
        $this->cid = $cid;

    	$possible_types = array();
        if($cid) {
            $portfolio_identifier = $cid;
            $possible_types[] = PortfolioRights::TYPE_PORTFOLIO_ITEM;
            $possible_types[] = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;

        }
        else if($pid) {
            $portfolio_identifier = $pid;
            $possible_types[]= PortfolioRights::TYPE_PORTFOLIO_FOLDER;

        }
        else {
            $portfolio_identifier = self::PROPERTY_ROOT;
        }

        if(is_a($this->get_user(), User::CLASS_NAME)) {
            $current_user_id = $this->get_user_id();
        }
        else {
            //anonymous user
            $current_user_id = 1;
        }

        
        if($portfolio_identifier != self::PROPERTY_ROOT)
        {
            $rights = PortfolioRights::get_rights($current_user_id, $portfolio_identifier, $possible_types);
        }

        $this->viewing_right = $rights[PortfolioRights::VIEW_RIGHT];
        $this->editing_right = $rights[PortfolioRights::EDIT_RIGHT];
        $this->feedback_viewing_right = $rights[PortfolioRights::VIEW_FEEDBACK_RIGHT];
        $this->feedback_giving_right = $rights[PortfolioRights::GIVE_FEEDBACK_RIGHT];
        $this->permission_setting_right = $rights[PortfolioRights::SET_PERMISSIONS_RIGHT];
    	
    	if($portfolio_identifier == self::PROPERTY_ROOT && $current_user_id != 1)
        {
            //root can be seen by every user
            $this->viewing_right = true;
        }
        
        $rdm = RepositoryDataManager :: get_instance();
        
    	if ($pid && $cid)
        {
            //get complex_content_object
            $wrapper = $rdm->retrieve_complex_content_object_item($cid);
            //get portfolio_item
            $this->selected_object = $rdm->retrieve_content_object($wrapper->get_ref());
            if ($this->selected_object->get_type() == PortfolioItem :: get_type_name())
            {
                //get content opbject
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
        
    	$current_action = Request :: get('action') ? Request :: get('action') : self :: ACTION_VIEW;
    	call_user_func(array($this, 'display_' . $current_action . '_page'));
    }
    
    function display_header()
    {
        $viewing_right = $this->viewing_right;
        $editing_right = $this->editing_right;
        $feedback_viewing_right = $this->feedback_viewing_right;
        $feedback_giving_right = $this->feedback_giving_right;
        $permission_setting_right = $this->permission_setting_right;
        $pid = $this->pid;
        $cid = $this->cid;
        $owner_user_id = $this->owner_user_id;
        
    	if(is_a($this->get_user(), User::CLASS_NAME))
        {
            $current_user_id = $this->get_user_id();
        }
        else
        {
            $current_user_id = 1;
        }
        
        $actions = array();

        if($viewing_right)
        {
             $actions[] = self::ACTION_VIEW;
            if($editing_right)
            {
              $actions[] = self::ACTION_EDIT;
            }
            if($feedback_viewing_right || $feedback_giving_right)
            {
              $actions[] = self::ACTION_FEEDBACK;
            }
            if($permission_setting_right)
            {
              $actions[] = self::ACTION_PERMISSIONS;
            }
            //get the object
        }
           
        if ($owner_user_id == $current_user_id)
        {
            $this->action_bar = $this->get_action_bar();
            $html[] = $this->action_bar->as_html();
        }

        $html[] = '<div id="action_bar_browser">';
        $html[] = '<div style="width: 18%; float: left; overflow: auto;">';
        
        if (PlatformSetting :: get('display_user_picture', 'portfolio'))
        {
            $user = UserDataManager :: get_instance()->retrieve_user($owner_user_id);

            $html[] = '<div style="text-align: center;">';
            $html[] = '<img src="' . $user->get_full_picture_url() . '" />';
            $html[] = '</div><br />';
        }
        $menu = new PortfolioMenu($this->get_user(), 'run.php?go=view_portfolio&application=portfolio&'. PortfolioManager::PARAM_PORTFOLIO_OWNER_ID.'=' . $owner_user_id . '&pid=%s&cid=%s', $pid, $cid, $owner_user_id);
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';

        $html[] = '<div style="width: 80%; overflow: auto;">';
        $html[] = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        $current_action = Request :: get('action') ? Request :: get('action') : self :: ACTION_VIEW;        
        foreach ($actions as $action)
        {
            $html[] = '<li><a';
            if ($action == $current_action)
            {
                $html[] = ' class="current"';
            }
            
            $html[] = ' href="' . $this->get_url(array('pid' => $pid, 'cid' => $cid, PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $owner_user_id, 'action' => $action)) . '">' . htmlentities(Translation :: get(ucfirst($action) . 'Title'));
            if ($action == 'feedback')
            {
                $html[] = '[' . AdminDataManager :: get_instance()->count_feedback_publications($pid, $cid, PortfolioManager :: APPLICATION_NAME) . ']';
            }
            $html[] = '</a></li>';
        }

        $html[] = '</ul><div class="tabbed-pane-content">';

        parent :: display_header();
 		echo implode("\n", $html);
 		
 		if($this->additional_html)
 		{
 			echo $this->additional_html;
 		}
    }
    
    function display_footer()
    {
     	if(is_a($this->get_user(), User::CLASS_NAME))
        {
            $current_user_id = $this->get_user_id();
        }
        else
        {
            $current_user_id = 1;
        }
    	
        $current_action = Request :: get('action') ? Request :: get('action') : self :: ACTION_VIEW;
        $owner_user_id = Request :: get(PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID);
        
    	$html[] = '</div></div>';
        $html[] = '</div>';
        $html[] = '</div>';

        if($current_user_id != 1)
        {
            $trail = BreadcrumbTrail::get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolios')));
        }
        if($current_action == 'view')
        {
            if($current_user_id != 1)
            {
                $trail->add_help('portfolio viewer');
            }
        }
        else if($current_action == 'feedback')
        {
            if($current_user_id != 1)
            {
                $trail->add_help('portfolio feedback');
            }
        }
        if($current_action == properties)
        {
            $trail->add_help('portfolio permissions'); 
        }
         if($current_action == edit)
        {
            $trail->add_help('portfolio edit'); 
        }
        else
        {
            if($current_user_id != 1)
            {
                $trail->add_help('portfolio general');
            }

        }

        $udm = UserDataManager::get_instance();
        $user = $udm->retrieve_user($owner_user_id);
        if($current_user_id != 1)
        {
            $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $current_user_id)), Translation :: get('ViewPortfolio') . ' ' . $user->get_fullname()));
        }
        
        echo implode("\n", $html);
        parent :: display_footer();
    	
    }

    function add_actionbar_item($item) {
        if($this->action_bar!= null) {
            $this->action_bar->add_tool_action($item);
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishNewPortfolio'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_portfolio_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

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
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete_portfolio_publication'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
            else
            {
                $url = $this->get_delete_portfolio_item_url($this->cid);
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete_portfolio_item'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }


        }
        return $action_bar;
    }

    function display_view_page() {
        $html = array();

        if ($this->selected_object) {
            //display information on the portfolio publication
            $display = ContentObjectDisplay :: factory($this->selected_object);
            $html[] = $display->get_full_html();
        }
        else if ($this->viewing_right == false) {
            //display a warning that the user does not have viewing rights on the item
            $html[] = Translation :: get('NoPermissionToViewItem');
        }
        else {
            $dm = PortfolioDataManager :: get_instance();
            $info = $dm->retrieve_portfolio_information_by_user($this->owner_user_id);
            if($info) {
                $html[] = $info->get_portfolio_info_text();
            }
            else {
                $html[] = Translation :: get('PortfolioNotUpdatedYet');
            }

        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

    //TODO: Refactor this code to work with the new submanager structure of the feedback manager
    function display_feedback_page()
    {
    	$this->set_parameter('action', Request :: get('action'));
        $this->set_parameter(self::PROPERTY_CID, $this->cid);
        $this->set_parameter(self::PROPERTY_PID, $this->pid);
        $this->set_parameter(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID, Request :: get(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID));

        if($this->feedback_viewing_right)
        { 
             $feedback_manager_view = new FeedbackManager($this, PortfolioManager :: APPLICATION_NAME, $this->pid, $this->cid);
        }
        else
        {
            $html[] = '<br /><div id="no_rights">';
            $html[] = Translation :: get('NoPermissionToViewFeedback');                
            $html[] = '</div><br />';
        }
        if(!isset($this->feedback_giving_right) || $this->feedback_giving_right)
        {
        	$html[] = '<h3>' . Translation :: get('PublicationGiveFeedback') . '</h3>';
            $feedback_manager_create = new FeedbackManager($this, PortfolioManager :: APPLICATION_NAME, $this->pid, $this->cid);
        }
        else
        {
        	$html[] = '<div id="no_rights">';
            $html[] = Translation :: get('NoPermissionToGiveFeedback');
            $html[] = '</div><br />';
        }
        
        if($html)
        {
        	$this->additional_html = implode("\n", $html);
        }
        
    	if($feedback_manager_view)
        {
        	$feedback_manager_view->run();
        }
    }

    function display_validation_page()
    {
        
        $html = array();
        $fbm = new ValidationManager($this, PortfolioManager :: APPLICATION_NAME);
        $html[] = $fbm->as_html();

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

    function display_edit_page()
    {
        $html = array();
        $success = true;
        $allow_new_version = ($this->selected_object->get_type() != Portfolio :: get_type_name());

        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $this->selected_object, 'content_object_form', 'post', $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'edit')), null, null, $allow_new_version);
        
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


            if ($form->is_version()) {
                $object = $form->get_content_object();
                if ($this->publication) {
                    $this->publication->set_content_object($object->get_latest_version()->get_id());
                    $success &= $this->publication->update(false);
                }
                else {
                    $this->portfolio_item->set_reference($object->get_latest_version()->get_id());
                    $success &= $this->portfolio_item->update();
                }
            }

            $this->redirect($success ? Translation :: get('PortfolioUpdated') : Translation :: get('PortfolioNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
        }
        else {
            $html[] = $form->toHtml();



        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
//        if($this->selected_object->get_type() == Portfolio :: get_type_name()) {
//
//            $html[] =  '</div><div style="width: 80%; float: right;">';
//            $complex_portfolio_builder = new PortfolioBuilderBrowserComponent($this);
//            $coid=PortfolioManager::get_co_id_from_portfolio_publication_wrapper($this->pid);
//            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $coid, ComplexContentObjectItem :: get_table_name());
////            $html[] =  $complex_portfolio_builder->get_complex_content_object_table_html(false, null, new PortfolioBrowserTableCellRenderer($complex_portfolio_builder, $condition));
//            $parameters[ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $coid;
//            $table = new ComplexBrowserTable($complex_portfolio_builder, $parameters,  $condition, false, null, null);
//            $html[] =  $table->as_html();
//
//
//
//            $html[] =  '</div>';
//        }

    }

    function display_properties_page() {
        $html = array();
        if ($this->cid) {
            if($this->selected_object->get_type() != Portfolio :: get_type_name()) {
                $type = PortfolioRights::TYPE_PORTFOLIO_ITEM;
            }
            else {
                $type = PortfolioRights::TYPE_PORTFOLIO_SUB_FOLDER;
            }

        }
        else {
            $type = PortfolioRights::TYPE_PORTFOLIO_FOLDER;
        }

        $form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_EDIT, $this->publication, $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid, 'action' => 'properties')), $this->get_user(), $type);

        if ($form->validate()) {
            $success = $form->update_portfolio_publication($type);
            $this->redirect($success ? Translation :: get('PortfolioPropertiesUpdated') : Translation :: get('PortfolioPropertiesNotUpdated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id(), 'pid' => $this->pid, 'cid' => $this->cid));
        }
        else {
            $html[] = $form->toHtml();
        }
        
        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

}
?>