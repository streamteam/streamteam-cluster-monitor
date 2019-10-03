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
	 * Sorts an array alphanumerically by an attribute.
	 * @param $arrayToSort Array to sort
	 * @param $attributeName Attribute name
	 * @param bool $ascending Ascending (true) or descending (false)
	 */
	function sortAlphanumericallyByAttribute(&$arrayToSort, $attributeName, $ascending = true)
	{
		// https://stackoverflow.com/questions/8230538/pass-extra-parameters-to-usort-callback
		usort($arrayToSort, function ($a, $b) use ($attributeName, $ascending) {
			// https://stackoverflow.com/questions/1595423/how-to-compare-2-strings-alphabetically
			$result = strcmp($a->{$attributeName}, $b->{$attributeName});
			if ($ascending == false) {
				$result = -$result;
			}
			return $result;
		});
	}

	/**
	 * Sorts an array alphanumerically.
	 * @param $arrayToSort Array to sort
	 * @param bool $ascending Ascending (true) or descending (false)
	 */
	function sortAlphanumerically(&$arrayToSort, $ascending = true)
	{
		usort($arrayToSort, function ($a, $b) use ($ascending) {
			// https://stackoverflow.com/questions/1595423/how-to-compare-2-strings-alphabetically
			$result = strcmp($a, $b);
			if ($ascending == false) {
				$result = -$result;
			}
			return $result;
		});
	}

?>