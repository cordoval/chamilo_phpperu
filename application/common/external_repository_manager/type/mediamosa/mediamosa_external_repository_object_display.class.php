<?php
/**
 * Description of mediamosa_external_repository_displayclass
 *
 * @author jevdheyd
 */


class MediamosaExternalRepositoryObjectDisplay  extends ExternalRepositoryObjectDisplay
{
        private $parent;
    
        function as_html($parent = null)
        {
            $this->parent = $parent;
            $object = $this->get_object();

            $html = array();
            $html[] = '<h3>' . $object->get_title() . ' (' . $object->get_duration() . ')</h3>';
            $html[] = $this->get_video_player_as_html() . '<br/>';
            $html[] = $this->get_additional_properties() . '<br/>';

            return implode("\n", $html);
        }

        function get_video_player_as_html()
	{
            $connector = MediamosaExternalRepositoryConnector :: get_instance($this);

            $object = $this->get_object();

            if($object->get_status() == MediamosaExternalRepositoryObject :: STATUS_AVAILABLE)
            {
                //see which mediafile to play
                if(Request :: get(MediamosaExternalRepositoryManager :: PARAM_MEDIAFILE))
                {
                    $mediafile_id = Request :: get(MediamosaExternalRepositoryManager :: PARAM_MEDIAFILE);
                }
                else
                {
                    $mediafile_id = $object->get_default_mediafile();
                }

                if($mediafile_id)
                {
                    //get player
                    $output = $connector->mediamosa_play_proxy_request($object->get_id(), $mediafile_id);
                }
                else{
                    $output = '';
                }
            }
            else
            {
                $output = Translation :: get('NotAvailable');
            }
            return $output;
	}

        function get_additional_properties(){
            //get different mediafiles (+status)
            $object = $this->get_object();
            $mediafiles = $object->get_mediafiles();

            $html = array();
            $i = 1;
            
            if(is_array($mediafiles))
            {
                foreach($mediafiles as $mediafile)
                {
                    //TODO:jens -> get_link
                    $url = $this->parent->get_url(array(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => MediamosaExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY, ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(), MediamosaExternalRepositoryManager :: PARAM_MEDIAFILE => $mediafile->get_id()));

                    $download = null;
                    if($mediafile->get_is_downloadable()) $download = 'download';

                    $html[] = '<tr><td class="header">' . Translation :: get('Version') . ' ' .$i. '</td><td><a href="' .$url. '">' . $mediafile->get_title() . '</a></td></tr>';

                    $i++;
                }

                $html[] = '<tr><td class="header">' . Translation :: get('PublishedBy') . '</td><td>' . $object->get_publisher() . '</td></tr>';

                if($object->get_creator()) $html[] = '<tr><td class="header">' . Translation :: get('CreatedBy') . '</td><td>' . $object->get_creator() . '</td></tr>';

                return '<table>' . implode("\n",$html) . '</table>';

            }
        }
}
?>
