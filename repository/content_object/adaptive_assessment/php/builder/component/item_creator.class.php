<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;

use repository\content_object\adaptive_assessment_item\AdaptiveAssessmentItem;
use common\extensions\repo_viewer\RepoViewerInterface;
use common\extensions\repo_viewer\RepoViewer;
use repository\ComplexBuilder;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentBuilderItemCreatorComponent extends AdaptiveAssessmentBuilder implements RepoViewerInterface
{

    private $rdm;
    private $type;

    function run()
    {
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('Create')));
        $trail->add_help('repository learnpath builder');

        $root_content_object = $this->get_root_content_object();
        $complex_content_object_item_id = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $this->type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);

        $this->rdm = RepositoryDataManager :: get_instance();

        $parent = $root_content_object->get_id();
        if ($complex_content_object_item_id)
        {
            $parent_complex_content_object_item = $this->rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
            $parent = $parent_complex_content_object_item->get_ref();
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

        if (! RepoViewer :: is_ready_to_be_published())
        {
            $pub = RepoViewer :: construct($this);
            if ($rtype)
            {
                $pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
            }

            $pub->set_parameter(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID, $complex_content_object_item_id);
            $pub->set_excluded_objects($exclude);
            $t = is_array($this->type) ? implode(',', $this->type) : $this->type;
            $p = $this->rdm->retrieve_content_object($parent);
            //$html[] = '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), $t, $p->get_type(), $p->get_title()) . '</h4><br />';
            $pub->run();
        }
        else
        {
            $object = RepoViewer :: get_selected_objects();

            if (! is_array($object))
            {
                $object = array($object);
            }

            $rdm = $this->rdm;

            foreach ($object as $obj)
            {
                $content_object = new AdaptiveAssessmentItem();
                $content_object->set_title(AdaptiveAssessmentItem :: get_type_name());
                $content_object->set_description(AdaptiveAssessmentItem :: get_type_name());
                $content_object->set_owner_id($this->get_user_id());
                $content_object->set_reference($obj);
                $content_object->set_parent_id(0);

                $content_object->create();

                $complex_content_object_item = ComplexContentObjectItem :: factory(AdaptiveAssessmentItem :: get_type_name());
                $complex_content_object_item->set_ref($content_object->get_id());

                $complex_content_object_item->set_parent($parent);
                $complex_content_object_item->set_display_order($rdm->select_next_display_order($parent));
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->create();
            }

            $this->redirect(Translation :: get('ObjectAdded'), false, array(
                    ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE,
                    ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id));
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

    function get_allowed_content_object_types()
    {
        return array($this->type);
    }

}

?>