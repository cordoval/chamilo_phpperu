<?php
namespace repository\content_object\adaptive_assessment;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use repository\RepositoryDataManager;
use repository\ComplexMenu;
use repository\ComplexBuilder;

/**
 * @author Hans De Bisschop
 * @package repository.content_object.adaptive_assessment
 */

class AdaptiveAssessmentBuilderPrerequisitesBuilderComponent extends AdaptiveAssessmentBuilder
{

    function run()
    {
        $complex_content_object_item_id = Request :: get(AdaptiveAssessmentBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
        $parent_complex_content_object_item = Request :: get(AdaptiveAssessmentBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();

        $parameters = array(
                AdaptiveAssessmentBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $parent_complex_content_object_item,
                AdaptiveAssessmentBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item_id);

        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('BuildPrerequisites')));

        if (! $complex_content_object_item_id)
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
            exit();
        }

        $selected_complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_content_object_item_id);
        $form = new PrerequisitesBuilderForm($this->get_user(), $selected_complex_content_object_item, $this->get_url($parameters));

        if ($form->validate())
        {
            $succes = $form->build_prerequisites();
            $message = $succes ? 'PrerequisitesBuild' : 'PrerequisitesNotBuild';
            $this->redirect(Translation :: get($message), ! $succes, array_merge($parameters, array(
                    ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE)));
        }
        else
        {
            $this->display_header();
            echo '<div style="width: 18%; overflow: auto; float: left;">';
            $menu = new ComplexMenu($this->get_root_content_object(), $this->get_complex_content_object_item(), '', true, false);
            echo $menu->render_as_tree();
            echo '</div>';
            echo '<div style="width: 80%; overflow: auto; float: right;">';
            $form->display();
            echo '</div>';
            $this->display_footer();
        }

    }
}

?>