<?php
/*
 * @author jevdheyd
 */
class StreamingVideoClip extends ContentObject implements Versionable
{
    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>