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

// Page which lists all Kafka topics, all keys for a given topic, and the latest data stream elements for a given topic and key

	include("head.php");
	include('helper/RestResult.php');
	include('helper/SortFunctions.php');
	include('helper/YarnResourceManager.php');

	$kafkaRestProxy = "http://" . YarnResourceManager::getIp() . ":5555";
	$limit = 200;

	if (isset($_GET['t'])) {
		$topic = $_GET['t'];
		if (isset($_GET['k'])) {
			$key = $_GET['k'];
			$url = $kafkaRestProxy . "/consume?t=" . $topic . "&k=" . $key . "&l=" . $limit;
			$mode = 2;
		} else {
			$url = $kafkaRestProxy . "/listKeys?t=" . $topic;
			$mode = 1;
		}
	} else {
		$url = $kafkaRestProxy . "/listTopics";
		$mode = 0;
	}

?>
<div class="page-header">
	<h1>Kafka</h1>
</div>

<div id="kafka-navi">
	<ol class="breadcrumb">
		<?php
			switch ($mode) {
				case 0:
					echo '<li class="active">All topics</li>';
					break;
				case 1:
					echo '<li><a href="./kafka.php">All topics</a></li>';
					echo '<li class="active">' . $topic . '</li>';
					break;
				case 2:
					echo '<li><a href="./kafka.php">All topics</a></li>';
					echo '<li><a href="./kafka.php?t=' . $topic . '">' . $topic . '</a></li>';
					echo '<li class="active">' . $key . '</li>';
					break;
			}
		?>
	</ol>
</div>

<p>
	Autorefresh <input type="checkbox" id="autorefresh-checkbox" aria-label="Autorefresh checkbox" />
</p>

<?php
	$restResult = RestResult::performRestRequest($url);

	switch ($restResult->getStatus()) {
		case 200: // OK
			$obj = $restResult->getJsonResult();

			switch ($mode) {
				case 0:
					$topics = $obj->{'t'};
					natsort($topics);
					echo '<p>List of all topics for which data stream elements are buffered in the KafkaRestProxy:</p><ul>';
					foreach ($topics as $topic) {
						echo '<li><a href="./kafka.php?t=' . $topic . '">' . $topic . '</a></li>';
					}
					echo '</ul>';
					break;
				case 1:
					$keys = $obj->{'k'};
					natsort($keys);
					echo '<p>List of all keys for which data stream elements of topic ' . $topic . ' are buffered in the KafkaRestProxy:</p><ul>';
					foreach ($keys as $key) {
						echo '<li><a href="./kafka.php?t=' . $topic . '&k=' . $key . '">' . $key . '</a></li>';
					}
					if (count($keys) > 0) {
						echo '<li><a href="./kafka.php?t=' . $topic . '&k=_ALL">All</a></li>'; // _ALL is the dedicated all-key of the Kafka REST Proxy
					}
					echo '</ul>';
					break;
				case 2:
					$dataStreamElements = $obj->{'d'};
					if ($key == "_ALL") { // _ALL is the dedicated all-key of the Kafka REST Proxy
						echo '<p>List of the latest ' . $limit . ' data stream elements buffered in the KafkaRestProxy for topic ' . $topic . ' and alls keys:</p>';
					} else {
						echo '<p>List of the latest ' . $limit . ' data stream elements buffered in the KafkaRestProxy for topic ' . $topic . ' and key ' . $key . ':</p>';
					}
					echo '<div class="col-md-12"><table class="table table-striped">';
					echo '<thead><tr><th>#</th><th>Key</th><th>Partition</th><th>Offset</th><th>Value</th></thead><tbody>';
					$i = 1;
					foreach ($dataStreamElements as $dataStreamElement) {
						// The base64 encoded Protobuf message is encoded with Javascript using our StreamTeam Data Model library
						// http://ecmanaut.blogspot.com/2006/07/encoding-decoding-utf8-in-javascript.html
						echo '<tr><td>' . $i . '</td><td>' . $dataStreamElement->{'k'} . '</td><td>' . $dataStreamElement->{'p'} . '</td><td>' . $dataStreamElement->{'o'} . '</td><td id="kafka-line'. $i . '"><script> setTimeout(function() {document.getElementById("kafka-line'. $i .'").innerHTML = decodeURIComponent(escape(JSON.stringify(decodeBase64EncodedImmutableDataStreamElement(\'' . $dataStreamElement->{'v'} . '\'))));}, 1000); </script></td></tr>';
						$i++;
					}
					echo '</tbody></table></div>';
					break;
			}
			break;
		case 204: // NO CONTENT
			switch ($mode) {
				case 0:
					$noDataMsg = "There are no topics for which data stream elements are buffered in the KafkaRestProxy.";
					break;
				case 1:
					$noDataMsg = "There are no keys for which data stream elements of topic " . $topic . " are buffered in the KafkaRestProxy.";
					break;
				case 2:
					$noDataMsg = "There are no data stream elements buffered in the KafkaRestProxy for topic " . $topic . " and key " . $key . ".";
					break;
			}
			echo '<div class="alert alert-warning" role="alert">' . $noDataMsg . '</div>';
			break;
		case 0:
			echo '<div class="alert alert-danger" role="alert">Cannot reach KafkaRestProxy. Check if KafkaRestProxy runs properly.</div>';
			break;
		default:
			echo '<div class="alert alert-danger" role="alert">Error ' . $restResult->getStatus() . '</div>';
			break;
	}
?>

<p>REST API URL: <a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>

<?php
	include("foot.php");
?>
