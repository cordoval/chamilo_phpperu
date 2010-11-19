<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

use repository\content_object\adaptive_assessment_item\AdaptiveAssessmentItem;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItemForm;
use repository\ContentObjectForm;

/**
 * $Id: updater.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.adaptive_assessment.component
 */
//require_once dirname(__FILE__) . '/../adaptive_assessment_builder_component.class.php';
//require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';

class AdaptiveAssessmentBuilderUpdaterComponent extends AdaptiveAssessmentBuilder
{

    function run()
    {
    	$menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('Update')));

        $complex_content_object_item_id = Request :: get(AdaptiveAssessmentBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(AdaptiveAssessmentBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $parameters = array(AdaptiveAssessmentBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item, AdaptiveAssessmentBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id);

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

        if ($content_object->get_type() == AdaptiveAssessmentItem :: get_type_name())
        {
            $item_lo = $content_object;
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
                if ($item_lo)
                {
                    $item_lo->set_reference($new_id);
                    $item_lo->update();
                }
                else
                {
                    $complex_content_object_item->set_ref($new_id);
                }
            }

            $complex_content_object_item->update();

            $parameters[AdaptiveAssessmentBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = null;

            $this->redirect(Translation :: get('ContentObjectUpdated'), false, array_merge($parameters, array(AdaptiveAssessmentBuilder :: PARAM_BUILDER_ACTION => AdaptiveAssessmentBuilder :: ACTION_BROWSE)));
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