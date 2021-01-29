<?php
/**
 * StreamTeam
 * Copyright (C) 2019  University of Basel
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

if (isset($_GET['id'])) {
		$yarnResourceManagerIp = $_SERVER['SERVER_ADDR'];
		if($yarnResourceManagerIp == "::1") {
				$yarnResourceManagerIp = "127.0.0.1";
			}
		$appId = $_GET['id'];

		// https://stackoverflow.com/questions/5043525/php-curl-http-put
		// http://hadoop-common.472056.n3.nabble.com/Yarn-REST-API-cannot-kill-an-application-td4100621.html
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, "http://" . $yarnResourceManagerIp . ":8088/ws/v1/cluster/apps/" . $appId . "/state");
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_POSTFIELDS, '{"state":"KILLED"}');
		$response = curl_exec($curl);
	}

?>
