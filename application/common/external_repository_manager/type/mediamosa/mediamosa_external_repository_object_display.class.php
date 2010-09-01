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
        $object = $this->get_object();

        $properties = array();
        $properties[Translation :: get('Title')] = $object->get_title();

        if ($object->get_description())
        {
            $properties[Translation :: get('Description')] = $object->get_description();
        }

        $properties[Translation :: get('UploadedOn')] = DatetimeUtilities :: format_locale_date(null, $object->get_created());
        if ($object->get_created() != $object->get_modified())
        {
            $properties[Translation :: get('ModifiedOn')] = DatetimeUtilities :: format_locale_date(null, $object->get_modified());
        }

        //get different mediafiles (+status)
        $object = $this->get_object();
        $mediafiles = $object->get_mediafiles();

        $html = array();
        $i = 1;

        if (is_array($mediafiles))
        {
            foreach ($mediafiles as $mediafile)
            {
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
        $external_repository_instance = RepositoryDataManager :: get_instance()->retrieve_external_repository(Request :: get(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY));
        $connector = MediamosaExternalRepositoryConnector :: get_instance($external_repository_instance);

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
