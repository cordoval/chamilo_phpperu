<?php
namespace repository\content_object\mediamosa;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use repository\ContentObjectDisplay;
use repository\RepositoryDataManager;
use repository\ExternalSync;
use common\extensions\external_repository_manager\implementation\mediamosa\MediamosaExternalRepositoryManagerConnector;
use common\extensions\external_repository_manager\implementation\mediamosa\MediamosaExternalRepositoryObject;

/**
 * Description of MediamosaDisplay class
 *
 * @author jevdheyd
 */
//require_once Path :: get_application_path() . 'common/external_repository_manager/type/mediamosa/mediamosa_external_repository_server_object.class.php';
//require_once Path :: get_application_path() . 'common/external_repository_manager/type/mediamosa/mediamosa_external_repository_data_manager.class.php';
require_once Path :: get_common_extensions_path() . '/external_repository_manager/implementation/mediamosa/php/mediamosa_external_repository_manager_connector.class.php';
//require_once Path :: get_application_path() . 'common/external_repository_manager/type/mediamosa/mediamosa_external_repository_object.class.php';

class MediamosaDisplay extends ContentObjectDisplay
{
    private $mediamosa_object;
    private $mediamosa_external_repository_manager_connector;
    private $connection_lost;

    const PARAM_MEDIAFILE = 'mediafile_id';

    function set_mediamosa_object()
    {
//        if(!$this->mediamosa_external_repository_manager_connector)
//        {
//            $object = $this->get_content_object();
//            $external_repository = RepositoryDataManager :: get_instance()->retrieve_external_instance($object->get_server_id());
//            $this->mediamosa_external_repository_manager_connector = MediamosaExternalRepositoryManagerConnector :: get_instance($external_repository);
//        }
//

        if(!$this->mediamosa_external_repository_manager_connector)
        {
            $object = $this->get_content_object();

            $rdm = RepositoryDataManager :: get_instance();
            $condition = new EqualityCondition(ExternalSync :: PROPERTY_CONTENT_OBJECT_ID, $object->get_id());
            $sync = $rdm->retrieve_external_sync($condition);

            $external_repository = $sync->get_external();
            $this->mediamosa_external_repository_manager_connector = MediamosaExternalRepositoryManagerConnector :: get_instance($external_repository);
        }

        if(!$this->mediamosa_object)
        {
            if(!$this->mediamosa_object = $this->mediamosa_external_repository_manager_connector->retrieve_mediamosa_asset($sync->get_external_object_id())){
                $this->connection_lost = true;
            }
        }
    }

    function get_video_player_as_html()
	{
            $this->set_mediamosa_object();

            if($this->mediamosa_object->get_status() == MediamosaExternalRepositoryObject :: STATUS_AVAILABLE)
            {
                //see which mediafile to play
                if(Request :: get(self :: PARAM_MEDIAFILE))
                {
                    $mediafile_id = Request :: get(self :: PARAM_MEDIAFILE);
                }
                else
                {
                    $mediafile_id = $this->mediamosa_object->get_default_mediafile();
                }

                if($mediafile_id)
                {
                    //get player
                    $output = $this->mediamosa_external_repository_manager_connector->mediamosa_play_proxy_request($this->mediamosa_object->get_id(), $mediafile_id);
                }
                else{
                    $output = '';
                }
            }
            else
            {
                $output = Translation :: get('VideoNotAvailable');
            }
            return $output;
	}

       function get_additional_properties(){
            //get different mediafiles (+status)
            $mediafiles = $this->mediamosa_object->get_mediafiles();

            $html = array();
            $i = 1;
            $html[] = '<tr><td class="header">' . Translation :: get('Versions').'</td></tr>';

            if(is_array($mediafiles))
            {
                foreach($mediafiles as $mediafile)
                {
                    $object = $this->get_content_object();
                    $url = $this->get_content_object_url($object);
                    $html[] = '<tr><td><a href="' .$url. '&' .self :: PARAM_MEDIAFILE. '=' .$mediafile->get_id(). '">' . $mediafile->get_title() . '</a></td></tr>';

                    $i++;
                }
                $html2 = array();

                $html2[] = Translation :: get('PublishedBy') . ' : ' . $this->mediamosa_object->get_publisher() . '<br />';

                if($this->mediamosa_object->get_creator()) $html2[] = Translation :: get('CreatedBy') . ' : ' . $this->mediamosa_object->get_creator();

                return '<table class="data_table data_table_no_header">' . implode("\n",$html) . '</table>' . implode("\n",$html2);
            }
        }

    function get_preview($is_thumbnail = false)
    {

        $this->set_mediamosa_object();
        $object = $this->get_content_object();

        if ($is_thumbnail && $this->mediamosa_object)
        {
                return '<img src="' . $this->mediamosa_object->get_thumbnail() . '" title="' . $object->get_title() . '" class="thumbnail" />';
        }
        else
        {
            if(!$this->mediamosa_object)
            {
                return Translation :: get('ConnectionLost');
            }
        }
    }

    function get_description()
    {
        $html = parent :: get_description();

        $this->set_mediamosa_object();

        if(!$this->connection_lost)
        {
            $video_element = $this->get_video_player_as_html();

            $additional_properties = $this->get_additional_properties();

            return str_replace(self :: DESCRIPTION_MARKER, '<div class="link_url" style="margin-top: 1em;">' . $video_element . '<br/>' .$additional_properties. '</div>' . self :: DESCRIPTION_MARKER, $html);
        }
        else
        {
            return '<div>' . Translation :: get('ConnectionLost') . '</div>';
        }
    }
}
?>