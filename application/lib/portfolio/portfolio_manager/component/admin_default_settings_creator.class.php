<?php
/**
 * $Id: portfolio_publication_creator.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/portfolio_publication_form.class.php';

/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerAdminDefaultSettingsCreatorComponent extends PortfolioManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        Header :: set_section('admin');
		$trail = BreadcrumbTrail::get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
                $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => PortfolioManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Portfolio') ));

                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DefaultSystemSettings')));

		if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
            
            $form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_CREATE_DEFAULTS, null, $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => 0, 'pid' => 0, 'cid' => 0, 'action' => 'properties')), $this->get_user(), null);


            if ($form->validate())
            {
                $success = $form->create_portfolio_default_settings();
                $this->redirect($success ? Translation :: get('PortfolioDefaultSettingsSaved') : Translation :: get('PortfolioDefaultSettingsNotSaved'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_SET_PORTFOLIO_DEFAULTS));
            }
            else
            {
                $html[] = $form->toHtml();
            }
      
           
        
        

         $this->display_header($trail);
         echo implode("\n", $html);
          $this->display_footer();
    }
}
?>