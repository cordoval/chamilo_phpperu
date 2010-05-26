<?php
/**
 * $Id: prerequisites_builder.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once dirname(__FILE__) . '/prerequisites_builder/prerequisites_builder_form.class.php';

class LearningPathBuilderPrerequisitesBuilderComponent extends LearningPathBuilderComponent
{

    function run()
    {
        $cloi_id = Request :: get(LearningPathBuilder :: PARAM_SELECTED_CLOI_ID);
        $parent_cloi = Request :: get(LearningPathBuilder :: PARAM_CLOI_ID);
        
        $menu_trail = $this->get_clo_breadcrumbs();
        $trail = new BreadcrumbTrail(false);
        $trail->merge($menu_trail);
        
        $parameters = array(LearningPathBuilder :: PARAM_ROOT_CONTENT_OBJECT => $this->get_root_content_object()->get_id(), LearningPathBuilder :: PARAM_CLOI_ID => $parent_cloi, LearningPathBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'));
        
        $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('BuildPrerequisites')));
        
        if (! $cloi_id)
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NoObjectSelected'));
            $this->display_footer();
            exit();
        }
        
        $selected_cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($cloi_id);
        $form = new PrerequisitesBuilderForm($this->get_user(), $selected_cloi, $this->get_url($parameters));
        
        if ($form->validate())
        {
            $succes = $form->build_prerequisites();
            $message = $succes ? 'PrerequisitesBuild' : 'PrerequisitesNotBuild';
            $this->redirect(Translation :: get($message), ! $succes, array_merge($parameters, array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO)));
        }
        else
        {
            $this->display_header($trail);
            echo '<div style="width: 18%; overflow: auto; float: left;">';
            $menu = new ComplexMenu($this->get_root_content_object(), $this->get_cloi(), '', true, false);
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