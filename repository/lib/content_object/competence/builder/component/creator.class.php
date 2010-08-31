<?php
/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
//require_once dirname(__FILE__) . '/../competence_builder_component.class.php';
//require_once dirname(__FILE__) . '/../competence_repoviewer/competence_repo_viewer.class.php';
//require_once dirname(__FILE__) . '/lib/conten../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/competence/competence.class.php';
require_once Path :: get_repository_path() . '/lib/complex_builder/competence/competence_repoviewer/competence_repo_viewer.class.php';
require_once Path :: get_repository_path() . '/lib/complex_builder/competence/competence_repoviewer/component/browser.class.php';
require_once Path :: get_repository_path() . '/lib/complex_builder/complex_repo_viewer.class.php';

class CompetenceBuilderCreatorComponent extends CompetenceBuilder implements RepoViewerInterface
{
    private $repository_data_manager;
    private $type;

    function run()
    {
        //ComplexBuilderComponent :: launch($this);
        //}
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository builder');

        $root_content_object = $this->get_root_content_object(); //Request :: get(ComplexBuilder :: PARAM_ROOT_CONTENT_OBJECT);
        $complex_content_object_item_id = $this->get_complex_content_object_item_id(); //Request :: get(ComplexBuilder :: PARAM_CLOI_ID);


        //$publish = Request :: get('publish');
        $this->type = Request :: get(ComplexBuilder :: PARAM_TYPE);
        $this->repository_data_manager = RepositoryDataManager :: get_instance();

        $parent = $this->get_root_content_object()->get_id();

        if ($complex_content_object_item_id)
        {
            $parent_complex_content_object_item = $this->repository_data_manager->retrieve_complex_content_object_item($complex_content_object_item_id);
            $parent = $parent_complex_content_object_item->get_ref();
        }

        if ($this->get_complex_content_object_item())
        {
            $content_object = $this->repository_data_manager->retrieve_content_object($this->get_complex_content_object_item_id()->get_ref());
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

        if ($this->type == Indicator :: get_type_name())
        {
            $publication = new CompetenceRepoViewer($this, $this->type);
        }
        else
        {
            $publication = RepoViewer :: construct($this);
        }

        if ($this->type)
        {
            $publication->set_parameter(ComplexBuilder :: PARAM_TYPE, $this->type);
        }

        $publication->set_parameter(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_content_object_item_id);
        $publication->set_excluded_objects($exclude);
        $publication->parse_input_from_table();

        if (! $publication->is_ready_to_be_published())
        {
            $t = is_array($this->type) ? implode(',', $this->type) : $this->type;
            $p = $this->repository_data_manager->retrieve_content_object($parent);
            $html[] = '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), Translation :: get(Utilities :: underscores_to_camelcase($t)), Translation :: get(Utilities :: underscores_to_camelcase($p->get_type())), $p->get_title()) . '</h4><br />';
            $html[] = $publication->as_html();
        }
        else
        {
            $object = $publication->get_selected_objects();

            if (! is_array($object))
            {
                $object = array($object);
            }

            $repository_data_manager = $this->repository_data_manager;

            foreach ($object as $obj)
            {
                $type = $repository_data_manager->determine_content_object_type($obj);

                $complex_content_object_item = ComplexContentObjectItem :: factory($type);
                $complex_content_object_item->set_ref($obj);

                $complex_content_object_item->set_parent($parent);
                $complex_content_object_item->set_display_order($repository_data_manager->select_next_display_order($parent));
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->create();
            }

            $this->redirect(Translation :: get('ObjectAdded'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id));
        }
        $trail->add(new Breadcrumb($this->get_url(array('builder_action' => null)), $root_content_object->get_title()));
        $trail->add(new Breadcrumb($this->get_url(array('builder_action' => 'create_complex_content_object_item', 'type' => Request :: get('type'))), Translation :: get('Create') . ' ' . Translation :: get(Utilities :: underscores_to_camelcase(Request :: get('type')))));

        $this->display_header($trail);
        echo '<br />' . implode("\n", $html);
        $this->display_footer();
    }

    private function retrieve_used_items($parent)
    {
        $items = array();

        $complex_content_object_items = $this->repository_data_manager->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
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

    function get_allowed_content_object_types()
    {
        return array($this->type);
    }
}

?>