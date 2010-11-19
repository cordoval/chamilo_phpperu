<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\InCondition;
use common\libraries\AndCondition;
use common\libraries\Authentication;

/**
 * $Id: title_exists.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.ajax
 */
require_once dirname(__FILE__) . '/../../../common/global.inc.php';

if (Authentication :: is_valid())
{
    $title = Request :: post('title');

    $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TITLE, $title);
    $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, Session :: get_user_id());
    $conditions[] = new InCondition(ContentObject :: PROPERTY_TYPE, RepositoryDataManager :: get_registered_types());
    $condition = new AndCondition($conditions);

    $count = RepositoryDataManager :: get_instance()->count_content_objects($condition);
    if ($count > 0)
    {
        echo '<div class="warning-message">' . Translation :: get('TitleExists') . '</div>';
    }
}

?>