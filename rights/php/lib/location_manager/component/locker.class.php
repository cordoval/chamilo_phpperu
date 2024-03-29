<?php
namespace rights;

use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;

/**
 * $Id: locker.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.location_manager.component
 */

class LocationManagerLockerComponent extends LocationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(LocationManager :: PARAM_LOCATION);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $location = $this->retrieve_location($id);
                $location->lock();

                if (! $location->update())
                {
                    $failures ++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLocationNotLocked';
                }
                else
                {
                    $message = 'SelectedLocationsNotLocked';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedLocationLocked';
                }
                else
                {
                    $message = 'SelectedLocationsLocked';
                }
            }

            if ($location->get_parent() == 0)
            {
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                        Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_LOCATIONS,
                        LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_BROWSE_LOCATIONS,
                        LocationManager :: PARAM_SOURCE => $location->get_application()));
            }
            else
            {
                $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                        Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_LOCATIONS,
                        LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_BROWSE_LOCATIONS,
                        LocationManager :: PARAM_SOURCE => $location->get_application(),
                        LocationManager :: PARAM_LOCATION => $location->get_parent()));
            }
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoLocationSelected')));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $ids = Request :: get(RightsTemplateManager :: PARAM_LOCATION);
        $location_id = $ids[0];

        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_LOCATIONS,
                LocationManager :: PARAM_LOCATION_ACTION => LocationManager :: ACTION_BROWSE_LOCATIONS,
                LocationManager :: PARAM_SOURCE => Request :: get(LocationManager :: PARAM_SOURCE),
                LocationManager :: PARAM_LOCATION => $location_id)), Translation :: get('LocationManagerBrowserComponent')));
        $breadcrumbtrail->add_help('rights_locations_locker');
    }

    function get_additional_parameters()
    {
        return array(LocationManager :: PARAM_LOCATION, LocationManager :: PARAM_SOURCE);
    }
}
?>