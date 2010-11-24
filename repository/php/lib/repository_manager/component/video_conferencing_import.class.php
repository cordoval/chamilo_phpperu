<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

class RepositoryManagerVideoConferencingImportComponent extends RepositoryManagerVideoConferencingComponent
{

    public function run()
    {
        $export = $this->get_video_conferencing_from_param();
        $repository_object_id = Request :: get(RepositoryManager :: PARAM_EXTERNAL_OBJECT_ID);

        try
        {
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIDEO_CONFERENCING_BROWSE, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $co_id)), Translation :: get('VideoConferencing')));
            $trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIDEO_CONFERENCING_LIST_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $co_id, RepositoryManager :: PARAM_VIDEO_CONFERENCING_ID => $export->get_id())), $export->get_title()));

            $trail->add(new Breadcrumb(null, Translation :: get('VideoConferencingImportObject') . ' : ' . $export->get_title()));
            $this->display_header($trail, false, true);

            $repository_object_infos = $this->get_repository_object_infos($repository_object_id);

            $list_of_one_object = $this->add_chamilo_infos($export, array(array(BaseVideoConferencingConnector :: EXTERNAL_OBJECT_KEY => $repository_object_infos)));

            //DebugUtilities::show($list_of_one_object);

            $form = new ExternalRepositoryImportForm($list_of_one_object[0], $export, $this->get_url(array(RepositoryManager :: PARAM_VIDEO_CONFERENCING_ID => $export->get_id(), RepositoryManager :: PARAM_EXTERNAL_OBJECT_ID => $repository_object_id)));

            if(!$form->isSubmitted())
            {
                $form->display();
            }
            else
            {
                $exporter = BaseVideoConferencingConnector :: get_instance($export);

                if($exporter->import($repository_object_id, $this->get_user_id()))
                {
                    $form->display_import_success();
                }
                else
                {
                    throw new Exception('An error occured during the import');
                }
            }

        }
        catch(Exception $ex)
        {
            $this->display_header($trail, false, true);
            $this->display_error_message($ex->getMessage());
        }

        $this->display_footer();
    }

    private function get_repository_object_infos($repository_object_id)
    {
        $export = $this->get_video_conferencing_from_param();
        if (isset($export) && $export->get_enabled() == 1)
        {
            $exporter = BaseVideoConferencingConnector :: get_instance($export);

            return $exporter->get_repository_object_infos($repository_object_id);
        }
        else
        {
            return null;
        }
    }

}
?>