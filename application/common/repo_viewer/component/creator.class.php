<?php
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
            //if (Request :: get(RepoViewer :: PARAM_EDIT))
            //{
            echo $this->get_editing_form($content_object_id);
            //}
        }
        else
        {
            $type = $this->get_type();
            if ($type)
            {
                echo $this->get_creation_form($type);
            }
            else
            {
                $this->display_header();
                echo $this->get_type_selector();
                $this->display_footer();
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
        $selection_types = array();
        $html = array();

        $html[] = '<div class="content_object_selection">';

        foreach ($this->get_types() as $object_type)
        {
            $selection_types[$object_type] = Translation :: get(ContentObject :: type_to_class($object_type) . 'TypeName');
            $object_type_parameters = $this->get_parameters();
            $object_type_parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $object_type;

            $html[] = '<a href="' . $this->get_url($object_type_parameters) . '">';
            $html[] = '<div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $object_type . '.png);">';
            $html[] = Translation :: get(ContentObject :: type_to_class($object_type) . 'TypeName');
            $html[] = '</div>';
            $html[] = '</a>';
        }

        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $form = new FormValidator('select_type', 'post', $this->get_url($this->get_parameters()));
        $form->addElement('hidden', 'tool');
        $form->addElement('hidden', RepoViewer :: PARAM_ACTION);
        $form->addElement('select', RepoViewer :: PARAM_CONTENT_OBJECT_TYPE, Translation :: get('CreateANew'), $selection_types, array('class' => 'learning-object-creation-type postback'));
        $form->addElement('static', '', '', implode("\n", $html));
        $form->addElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal select'));
        $form->addElement('html', '<br /><br />' . ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/postback.js'));
        $form->setDefaults(array(RepoViewer :: PARAM_ACTION => Request :: get(RepoViewer :: PARAM_ACTION)));

        if ($form->validate())
        {
            $values = $form->exportValues();
            $type = $values[RepoViewer :: PARAM_CONTENT_OBJECT_TYPE];
            return $this->get_creation_form($type);
        }
        else
        {
            return $form->toHTML();
        }
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

        return $this->handle_form($form, ContentObjectForm :: TYPE_CREATE);
    }

    /**
     * Gets the editing form
     */
    protected function get_editing_form($content_object_id)
    {
        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($content_object_id);
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url(array_merge($this->get_parameters(), array(RepoViewer :: PARAM_EDIT_ID => $content_object_id))), null, array(), true, $this->get_object_form_variant());
        return $this->handle_form($form, ContentObjectForm :: TYPE_EDIT);
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