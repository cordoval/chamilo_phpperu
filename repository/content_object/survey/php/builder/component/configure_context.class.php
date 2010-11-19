<?php
namespace repository\content_object\survey;

use repository\RepositoryDataManager;
use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;

require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey.class.php';

class SurveyBuilderConfigureContextComponent extends SurveyBuilder
{

    function run()
    {
        $survey_id = Request :: get(SurveyBuilder :: PARAM_SURVEY_ID);

        $clois = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $survey_id, ComplexContentObjectItem :: get_table_name()));
        while ($cloi = $clois->next_result())
        {
            $survey_page_ids[] = $cloi->get_ref();
        }

        if (count($survey_page_ids) == 0)
        {
            $this->display_header(BreadcrumbTrail :: get_instance());
            $this->display_error_message(Translation :: get('NoPagesSelected'));
            $this->display_footer();
        }

        $succes = true;

        $parent = $this->get_root_lo()->get_id();

        exit();

        //        dump($survey_page_ids);
        //        dump($parent);


        //        foreach ($survey_page_ids as $survey_page_id)
        //        {
        //            $survey_page = RepositoryDataManager :: get_instance()->retrieve_content_object($survey_page_id);
        //            $cloi = ComplexContentObjectItem :: factory($survey_page->get_type());
        //            $cloi->set_parent($parent);
        //            $cloi->set_ref($survey_page_id);
        //            $cloi->set_user_id($this->get_user_id());
        //            $cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));
        //            $succes &= $cloi->create();
        //        }


        $message = $succes ? Translation :: get('PagesAdded') : Translation :: get('PagesNotAdded');

        $this->redirect($message, ! $succes, array(SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_BROWSE_CLO, SurveyBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), 'publish' => Request :: get('publish')));
    }

}

?>