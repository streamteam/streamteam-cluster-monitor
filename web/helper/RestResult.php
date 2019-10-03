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

/**
 * REST result
 */
class RestResult
	{
		/**
		 * @var REST result
		 */
		private $result;

		/**
		 * @var REST status code
		 */
		private $statusCode;

		/**
		 * RestResult constructor.
		 * @param $result REST result
		 * @param $statusCode REST status code
		 */
		private function __construct($result, $statusCode)
		{
			$this->result = $result;
			$this->statusCode = $statusCode;
		}

		/**
		 * Performs a REST request
		 * @param $url Url
		 * @return RestResult REST result
		 */
		public static function performRestRequest($url)
		{
			// http://codular.com/curl-with-php
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($curl);
			$statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			curl_close($curl);
			return new RestResult($result, $statusCode);
		}

		/**
		 * Gets the REST result.
		 * @return REST result
		 */
		public function getResult()
		{
			return $this->result;
		}

		/**
		 * Gets the REST status code.
		 * @return REST status code
		 */
		public function getStatus()
		{
			return $this->statusCode;
		}

		/**
		 * Gets the REST result as JSON object.
		 * @return REST result as JSON object
		 */
		public function getJsonResult()
		{
			// http://php.net/manual/de/function.json-decode.php
			return json_decode($this->getResult());
		}
	}

?>