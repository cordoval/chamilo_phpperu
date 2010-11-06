<?php
namespace common\extensions\repo_viewer;

use repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\FormValidator;
use common\libraries\ResourceManager;
use common\libraries\Path;
use common\libraries\Utilities;

use repository\ContentObject;
use repository\RepositoryManager;
use repository\ContentObjectForm;
use repository\RepositoryDataManager;
use repository\ContentObjectTypeSelector;

/**
 * $Id: creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.common.repo_viewer.component
 */
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to create a new learning object before publishing it.
 */
class RepoViewerCreatorComponent extends RepoViewer
{

    /*
	 * Inherited
	 */
    function run($params = array())
    {
        $content_object_id = Request :: get(RepoViewer :: PARAM_EDIT_ID);
        if ($content_object_id)
        {
            $this->get_editing_form($content_object_id);
        }
        else
        {
            $types = $this->get_types();
            $type_selector = new ContentObjectTypeSelector($this, $this->get_types());

            if (count($types) > 1)
            {
                if ($type_selector->get_selection())
                {
                    $this->get_creation_form($type_selector->get_selection());
                }
                else
                {
                    $this->display_header();
                    echo $type_selector->as_html();
                    $this->display_footer();
                }
            }
            else
            {
                $this->get_creation_form($types[0]);
            }
        }
    }

    /**
     * Gets the type of the learning object which will be created.
     */
    function get_type()
    {
        $types = $this->get_types();

        if (count($types) > 1)
        {
            $type = Request :: post(RepoViewer :: PARAM_CONTENT_OBJECT_TYPE);
            if (! $type)
            {
                $type = Request :: get(RepoViewer :: PARAM_CONTENT_OBJECT_TYPE);
            }

            return $type;
        }
        else
        {
            return $types[0];
        }
    }

    /**
     * Gets the form to select a learning object type.
     * @return string A HTML-representation of the form.
     */
    protected function get_type_selector()
    {
        $type_selector = new ContentObjectTypeSelector($this, $this->get_types());

        if ($type_selector->get_selection())
        {
            $this->get_creation_form($type_selector->get_selection());
        }
        else
        {
            echo $type_selector->as_html();
        }
    }

    function get_content_object_type_creation_url($type)
    {
        $object_type_parameters = $this->get_parameters();
        $object_type_parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $type;
        return $this->get_url($object_type_parameters);
    }

    function is_allowed_to_create($type)
    {
        return true;
    }

    protected function get_object_form_variant()
    {
        return null;
    }

    /**
     * Gets the form to create the learning object.
     * @return string A HTML-representation of the form.
     */
    protected function get_creation_form($type)
    {
        $default_object = ContentObject :: factory($type);
        $default_object->set_owner_id($this->get_user_id());

        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $default_object, 'create', 'post', $this->get_url(array_merge(array(RepoViewer :: PARAM_CONTENT_OBJECT_TYPE => $type), $this->get_parameters())), null, array(), true, $this->get_object_form_variant());

        $creation_defaults = $this->get_creation_defaults();
        if ($creation_defaults)
        {
            $form->setParentDefaults($creation_defaults);
        }

        $this->handle_form($form, ContentObjectForm :: TYPE_CREATE);
    }

    /**
     * Gets the editing form
     */
    protected function get_editing_form($content_object_id)
    {
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_EDIT_ID => $content_object_id))), null, array(), true, $this->get_object_form_variant());
        $this->handle_form($form, ContentObjectForm :: TYPE_EDIT);
    }

    /*
	 * Handles the displaying and validating of a create/edit learning object form
	 */
    protected function handle_form($form, $type = ContentObjectForm :: TYPE_CREATE)
    {
        if ($form->validate())
        {
            if ($type == ContentObjectForm :: TYPE_EDIT)
            {
                $form->update_content_object();
                $content_object = $form->get_content_object();
            }
            else
            {
                $content_object = $form->create_content_object();
            }

            if (is_array($content_object))
            {
                $content_object_ids = array();
                foreach ($content_object as $object)
                {
                    $content_object_ids[] = $object->get_id();
                }
            }
            else
            {
                $content_object_ids = $content_object->get_id();
            }

            $redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $content_object_ids));
            $this->redirect(null, false, $redirect_params);
        }
        else
        {

            $this->display_header();
            echo $form->toHtml();
            $this->display_footer();
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('repo_viewer_viewer');
    }

    function get_additional_parameters()
    {
        return array(RepoViewer :: PARAM_EDIT_ID, RepoViewer :: PARAM_CONTENT_OBJECT_TYPE);
    }
}
?>