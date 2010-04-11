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
class RepositoryManagerCreatorComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository general');

        $type_options = array();
        $type_options[''] = '-- ' . Translation :: get('SelectObject') . ' --';
        $extra_params = array();
        $this->forbidden_types = array('portfolio_item', 'learning_path_item', 'scorm_item', 'survey_item', 'survey_page_item');

        foreach ($this->get_content_object_types(true) as $type)
        {
            $setting = PlatformSetting :: get('allow_' . $type . '_creation', 'repository');
            if (($setting || $this->get_user()->is_platform_admin()) && !in_array($type, $this->forbidden_types))
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
            $object = new AbstractContentObject($type, $this->get_user_id(), $category);
            $lo_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array_merge($extra_params, array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type))), null);

            if ($lo_form->validate())
            {
                $object = $lo_form->create_content_object();

                if (! $object)
                {
                    $this->redirect(Translation :: get('FileCouldNotBeUploaded'), true, array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_CREATE_CONTENT_OBJECTS, 'type' => $type));
                }

                if (! is_array($object) && ($object->is_complex_content_object() || count($extra_params) == 2 || count($extra_params) == 3))
                {
                    $parameters = array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BUILD_COMPLEX_CONTENT_OBJECT, ComplexBuilder :: PARAM_ROOT_LO => $object->get_id());
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
                    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
                    $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)), Translation :: get(ContentObject :: type_to_class($type) . 'CreationFormTitle')));
                    $this->display_header($trail, false, true);
                }
                else
                {
                    $this->display_header($trail, false, true);
                }

                $lo_form->display();
                $this->display_footer();
            }
        }
        else
        {
            if (! Request :: get('publish'))
            {
                if ($extra)
                {
                    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddContentObject')));
                }
                else
                {
                    $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
                }
            }

            if (Request :: get('publish'))
            {
                $this->display_header($trail, false, true);
            }
            else
            {
                $this->display_header($trail, false, true);
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
        $type_counts = array();
        $most_used_type_count = 0;

        if (! $use_general_statistics)
        {
            $condition = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
        }
        else
        {
            $condition = null;
        }

        foreach ($this->get_content_object_types(true) as $type)
        {
            $count = $this->count_type_content_objects($type, $condition);
            $type_counts[$type] = $count;
            if ($count > $most_used_type_count)
            {
                $most_used_type_count = $count;
            }
        }

        arsort($type_counts, SORT_STRING);

        $limit = round($most_used_type_count / 2);
        $html = array();
        $used_html = array();
        $unused_html = array();

        foreach ($type_counts as $type => $count)
        {
            $object = array();
            $setting = PlatformSetting :: get('allow_' . $type . '_creation', 'repository');
            if (($setting || $this->get_user()->is_platform_admin()) && !in_array($type, $this->forbidden_types))
            {
                $object[] = '<a href="' . $this->get_url(array(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE => $type)) . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
                $object[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
                $object[] = '</div></a>';
            }

            if ($count >= $limit)
            {
                $used_html[$type] = implode("\n", $object);
            }
            else
            {
                $unused_html[$type] = implode("\n", $object);
            }
        }

        ksort($used_html, SORT_STRING);
        ksort($unused_html, SORT_STRING);

        if (! $use_general_statistics)
        {
            $html[] = '<h3>' . Translation :: get('MostUsedObjectTypes') . '</h3>';
        }
        else
        {
            $html[] = '<h3>' . Translation :: get('MostUsedGeneralObjectTypes') . '</h3>';
        }

        $html[] = '<div class="content_object_selection">';
        $html[] = implode("\n", $used_html);
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        if (count($unused_html) > 0)
        {
            $html[] = Utilities :: add_block_hider();
            $html[] = Utilities :: build_block_hider('other_content_object_types', null, true);

            //$html[] = '<h3>'. Translation :: get('OtherObjectTypes') .'</h3>';
            $html[] = '<div class="content_object_selection">';
            $html[] = implode("\n", $unused_html);
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $html[] = Utilities :: build_block_hider();
        }

        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');

        return implode("\n", $html);
        ;
    }
}
?>