<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;

/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
//require_once dirname(__FILE__) . '/../complex_repo_viewer.class.php';

class ComplexBuilderComponentUpdaterComponent extends ComplexBuilderComponent
{

    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();

        $complex_content_object_item_id = Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $parameters = array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item, ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id);

        $rdm = RepositoryDataManager :: get_instance();
        $complex_content_object_item = $rdm->retrieve_complex_content_object_item($complex_content_object_item_id);
        $content_object = $rdm->retrieve_content_object($complex_content_object_item->get_ref());

        $type = $content_object->get_type();

        $complex_content_object_item_form = ComplexContentObjectItemForm :: factory_with_type(ComplexContentObjectItemForm :: TYPE_CREATE, $type, $complex_content_object_item, 'create_complex', 'post', $this->get_url());

        if ($complex_content_object_item_form)
        {
            $elements = $complex_content_object_item_form->get_elements();
            $defaults = $complex_content_object_item_form->get_default_values();
        }

        $content_object_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url($parameters), null, $elements);
        $content_object_form->setDefaults($defaults);

        if ($content_object_form->validate())
        {
            $content_object_form->update_content_object();

            if ($content_object_form->is_version())
            {
                $old_id = $complex_content_object_item->get_ref();
                $new_id = $content_object->get_latest_version()->get_id();
                $complex_content_object_item->set_ref($new_id);

                $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_id, ComplexContentObjectItem :: get_table_name()));
                while ($child = $children->next_result())
                {
                    $child->set_parent($new_id);
                    $child->update();
                }
            }

            if ($complex_content_object_item_form)
                $complex_content_object_item_form->update_cloi_from_values($content_object_form->exportValues());
            else
                $complex_content_object_item->update();

            $parameters[ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;

            $this->redirect(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES), false, array_merge($parameters, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE)));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('repository builder');

            $trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, 'cid' => Request :: get('cid'))), $this->get_root_content_object()->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array('builder_action' => ComplexBuilder :: ACTION_UPDATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id, 'cid' => Request :: get('cid'))), Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES)));

            $this->display_header($trail);
            echo $content_object_form->toHTML();
            $this->display_footer();
        }

    }
}

?>