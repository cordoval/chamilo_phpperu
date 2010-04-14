<?php
/**
 * $Id: creator.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../competence_builder_component.class.php';
require_once dirname(__FILE__) . '/../competence_repoviewer/competence_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class CompetenceBuilderCreatorComponent extends CompetenceBuilderComponent
{
    private $rdm;

    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository builder');
        
        $root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
        $cloi_id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
        $publish = Request :: get('publish');
        $type = $rtype = Request :: get(ComplexBuilder :: PARAM_TYPE);
        
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
        
        if($type == 'indicator')
        {
        	$pub = new CompetenceRepoViewer($this, $type);
        }
        else
        {
        	$pub = new ComplexRepoViewer($this, $type);
        }
        
        if ($rtype)
        {
            $pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $rtype);
        }
        
        $pub->set_parameter(ComplexBuilder :: PARAM_ROOT_LO, $root_lo);
        $pub->set_parameter(ComplexBuilder :: PARAM_CLOI_ID, $cloi_id);
        $pub->set_parameter('publish', $publish);
        $pub->set_excluded_objects($exclude);
        $pub->parse_input_from_table();
        
        if (!$pub->is_ready_to_be_published())
        {
            $t = is_array($type) ? implode(',', $type) : $type;
            $p = $this->rdm->retrieve_content_object($parent);
        	$html[] = '<h4>' . sprintf(Translation :: get('AddOrCreateNewTo'), Translation :: get(Utilities:: underscores_to_camelcase($t)), Translation :: get(Utilities:: underscores_to_camelcase($p->get_type())), $p->get_title()) . '</h4><br />'; 
        	$html[] = $pub->as_html();
        }
        else
        {
            $object = $pub->get_selected_objects();
            
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
                $cloi->set_display_order($rdm->select_next_display_order($parent));
                $cloi->set_user_id($this->get_user_id());
                $cloi->create();
            }
            
            $this->redirect(Translation :: get('ObjectAdded'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish')));
        }
        $trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, 'root_lo' => $root_lo, 'publish' => Request :: get('publish'))), RepositoryDataManager :: get_instance()->retrieve_content_object($root_lo)->get_title()));
        $trail->add(new Breadcrumb($this->get_url(array('builder_action' => 'create_cloi', 'type' => Request :: get('type'), 'root_lo' => $root_lo, 'publish' => Request :: get('publish'))), Translation :: get('Create') . ' ' . Translation :: get(Utilities :: underscores_to_camelcase(Request :: get('type')))));
        
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