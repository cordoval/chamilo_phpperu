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

    function run()
    {
        $user_id = Request :: get('user_id');
        $pid = Request :: get('pid');
        $this->pid = $pid;
        $cid = Request :: get('cid');
        $this->cid = $cid;
        
        $rdm = RepositoryDataManager :: get_instance();
        
        if ($pid && $cid)
        {
            $wrapper = $rdm->retrieve_complex_content_object_item($cid);
            $this->selected_object = $rdm->retrieve_content_object($wrapper->get_ref());
            
            if ($this->selected_object->get_type() == 'portfolio_item')
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
        
        //view tab is ALWAYS shown. feedback and validation are shown when there is an object selected (only not shown when portfolio root is selected), no checks to the rights managment system. TODO: change this
        $actions = array('view');
        if ($this->selected_object)
        {
            $actions[] = 'feedback';
            //validation feature: expectations and functionality have to be cleared out before further development
            //$actions[] = 'validation';
        }
        
        //TODO: this has to be changed as only checks if user is owner instead of using rights system. OK for properties: only to be set by owner but editing can be done bij others too
        if ($user_id == $this->get_user_id())
        {
            $this->action_bar = $this->get_action_bar();
            $html[] = $this->action_bar->as_html();

            if ($pid && ! $cid)
            {
                $actions[] = 'edit';
                $actions[] = 'properties';
            }
               //again: no checks TODO: change this
            if ($cid)
            {
                $actions[] = 'edit';
                $actions[] = 'properties';
            }
            //$actions[] = 'validation';
        }
        
        $html[] = '<div id="action_bar_browser">';
        
        $html[] = '<div style="width: 18%; float: left; overflow: auto;">';
        
        if (PlatformSetting :: get('display_user_picture', 'portfolio'))
        {
            $user = UserDataManager :: get_instance()->retrieve_user($user_id);
            
            $html[] = '<div style="text-align: center;">';
            $html[] = '<img src="' . $user->get_full_picture_url() . '" />';
            $html[] = '</div><br />';
        }
        
        $menu = new PortfolioMenu($this->get_user(), 'run.php?go=view_portfolio&application=portfolio&user_id=' . $user_id . '&pid=%s&cid=%s', $pid, $cid, $user_id);
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
                $html[] = '[' . AdminDataManager :: get_instance()->count_feedback_publications($pid, $cid, 'portfolio') . ']';
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
        //I think there should always be a check *somewhere* to see if there is indeed an action bar before adding an item
        //I put it here because the feedbackmanager tries to put an item on the action bar even when there was no action bar created
        //maybe the check should be put in the feedbackmanager but the properties are private and this would mean creating extra getters
        //for the moment I think the portfolio application is the only one using the feedback manager so I put my check here and added a comment in the feedbackmanager(NathalieB)
        if($this->action_bar!= null)
        {
            $this->action_bar->add_tool_action($item);
        }
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishNewPortfolio'), Theme :: get_common_image_path() . 'action_create.png', $this->get_create_portfolio_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if ($this->selected_object && $this->selected_object->get_type() == 'portfolio')
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
        
        $allow_new_version = ($this->selected_object->get_type() != 'portfolio');
        
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
            $type = PortfolioPublicationForm::TYPE_PORTFOLIO_ITEM;
        }
        else
        {
            $type = PortfolioPublicationForm::TYPE_PORTFOLIO;
        }
        
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