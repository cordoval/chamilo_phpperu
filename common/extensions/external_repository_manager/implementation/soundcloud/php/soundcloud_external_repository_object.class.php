<?php
namespace common\extensions\external_repository_manager\implementation\soundcloud;

use common\extensions\external_repository_manager\ExternalRepositoryObject;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Theme;
use common\libraries\ToolbarItem;

class SoundcloudExternalRepositoryObject extends ExternalRepositoryObject
{
    const OBJECT_TYPE = 'soundcloud';

    const PROPERTY_ARTWORK = 'artwork';
    const PROPERTY_LICENSE = 'license';

    const PROPERTY_GENRE = 'genre';
    const PROPERTY_WAVEFORM = 'waveform';
    const PROPERTY_TRACK_TYPE = 'track_type';
    const PROPERTY_BPM = 'bpm';
    const PROPERTY_LABEL = 'label';

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_ARTWORK, self :: PROPERTY_GENRE, self :: PROPERTY_WAVEFORM, self :: PROPERTY_TRACK_TYPE, self :: PROPERTY_BPM, self :: PROPERTY_LABEL));
    }

    function get_artwork()
    {
        return $this->get_default_property(self :: PROPERTY_ARTWORK);
    }

    function set_artwork($artwork)
    {
        return $this->set_default_property(self :: PROPERTY_ARTWORK, $artwork);
    }

    function get_license()
    {
        return $this->get_default_property(self :: PROPERTY_LICENSE);
    }

    function get_license_icon()
    {
        $icon = new ToolbarItem($this->get_license(), Theme :: get_image_path() . 'licenses/' . $this->get_license() . '.png', null, ToolbarItem :: DISPLAY_ICON);
        return $icon->as_html();
    }

    function set_license($license)
    {
        return $this->set_default_property(self :: PROPERTY_LICENSE, $license);
    }

    function get_genre()
    {
        return $this->get_default_property(self :: PROPERTY_GENRE);
    }

    function set_genre($genre)
    {
        return $this->set_default_property(self :: PROPERTY_GENRE, $genre);
    }

    function get_waveform()
    {
        return $this->get_default_property(self :: PROPERTY_WAVEFORM);
    }

    function set_waveform($waveform)
    {
        return $this->set_default_property(self :: PROPERTY_WAVEFORM, $waveform);
    }

    function get_track_type()
    {
        return $this->get_default_property(self :: PROPERTY_TRACK_TYPE);
    }

    function set_track_type($track_type)
    {
        return $this->set_default_property(self :: PROPERTY_TRACK_TYPE, $track_type);
    }

    function get_bpm()
    {
        return $this->get_default_property(self :: PROPERTY_BPM);
    }

    function set_bpm($bpm)
    {
        return $this->set_default_property(self :: PROPERTY_BPM, $bpm);
    }

    function get_label()
    {
        return $this->get_default_property(self :: PROPERTY_LABEL);
    }

    function set_label($label)
    {
        return $this->set_default_property(self :: PROPERTY_LABEL, $label);
    }

    static function get_object_type()
    {
        return self :: OBJECT_TYPE;
    }

    static function get_valid_track_types()
    {
        $track_types = array();

        $track_types['original'] = Translation :: get('Original');
        $track_types['remix'] = Translation :: get('Remix');
        $track_types['live'] = Translation :: get('Live');
        $track_types['recording'] = Translation :: get('Recording');
        $track_types['spoken'] = Translation :: get('Spoken');
        $track_types['podcast'] = Translation :: get('Podcast');
        $track_types['demo'] = Translation :: get('Demo');
        $track_types['in progress'] = Translation :: get('WorkInProgress');
        $track_types['stem'] = Translation :: get('Stem');
        $track_types['loop'] = Translation :: get('Loop');
        $track_types['sound effect'] = Translation :: get('SoundEffect');
        $track_types['sample'] = Translation :: get('OneShotSample');
        $track_types['other'] = Translation :: get('Other');

        return $track_types;
    }
}
?>