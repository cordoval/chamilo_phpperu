<?php
namespace application\cda;

use common\libraries\AndCondition;
use common\libraries\WebApplication;
use common\libraries\Display;
use common\libraries\ActionBarRenderer;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\ToolbarItem;
use common\libraries\ConditionProperty;
use common\libraries\AdministrationComponent;
use common\libraries\Application;
use common\libraries\Utilities;

/**
 * @package application.cda.cda.component
 */

require_once WebApplication :: get_application_class_lib_path('cda') . 'cda_manager/component/language_pack_browser/language_pack_browser_table.class.php';
require_once WebApplication :: get_application_class_lib_path('cda') . 'forms/language_pack_browser_filter_form.class.php';

/**
 * cda component which allows the user to browse his language_packs
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerAdminLanguagePacksBrowserComponent extends CdaManager implements AdministrationComponent
{
    private $actionbar;
    private $form;

    function run()
    {
        $this->actionbar = $this->get_action_bar();

        $can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');
        $can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');
        $can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');

        if (! $can_edit && ! $can_delete && ! $can_add)
        {
            Display :: not_allowed();
        }

        $this->display_header();

        echo $this->actionbar->as_html();
        echo $this->get_table();

        $this->display_footer();
    }

    function get_table()
    {
        $this->form = new LanguagePackBrowserFilterForm($this, $this->get_url());
        $table = new LanguagePackBrowserTable($this, array(
                Application :: PARAM_APPLICATION => 'cda',
                Application :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS), $this->get_condition());

        $html[] = $this->form->display();
        $html[] = $table->as_html();
        return implode("\n", $html);
    }

    function get_condition()
    {
        $form = $this->form;

        $condition = $form->get_filter_conditions();
        if ($condition)
            $conditions[] = $condition;

        $properties[] = new ConditionProperty(LanguagePack :: PROPERTY_NAME);
        $ab_condition = $this->actionbar->get_conditions($properties);
        if ($ab_condition)
            $conditions[] = $ab_condition;

        if (count($conditions) > 0)
            return new AndCondition($conditions);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_LANGUAGE_PACKS, 'manager');
        if ($can_add)
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_language_pack_url()));
        }

        $action_bar->set_search_url($this->get_admin_browse_language_packs_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll', null, Utilities :: COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_browser.png', $this->get_admin_browse_language_packs_url()));

        return $action_bar;
    }

    function get_cda_language()
    {
        return null;
    }

    function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('cda_admin_language_packs_browser');
    }
}
?>