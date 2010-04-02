<?php

/*
 * This file is part of Ovis.
 * 
 * Ovis is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * Ovis is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * Ovis. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Exposes relevant methods via the Web service.
 * 
 * @author Tim De Pauw
 * @package ovis
 */
class StreamingVideoWebServiceFacade {
	var $service;

	function registerUpload($username, $password, $filename, $segments) {
		return $this->service->registerUpload(
			$username, $password, $filename, $segments);
	}

	function version() {
		return $this->service->version();
	}
}

?>