<?php
namespace application\phrases;

use common\libraries\DelegateComponent;
use common\libraries\Request;
use common\extensions\rights_editor_manager\RightsEditorManager;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerRightsEditorComponent extends PhrasesManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category = Request :: get(PhrasesManager :: PARAM_CATEGORY);
        $publications = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);

        if ($publications && ! is_array($publications))
        {
            $publications = array($publications);
        }

        $locations = array();

        foreach ($publications as $publication)
        {
            if ($this->get_user()->is_platform_admin() || $publication->get_publisher() == $this->get_user_id())
            {
                $locations[] = PhrasesRights :: get_location_by_identifier_from_phrasess_subtree($publication, PhrasesRights :: TYPE_PUBLICATION);
            }
        }

        if (count($locations) == 0)
        {
            if ($this->get_user()->is_platform_admin())
            {
                if ($category == 0)
                {
                    $locations[] = PhrasesRights :: get_phrasess_subtree_root();
                }
                else
                {
                    $locations[] = PhrasesRights :: get_location_by_identifier_from_phrasess_subtree($category, PhrasesRights :: TYPE_CATEGORY);
                }
            }
        }

        $manager = new RightsEditorManager($this, $locations);
        $manager->exclude_users(array($this->get_user_id()));
        $manager->run();
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_rights_editor');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION, self :: PARAM_CATEGORY);
    }

    function get_available_rights()
    {
        $publications = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);
        if (count($publications) > 0)
        {
            return PhrasesRights :: get_available_rights_for_publications();
        }

        return PhrasesRights :: get_available_rights_for_categories();
    }

}
?>