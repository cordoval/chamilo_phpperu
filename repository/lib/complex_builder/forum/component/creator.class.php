<?php
/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */
require_once dirname(__FILE__) . '/../forum_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class ForumBuilderCreatorComponent extends ForumBuilderComponent
{
    private $rdm;

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $menu_trail = $this->get_clo_breadcrumbs();
        $trail->merge($menu_trail);
        $trail->add_help('repository forum builder');

        $object = Request :: get('object');
        $root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        $cloi_id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
        $publish = Request :: get('publish');
        $type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);

        $parameters = array('object' => $object, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id,
        					'publish' => $publish, ComplexBuilder :: PARAM_TYPE => $type);

        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('Add' . Utilities :: underscores_to_camelcase($type))));

        $this->rdm = RepositoryDataManager :: get_instance();

    	$parent = $root_lo;
        if ($cloi_id)
        {
            $parent_cloi = $this->rdm->retrieve_complex_content_object_item($cloi_id);
            $parent = $parent_cloi->get_ref();
        }

        if ($this->get_cloi())
        {
            $lo = $this->rdm->retrieve_content_object($this->get_cloi()->get_ref());
        }
        else
        {
            $lo = $this->get_root_lo();
        }

        $exclude = $this->retrieve_used_items($this->get_root_lo()->get_id());
        $exclude[] = $this->get_root_lo()->get_id();

        if (! $type)
        {
            $type = $lo->get_allowed_types();
        }

        $pub = new ComplexRepoViewer($this, $type);
        if ($rtype)
        {
            $pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
        }

        $pub->set_parameter(ComplexBuilder :: PARAM_ROOT_LO, $root_lo);
        $pub->set_parameter(ComplexBuilder :: PARAM_CLOI_ID, $cloi_id);
        $pub->set_parameter('publish', $publish);
        $pub->set_excluded_objects($exclude);
        $pub->parse_input();

        if (! isset($object))
        {
        	$t = is_array($type) ? implode(',', $type) : $type;
            $p = $this->rdm->retrieve_content_object($parent);
        	$html[] = '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), $t, $p->get_type(), $p->get_title()) . '</h4><br />';
        	$html[] = $pub->as_html();
        }
        else
        {
            if (! is_array($object))
            {
                $object = array($object);
            }

            $rdm = $this->rdm;

            foreach ($object as $obj)
            {
                $type = $rdm->determine_content_object_type($obj);

                $cloi = ComplexContentObjectItem :: factory($type);
                $cloi->set_ref($obj);

                $cloi->set_parent($parent);
                if ($type == 'forum')
                {
                    $cloi->set_display_order($rdm->select_next_display_order($parent));
                }
                $cloi->set_user_id($this->get_user_id());
                $cloi->create();
            }

            $this->redirect(Translation :: get('ObjectAdded'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish')));
        }

        $this->display_header($trail);
        echo '<br />' . implode("\n", $html);
        $this->display_footer();
    }

    private function retrieve_used_items($parent)
    {
        $items = array();

        $clois = $this->rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $parent, ComplexContentObjectItem :: get_table_name()));
        while ($cloi = $clois->next_result())
        {
            if ($cloi->is_complex())
            {
                $items[] = $cloi->get_ref();
                $items = array_merge($items, $this->retrieve_used_items($cloi->get_ref()));
            }
        }

        return $items;
    }
}

?>
