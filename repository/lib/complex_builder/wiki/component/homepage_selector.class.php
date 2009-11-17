<?php
/**
 * $Id: homepage_selector.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki.component
 */
require_once dirname(__FILE__) . '/../wiki_builder_component.class.php';

class WikiBuilderHomepageSelectorComponent extends WikiBuilderComponent
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        
        $root = $this->get_root_lo();
        $cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID));
        
        $cloi->set_is_homepage(1);
        $cloi->update();
        
        $this->redirect(Translation :: get('HomepageSelected'), false, array(ComplexBuilder :: PARAM_ROOT_LO => $root->get_id(), ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, 'publish' => Request :: get('publish')));
    
    }
}

?>