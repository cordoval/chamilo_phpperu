<?php
/**
 * $Id: portfolio_publication_creator.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */
require_once dirname(__FILE__) . '/../portfolio_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/portfolio_introduction_form.class.php';

/**
 * Component to create a new portfolio_publication object
 * @author Sven Vanpoucke
 */
class PortfolioManagerPortfolioIntroductionCreatorComponent extends PortfolioManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_BROWSE)), Translation :: get('BrowsePortfolio')));

        $udm = UserDataManager :: get_instance();
        $user = $udm->retrieve_user($this->get_user_id());
        $trail->add(new Breadcrumb($this->get_url(array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id())), Translation :: get('ViewPortfolio') . ' ' . $user->get_fullname()));

        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateIntroduction')));
        $trail->add_help('portfolio introduction');

        

            $form = new PortfolioIntroductionForm(PortfolioIntroductionForm :: TYPE_EDIT, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $object)), $this->get_user());

            if ($form->validate())
            {
                $success = $form->create_portfolio_introduction();
                $this->redirect($success ? Translation :: get('PortfolioIntroductionCreated') : Translation :: get('PortfolioIntroductionNotCreated'), ! $success, array(PortfolioManager :: PARAM_ACTION => PortfolioManager :: ACTION_VIEW_PORTFOLIO, PortfolioManager :: PARAM_PORTFOLIO_OWNER_ID => $this->get_user_id()));
            }
            else
            {
                $html[] = $form->toHtml();
                

                $this->display_header();
                echo implode("\n", $html);
                $this->display_footer();
            }
        
    }
}
?>