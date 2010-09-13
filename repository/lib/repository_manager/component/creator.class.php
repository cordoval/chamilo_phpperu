<?php
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
        $type_options = array();
        $type_options[''] = '-- ' . Translation :: get('SelectObject') . ' --';
        $extra_params = array();
        $this->forbidden_types = array(PortfolioItem :: get_type_name(), LearningPathItem :: get_type_name(), ScormItem :: get_type_name());

        foreach ($this->get_allowed_content_object_types() as $type)
        {
        	$type_options[$type] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
        }

        $type_form = new FormValidator('create_type', 'post', $this->get_url($extra_params));

        asort($type_options);
        $type_form->addElement('select', RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE, Translation :: get('CreateANew'), $type_options, array('class' => 'learning-object-creation-type postback'));
        $type_form->addElement('style_submit_button', 'submit', Translation :: get('Select'), array('class' => 'normal select'));
        $type_form->addElement('html', '<br /><br />' . ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/postback.js'));

        $type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE) : Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE));

        if ($type)
        {
            $category = Request :: get(RepositoryManager :: PARAM_CATEGORY_ID);

            $object = ContentObject :: factory($type);
            $object->set_owner_id($this->get_user_id());
            $object->set_parent_id($category);

            $content_object_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array_merge($extra_params, array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type))), null);

            if ($content_object_form->validate())
            {
                $object = $content_object_form->create_content_object();

                if (! $object)
                {
                    $this->redirect(Translation :: get('FileCouldNotBeUploaded'), true, array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_CREATE_CONTENT_OBJECTS, 'type' => $type));
                }

                if (! is_array($object) && ($object instanceof ComplexContentObjectSupport || count($extra_params) == 2 || count($extra_params) == 3))
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
                    }
                    else
                    {
                        $parent = $object->get_parent_id();
                    }

                    $parameters = array();
                    $parameters[Application :: PARAM_ACTION] = RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS;
                    $parameters[RepositoryManager :: PARAM_CATEGORY_ID] = $parent;
                    $message = Utilities :: underscores_to_camelcase($object->get_type()) . 'TypeNameCreated';
                    $this->redirect(Translation :: get($message), false, $parameters);
                }
            }
            else
            {
                if (! Request :: get('publish'))
                {
//                    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
//                    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)), Translation :: get(ContentObject :: type_to_class($type) . 'CreationFormTitle')));
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
            if (! Request :: get('publish'))
            {
//                if ($extra)
//                {
//                    //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddContentObject')));
//                }
//                else
//                {
//                    //$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
//                }
            }

            if (Request :: get('publish'))
            {
                $this->display_header(null, false, true);
            }
            else
            {
                $this->display_header(null, false, true);
            }

            //echo $extra;
            $quotamanager = new QuotaManager($this->get_user());

            if ($quotamanager->get_available_database_space() <= 0)
            {
                Display :: warning_message(htmlentities(Translation :: get('MaxNumberOfContentObjectsReached')));
            }
            else
            {
                $renderer = clone $type_form->defaultRenderer();
                $renderer->setElementTemplate('{label}&nbsp;{element}&nbsp;');
                $type_form->accept($renderer);
                echo $renderer->toHTML();

                $user_objects = $quotamanager->get_used_database_space();
                echo $this->get_content_object_type_counts(($user_objects == 0));
            }
            $this->display_footer();
        }
    }

    function get_content_object_type_counts($use_general_statistics = false)
    {
        $type_categories = array();
        $type_counts = array();
        $categories = array();
        $most_used_type_count = 0;

        if (! $use_general_statistics)
        {
            $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
        }
        else
        {
            $condition = null;
        }

        foreach ($this->get_allowed_content_object_types() as $type)
        {
        		$package_info = PackageInfo :: factory(Registration :: TYPE_CONTENT_OBJECT, $type);
                $package_info = $package_info->get_package_info();
                $category = $package_info['package']['category'];
                $category_name = Translation :: get(Utilities :: underscores_to_camelcase($category));

                if (! in_array($category, array_keys($categories)))
                {
                    $categories[$category] = $category_name;
                }

                if (! is_array($type_categories[$category]))
                {
                    $type_categories[$category] = array();
                }

                $type_categories[$category][Translation :: get(ContentObject :: type_to_class($type) . 'TypeName')] = $type;

                $count = $this->count_type_content_objects($type, $condition);
                $type_counts[$type] = $count;
                if ($count > $most_used_type_count)
                {
                    $most_used_type_count = $count;
                }
        }

        arsort($type_counts, SORT_STRING);

        $limit = round($most_used_type_count / 2);
        $type_counts = array_slice($type_counts, 0, 10);

        $most_used_html = array();

        foreach ($type_counts as $type => $count)
        {
            if ($count > 0)
            {
                $most_used_html[] = '<a href="' . $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
//                $most_used_html[] = '<a href="' . $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $type . '.png);">';
                $most_used_html[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
                $most_used_html[] = '</div></a>';
            }
        }

        asort($categories);

        $renderer_name = Utilities :: camelcase_to_underscores(get_class($this));
        $tabs = new DynamicTabsRenderer($renderer_name);
        $tabs->add_tab(new DynamicContentTab(self :: TAB_MOST_USED, Translation :: get('MostUsed'), Theme :: get_image_path() . 'place_mini_most_used.png', implode("\n", $most_used_html)));

        foreach ($categories as $category => $category_name)
        {
            $types = $type_categories[$category];
            ksort($types);

            $types_html = array();

            foreach ($types as $name => $type)
            {
                $types_html[] = '<a href="' . $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
//                $types_html[] = '<a href="' . $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $type . '.png);">';
                $types_html[] = $name;
                $types_html[] = '</div></a>';
            }

            $tabs->add_tab(new DynamicContentTab($category, $category_name, Theme :: get_image_path() . 'place_mini_' . $category . '.png', implode("\n", $types_html)));
        }

        $html[] = $tabs->render();
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');

        return implode("\n", $html);
    }
    
    function get_allowed_content_object_types()
    {
    	$types = $this->get_content_object_types(true, false);
    	foreach($types as $index => $type)
    	{
    		$registration = AdminDataManager :: get_registration($type, Registration :: TYPE_CONTENT_OBJECT);
        	if(!RepositoryRights :: is_allowed_in_content_objects_subtree(RepositoryRights :: ADD_RIGHT, $registration->get_id())) 
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