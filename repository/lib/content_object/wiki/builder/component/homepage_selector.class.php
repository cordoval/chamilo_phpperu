<?php
/**
 * $Id: homepage_selector.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.wiki.component
 */

class WikiBuilderHomepageSelectorComponent extends WikiBuilder
{

    function run()
    {
        $trail = new BreadcrumbTrail();
        
        $root = $this->get_root_content_object();
        $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item(Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID));
        
        $complex_content_object_item->set_is_homepage(1);
        $complex_content_object_item->update();
        
        $this->redirect(Translation :: get('HomepageSelected'), false, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECT));
    
    }
}

?>