<?php
namespace repository;

use common\libraries;

use common\libraries\AndCondition;

use common\libraries\Request;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\ResourceManager;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\BasicApplication;
use common\libraries\ComplexContentObjectSupport;
use common\libraries\DynamicTabsRenderer;
use common\libraries\DynamicContentTab;

use repository\content_object\learning_path_item\LearningPathItem;
use repository\content_object\portfolio_item\PortfolioItem;
use repository\content_object\scorm_item\ScormItem;

use admin\AdminDataManager;
use admin\Registration;
use admin\PackageInfo;

/**
 * $Id: creator.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which gives the user the possibility to create a
 * new learning object in his repository. When no type is passed to this
 * component, the user will see a dropdown list in which a learning object type
 * can be selected. Afterwards, the form to create the actual learning object
 * will be displayed.
 */
require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';

class RepositoryManagerCreatorComponent extends RepositoryManager
{
    const TAB_MOST_USED = 'most_used';

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $quotamanager = new QuotaManager($this->get_user());
        $user_objects = $quotamanager->get_used_database_space();
        $type_selector = new ContentObjectTypeSelector($this, $this->get_allowed_content_object_types(), array(), $user_objects == 0);

        $type = ($type_selector->get_selection() ? $type_selector->get_selection() : Request :: get(ContentObjectTypeSelector :: PARAM_CONTENT_OBJECT_TYPE));

        if ($type)
        {
            $category = Request :: get(RepositoryManager :: PARAM_CATEGORY_ID);

            $object = ContentObject :: factory($type);
            $object->set_owner_id($this->get_user_id());
            $object->set_parent_id($category);

            $content_object_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)), null);

            if ($content_object_form->validate())
            {
                $object = $content_object_form->create_content_object();

                if (! $object)
                {
                    $this->redirect(Translation :: get('FileCouldNotBeUploaded'), true, array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_CREATE_CONTENT_OBJECTS, 'type' => $type));
                }

                if (! is_array($object) && $object instanceof ComplexContentObjectSupport)
                {
                    $parameters = array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $object->get_id());
                    $filter = array('category');
                    $this->redirect(null, false, $parameters, $filter);
                }
                else
                {
                    if (is_array($object))
                    {
                        $parent = $object[0]->get_parent_id();
                        $type_name = $object[0]->get_type_name();
                    }
                    else
                    {
                        $parent = $object->get_parent_id();
                        $type_name = $object->get_type_name();
                    }

                    $parameters = array();
                    $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                    $parameters[RepositoryManager :: PARAM_CATEGORY_ID] = $parent;

                    $this->redirect(Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('TypeName', null, ContentObject :: get_content_object_type_namespace($type_name))), Utilities :: COMMON_LIBRARIES), false, $parameters);
                }
            }
            else
            {
                if (! Request :: get('publish'))
                {
                    $this->display_header(null, false, true);
                }
                else
                {
                    $this->display_header(null, false, true);
                }

                $content_object_form->display();
                $this->display_footer();
            }
        }
        else
        {

            if (Request :: get('publish'))
            {
                $this->display_header(null, false, true);
            }
            else
            {
                $this->display_header(null, false, true);
            }

            if ($quotamanager->get_available_database_space() <= 0)
            {
                Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfContentObjectsReached')));
            }
            else
            {
                echo $type_selector->as_html();
                //                $html[] = ResourceManager :: get_instance()->get_resource_html(BasicApplication :: get_application_web_resources_javascript_path(RepositoryManager :: APPLICATION_NAME) . 'repository.js');
            //                echo implode("\n", $html);
            }
            $this->display_footer();
        }
    }

    function get_content_object_type_creation_url($type)
    {
        return $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type));
    }

    function is_allowed_to_create($type)
    {
        return true;
    }

    function get_allowed_content_object_types()
    {
        $types = $this->get_content_object_types(true, false);
        foreach ($types as $index => $type)
        {
            $registration = AdminDataManager :: get_registration($type, Registration :: TYPE_CONTENT_OBJECT);
            if (! $registration || ! $registration->is_active())
            {
                unset($types[$index]);
            }
        }

        return $types;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
        $breadcrumbtrail->add_help('repository_creator');
    }
}
?>