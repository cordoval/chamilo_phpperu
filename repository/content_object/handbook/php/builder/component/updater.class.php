<?php
namespace repository\content_object\handbook;

use common\libraries\Utilities;

use repository\ComplexBuilderComponent;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItemForm;
use repository\content_object\handbook_item\HandbookItem;
use repository\ContentObjectForm;
use common\libraries\Translation;

class HandbookBuilderUpdaterComponent extends HandbookBuilder
{

    private $complex_builder_updater_component;

    function run()
    {
        $this->complex_builder_browser_component = ComplexBuilderComponent :: factory(ComplexBuilderComponent :: UPDATER_COMPONENT, $this);
        $trail = BreadcrumbTrail :: get_instance();

        $root_content_object = $this->get_root_content_object();
        $complex_content_object_item_id = Request :: get(HandbookBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(HandbookBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $parameters = array(
                HandbookBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item,
                HandbookBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id);

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

        if ($content_object->get_type() == HandbookItem :: get_type_name())
        {
            $item_content_object = $content_object;
            $content_object = $rdm->retrieve_content_object($content_object->get_reference());
        }

        $content_object_form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $content_object, 'edit', 'post', $this->get_url($parameters), null, $elements);
        $content_object_form->setDefaults($defaults);

        if ($content_object_form->validate())
        {
            $content_object_form->update_content_object();

            if ($content_object_form->is_version())
            {
                $new_id = $content_object->get_latest_version()->get_id();
                if ($item_content_object)
                {
                    $item_content_object->set_reference($new_id);
                    $item_content_object->update();
                }
                else
                {
                    $complex_content_object_item->set_ref($new_id);
                }
            }

            if ($complex_content_object_item_form)
                $complex_content_object_item_form->update_complex_content_object_item_from_values($content_object_form->exportValues());
            else
                $complex_content_object_item->update();

            $parameters[HandbookBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;

            $this->redirect(Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('Handbook')), Utilities :: COMMON_LIBRARIES), false, array_merge($parameters, array(
                    HandbookBuilder :: PARAM_BUILDER_ACTION => HandbookBuilder :: ACTION_BROWSE)));
        }
        else
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add_help('repository learnpath builder');
            $this->display_header($trail);
            echo $content_object_form->toHTML();
            $this->display_footer();
        }

    }
}

?>