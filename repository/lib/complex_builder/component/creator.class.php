<?php
/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
//require_once dirname(__FILE__) . '/../../complex_repo_viewer/complex_repo_viewer.class.php';

class ComplexBuilderComponentCreatorComponent extends ComplexBuilderComponent
{
    private $rdm;
    private $root_content_object_id;
    private $type;

    function display_header()
    {
        parent :: display_header();
        $type = is_array($this->type) ? implode(',', $this->type) : $this->type;
        $content_object = $this->rdm->retrieve_content_object($this->root_content_object_id);
        echo '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), Translation :: get(Utilities :: underscores_to_camelcase($type)), Translation :: get(Utilities :: underscores_to_camelcase($content_object->get_type())), $content_object->get_title()) . '</h4><br />';
    }

    function run()
    {
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository builder');

        $complex_content_object_item_id = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $this->type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);

        $this->rdm = RepositoryDataManager :: get_instance();

        $this->root_content_object_id = $this->get_parent()->get_root_content_object_id();

        if ($complex_content_object_item_id)
        {
            $parent_complex_content_object_item = $this->rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
            $this->root_content_object_id = $parent_complex_content_object_item->get_ref();
        }

        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->rdm->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $exclude = $this->retrieve_used_items($this->get_root_content_object()->get_id());
        $exclude[] = $this->get_root_content_object()->get_id();

        if (! $this->type)
        {
            $this->type = $content_object->get_allowed_types();
        }

        $complex_repository_viewer = new RepoViewer($this, $this->type);
        if ($rtype)
        {
            $complex_repository_viewer->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
        }

        $complex_repository_viewer->set_parameter(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_content_object_item_id);
        $complex_repository_viewer->set_excluded_objects($exclude);

        if (! $complex_repository_viewer->is_ready_to_be_published())
        {
            $trail->add(new Breadcrumb($this->get_url(array('builder_action' => 'create_complex_content_object_item', 'type' => Request :: get('type'))), Translation :: get('Create') . ' ' . Translation :: get(Utilities :: underscores_to_camelcase(Request :: get('type')))));
            $complex_repository_viewer->run();
        }
        else
        {
            $objects = $complex_repository_viewer->get_selected_objects();

            if (! is_array($objects))
            {
                $objects = array($objects);
            }

            $rdm = $this->rdm;

            foreach ($objects as $object)
            {
                $type = $rdm->determine_content_object_type($object);

                $complex_content_object_item = ComplexContentObjectItem :: factory($type);
                $complex_content_object_item->set_ref($object);

                $complex_content_object_item->set_parent($this->root_content_object_id);
                $complex_content_object_item->set_display_order($rdm->select_next_display_order($this->root_content_object_id));
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->create();
            }

            $this->redirect(Translation :: get('ObjectAdded'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id));
        }
    }

    private function retrieve_used_items($parent)
    {
        $items = array();

        $complex_content_object_items = $this->rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        while ($complex_content_object_item = $complex_content_object_items->next_result())
        {
            if ($complex_content_object_item->is_complex())
            {
                $items[] = $complex_content_object_item->get_ref();
                $items = array_merge($items, $this->retrieve_used_items($complex_content_object_item->get_ref()));
            }
        }

        return $items;
    }
}

?>