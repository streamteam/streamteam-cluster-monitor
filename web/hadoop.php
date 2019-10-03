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

// List to all YARN web interfaces and to all HDFS web interfaces

	include('head.php');
	include('helper/RestResult.php');
	include('helper/SortFunctions.php');
	include('helper/YarnResourceManager.php');
?>

<div class="page-header">
	<h1>Hadoop</h1>
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

			<h2>YARN</h2>
			<p>Links to all YARN web interfaces:</p>
			<ul>

				<?php
					echo '<li><a href="http://' . YarnResourceManager::getIp() . ':8088">' . YarnResourceManager::getHostname() . ' (Resource Manager)</a></li>';

					foreach ($nodeArray as $node) {
						$hostname = $node->{'nodeHostName'};
						$ip = gethostbyname($hostname);
						echo '<li><a href="http://' . $ip . ':8042">' . $hostname . ' (Node Manager)</a></li>';
					}
				?>

			</ul>

			<h2>HDFS</h2>
			<p>Links to all HDFS web interfaces:</p>
			<ul>

				<?php
					echo '<li><a href="http://' . YarnResourceManager::getIp() . ':50070">' . YarnResourceManager::getHostname() . ' (Namenode)</a></li>';
					echo '<li><a href="http://' . YarnResourceManager::getIp() . ':50090">' . YarnResourceManager::getHostname() . ' (Secondary Namenode)</a></li>';

					foreach ($nodeArray as $node) {
						$hostname = $node->{'nodeHostName'};
						$ip = gethostbyname($hostname);
						echo '<li><a href="http://' . $ip . ':50075">' . $hostname . ' (Datanode)</a></li>';
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
