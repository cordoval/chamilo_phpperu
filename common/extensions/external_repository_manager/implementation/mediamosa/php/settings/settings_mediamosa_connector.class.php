<?php
namespace common\extensions\external_repository_manager\implementation\mediamosa;
class ExternalRepositorySettingsMediamosaConnector
{
    public static function get_versions()
    {
        $versions =  array();
        $versions['1.7.4'] = '1.7.4';
        return $versions;
    }
}
?>