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

// List links to node log pages

	include('head.php');
	include('helper/RestResult.php');
	include('helper/SortFunctions.php');
	include('helper/YarnResourceManager.php');
?>

<div class="page-header">
	<h1>Logs</h1>
</div>

<?php
	$url = "http://" . YarnResourceManager::getIp() . ":8088/ws/v1/cluster/nodes";
	$restResult = RestResult::performRestRequest($url);

	switch ($restResult->getStatus()) {
		case 200: // OK
			$nodeObj = $restResult->getJsonResult();
			$nodeArray = $nodeObj->{'nodes'}->{'node'};
			sortAlphanumericallyByAttribute($nodeArray, 'nodeHostName');
			?>

			<p>The following pages list all YARN, HDFS and Samza logs:</p>
			<ul>

				<?php
					echo '<li><a href="http://' . YarnResourceManager::getIp() . ':8088/logs/">' . YarnResourceManager::getHostname() . ' (Resource Manager)</a></li>';

					foreach ($nodeArray as $node) {
						$hostname = $node->{'nodeHostName'};
						$ip = gethostbyname($hostname);
						echo '<li><a href="http://' . $ip . ':8042/logs/">' . $hostname . ' (Node Manager)</a></li>';
					}
				?>

			</ul>

			<?php
			break;
		case 0:
			echo '<div class="alert alert-danger" role="alert">Cannot reach YARN REST API. Check if YARN runs properly.</div>';
			break;
		default:
			echo '<div class="alert alert-danger" role="alert">Error ' . $restResult->getStatus() . '</div>';
			break;
	}
?>

<p>REST API URL: <a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>

<?php
	include('foot.php');
?>
