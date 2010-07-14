<?php
/**
 * Description of mediamosa_external_repository_displayclass
 *
 * @author jevdheyd
 */

class MediamosaExternalRepositoryObjectDisplay extends ExternalRepositoryObjectDisplay
{

    function get_display_properties()
    {
        $properties = parent :: get_display_properties();

        //get different mediafiles (+status)
        $object = $this->get_object();
        $mediafiles = $object->get_mediafiles();

        $html = array();
        $i = 1;

        if (is_array($mediafiles))
        {
            foreach ($mediafiles as $mediafile)
            {
                //TODO:jens -> get_link
                $url = $this->parent->get_url(array(
                        ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_MANAGER_ACTION => MediamosaExternalRepositoryManager :: ACTION_VIEW_EXTERNAL_REPOSITORY,
                        ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY_ID => $object->get_id(), MediamosaExternalRepositoryManager :: PARAM_MEDIAFILE => $mediafile->get_id()));

                $download = null;
                if ($mediafile->get_is_downloadable())
                {
                    $download = 'download';
                }

                $properties[Translation :: get('Version') . $i] = $mediafile->get_title();

                $i ++;
            }

            $properties[Translation :: get('PublishedBy')] = $object->get_publisher();

            if ($object->get_creator())
            {
                $properties[Translation :: get('CreatedBy')] = $object->get_creator();
            }
        }

        return $properties;
    }

    function get_preview($is_thumbnail = false)
    {
        $connector = MediamosaExternalRepositoryConnector :: get_instance($this);

        $object = $this->get_object();

        if ($object->get_status() == MediamosaExternalRepositoryObject :: STATUS_AVAILABLE)
        {
            //see which mediafile to play
            if (Request :: get(MediamosaExternalRepositoryManager :: PARAM_MEDIAFILE))
            {
                $mediafile_id = Request :: get(MediamosaExternalRepositoryManager :: PARAM_MEDIAFILE);
            }
            else
            {
                $mediafile_id = $object->get_default_mediafile();
            }

            if ($mediafile_id)
            {
                //get player
                $output = $connector->mediamosa_play_proxy_request($object->get_id(), $mediafile_id);
            }
            else
            {
                $output = '';
            }
        }
        else
        {
            $output = Translation :: get('NotAvailable');
        }
        return $output;
    }
}
?>
