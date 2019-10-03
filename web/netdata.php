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

// List Netdata boxes for YARN nodes

	include("head.php");
	include('helper/RestResult.php');
	include('helper/SortFunctions.php');
	include('helper/YarnResourceManager.php');
?>
<div class="page-header">
	<h1>Netdata</h1>
</div>

<?php
	$url = "http://" . YarnResourceManager::getIp() . ":8088/ws/v1/cluster/nodes";
	$restResult = RestResult::performRestRequest($url);

	switch ($restResult->getStatus()) {
		case 200: // OK
			?>

			<div class="row" id="netdata-panel-row">
				<!-- Automatically filled with Javascript -->
			</div>

			<script>
				var netdataDontStart = true;
				var netdataNoBootstrap = true;
				var netdataTheme = "slate";

				$(document).ready(function () {
					setTimeout(function() {
						<?php
						$nodeObj = $restResult->getJsonResult();
						$nodeArray = $nodeObj->{'nodes'}->{'node'};
						sortAlphanumericallyByAttribute($nodeArray, 'nodeHostName');

						$jsonString = '[';
						$jsonString .= '{"ip": "' . YarnResourceManager::getIP() . '", "hostname": "' . YarnResourceManager::getHostname() . '", "type": "Resource Manager"}';

						foreach ($nodeArray as $node) {
							$hostname = $node->{'nodeHostName'};
							$ip = gethostbyname($hostname);
							$jsonString .= ',{"ip": "' . $ip . '", "hostname": "' . $hostname . '", "type": "Node Manager"}';
						}
						$jsonString .= ']';
						echo "var nodes = " . $jsonString . ";";
						?>
						jQuery.each(nodes, function (i, node) {
							var address = 'http://' + node.ip + ':19999/';

							var netdataPanelStart = '<div class="col-md-3 col-sm-6"><a href="' + address + '"><div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">' + node.hostname + ' (' + node.type + ')</h3></div><div class="panel-body panel-body-netdata"><div class="row">';
							var netdataPanelEnd = '</div></div></div></a></div>';

							var netdataDivStart = '<div class="col-xs-4"><div data-host="' + address + '" data-chart-library="easypiechart" data-width="100%" data-after="-600" data-points="600" ';
							var netdataDivEnd = ' ></div></div>';


							var cpu = 'data-easypiechart-max-value="100" data-append-options="percentage" data-netdata="system.cpu" data-dimensions="user" data-title="Used CPU" data-units="%" data-colors="#22AA99"';
							var ram = 'data-easypiechart-max-value="100" data-append-options="percentage" data-netdata="system.ram" data-dimensions="used|buffers|active|wired" data-title="Used RAM" data-units="%" data-colors="#EE9911"';
							var diskRead = 'data-netdata="system.io" data-dimensions="in" data-title="Disk Read" data-units="kbyte/s" data-colors="#66AA00"';
							var diskWrite = 'data-netdata="system.io" data-dimensions="out" data-title="Disk Write" data-units="kbyte/s" data-colors="#FE3912"';
							var networkIn = 'data-netdata="system.net" data-dimensions="received" data-title="Network Received" data-units="kbit/s" data-colors="#66AA00"';
							var networkOut = 'data-netdata="system.net" data-dimensions="sent" data-title="Network Sent" data-units="kbit/s" data-colors="#FE3912"';

							var content = netdataPanelStart
								+ netdataDivStart + cpu + netdataDivEnd
								+ netdataDivStart + networkIn + netdataDivEnd
								+ netdataDivStart + networkOut + netdataDivEnd
								+ netdataDivStart + ram + netdataDivEnd
								+ netdataDivStart + diskRead + netdataDivEnd
								+ netdataDivStart + diskWrite + netdataDivEnd
								+ netdataPanelEnd;

							$("#netdata-panel-row").append(content);

						});
						NETDATA.start();
					}, 100);
				});
			</script>
			<script type="text/javascript"
					src="http://<?php echo YarnResourceManager::getIP(); ?>:19999/dashboard.js"></script>
			<?php
			break;
		case 0:
			echo '<div class="alert alert-danger" role="alert">Cannot reach YARN REST API. Check if YARN runs properly.</div>';
			break;
		default:
			echo '<div class="alert alert-danger" role="alert">Error ' . $restResult->getStatus() . '</div>';
			break;
	}

	include("foot.php");
?>
