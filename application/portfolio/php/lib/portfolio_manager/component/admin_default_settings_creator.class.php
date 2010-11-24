<?php


namespace application\portfolio;
use common\libraries\Redirect;
use common\libraries\Breadcrumb;
use admin\AdminManager;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Display;
use common\libraries\Header;
use common\libraries\BreadcrumbTrail;
use common\libraries\DynamicTabsRenderer;

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
                $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => PortfolioManager:: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Portfolio') ));

                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('DefaultSystemSettings')));
                $trail->add_help('portfolio system defaults');

		if (! $this->get_user()->is_platform_admin())
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed" , null, Utilities::COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }
        
            
            $form = new PortfolioPublicationForm(PortfolioPublicationForm :: TYPE_CREATE_DEFAULTS, null, $this->get_url(array(PortfolioManager::PARAM_PORTFOLIO_OWNER_ID => 0, 'pid' => 0, 'cid' => 0, 'action' => 'properties')), $this->get_user(), null);


            if ($form->validate())
            {
                $success = $form->create_portfolio_default_settings();
                $this->redirect($success ? Translation :: get('ObjectSaved' , array('OBJECT' => Translation::get('DefaultSystemSettings')), Utilities::COMMON_LIBRARIES) : Translation :: get('ObjectNotSaved' , array('OBJECT' => Translation::get('DefaultSystemSettings')), Utilities::COMMON_LIBRARIES), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_SET_PORTFOLIO_DEFAULTS));
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