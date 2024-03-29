<?php
namespace repository;

use reporting\ReportingManager;

use reporting\ReportingChartFormatter;

use reporting\ReportingFormatter;

use reporting\ReportingData;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

require_once dirname(__FILE__) . '/../repository_reporting_block.class.php';

class UserRepositoryReportingBlock extends RepositoryReportingBlock
{
    private $user_id;

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function count_data()
    {
        return 0;
    }

    public function retrieve_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('Count', null, Utilities :: COMMON_LIBRARIES)));

        $rdm = RepositoryDataManager :: get_instance();
        $registered_types = RepositoryDataManager :: get_registered_types();

        foreach ($registered_types as $key => $registered_type)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $registered_type);

            $user_id = $this->get_user_id();
            if ($user_id)
            {
                $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
            }

            $condition = new AndCondition($conditions);
            $count = $rdm->count_content_objects($condition);

            if ($count > 0)
            {
                $category_name = Translation :: get('TypeName', null, ContentObject :: get_content_object_type_namespace($registered_type));

                $reporting_data->add_category($category_name);
                $reporting_data->add_data_category_row($category_name, Translation :: get('Count', null, Utilities :: COMMON_LIBRARIES), $count);
            }
        }

        return $reporting_data;
    }

    function get_application()
    {
        return RepositoryManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();

        $modes[ReportingFormatter :: DISPLAY_TEXT] = Translation :: get('Text', null, ReportingManager :: APPLICATION_NAME);
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table', null, ReportingManager :: APPLICATION_NAME);
        $modes[ReportingChartFormatter :: DISPLAY_BAR] = Translation :: get('Chart:Bar', null, ReportingManager :: APPLICATION_NAME);

        return $modes;
    }
}
?>