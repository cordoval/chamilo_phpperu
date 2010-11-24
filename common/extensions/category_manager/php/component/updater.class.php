<?php
namespace common\extensions\category_manager;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\EqualityCondition;
/**
 * $Id: updater.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.category_manager.component
 */
require_once dirname(__FILE__) . '/../category_manager_component.class.php';
require_once dirname(__FILE__) . '/../platform_category.class.php';
require_once dirname(__FILE__) . '/../category_form.class.php';

class CategoryManagerUpdaterComponent extends CategoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_id = Request :: get(CategoryManager :: PARAM_CATEGORY_ID);
        $user = $this->get_user();

        $categories = $this->retrieve_categories(new EqualityCondition(PlatformCategory :: PROPERTY_ID, $category_id));
        $category = $categories->next_result();

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('category_manager_updater');
        $trail->add(new Breadcrumb($this->get_url(array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('CategoryManagerBrowserComponent')));
        $this->set_parameter(CategoryManager :: PARAM_CATEGORY_ID, Request :: get(CategoryManager :: PARAM_CATEGORY_ID));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CategoryManagerUpdaterComponent')));

        $form = new CategoryForm(CategoryForm :: TYPE_EDIT, $this->get_url(array(CategoryManager :: PARAM_CATEGORY_ID => $category->get_id())), $category, $user, $this);

        if ($form->validate())
        {
            $success = $form->update_category();

            $this->redirect(Translation :: get($success ? 'CategoryUpdated' : 'CategoryNotUpdated'), ($success ? false : true), array(CategoryManager :: PARAM_ACTION => CategoryManager :: ACTION_BROWSE_CATEGORIES, CategoryManager :: PARAM_CATEGORY_ID => $category->get_parent()));
        }
        else
        {
            $this->display_header($this->get_breadcrumb_trail());
            $form->display();
            $this->display_footer();
        }
    }
}
?>