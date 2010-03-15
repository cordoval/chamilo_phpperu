<?php
require_once dirname(__FILE__) . '/../cba_manager.class.php';
require_once dirname(__FILE__) . '/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../category_manager/competency_category_manager.class.php';
/**
 * @author Nick Van Loocke
 */
class CbaManagerCompetencyCategoryManagerComponent extends CbaManagerComponent
{
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('BrowseCompetency')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        $category_manager = new CompetencyCategoryManager($this, $trail);
        $category_manager->run();    
    }
    
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}
?>