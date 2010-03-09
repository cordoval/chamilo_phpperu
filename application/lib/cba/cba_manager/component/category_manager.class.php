<?php
require_once dirname(__FILE__).'/../../category_manager/cba_category_manager.class.php';
/**
 * @author Nick Van Loocke
 */
class CbaManagerCategoryManagerComponent extends CbaManagerComponent
{
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
        
        $category_manager = new CbaCategoryManager($this, $trail);
        $category_manager->run();
    }
}
?>