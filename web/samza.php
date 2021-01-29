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

// Lists all YARN jobs (i.e., Samza jobs) which are deployed on YARN

	include('head.php');
	include('helper/RestResult.php');
	include('helper/SortFunctions.php');
	include('helper/YarnResourceManager.php');
?>
<div class="page-header">
	<h1>Samza</h1>
</div>

<p>
	Autorefresh <input type="checkbox" id="autorefresh-checkbox" aria-label="Autorefresh checkbox" />
</p>

<?php
	$url = "http://" . YarnResourceManager::getIp() . ":8088/ws/v1/cluster/apps";
	$restResult = RestResult::performRestRequest($url);

	switch ($restResult->getStatus()) {
		case 200: // OK
			$appObj = $restResult->getJsonResult();
			if ($appObj->{'apps'} == null) {
				echo '<div class="alert alert-warning" role="alert">There are no applications deployed on YARN yet.</div>';
			} else {
				$appArray = $appObj->{'apps'}->{'app'};
				sortAlphanumericallyByAttribute($appArray, 'startedTime', false);
				?>
				<script type="text/javascript">
					/**
					 * Kills an app.
					 * @param id App identifier
					 */
					function killApp(id) {
						b = confirm("Are you sure that you want to kill " + id + "?");
						if (b == true) {
							$.get('./helper/killApp.php?id=' + id, function (data) {
								setTimeout(function () {
									location.reload();
								}, 1000);
							});
						}
					}

					/**
					 * Kills all apps.
					 */
					function killAllApps() {
						b = confirm("Are you sure that you want to kill all running applications?");
						if (b == true) {
							<?php
							foreach ($appArray as $app) {
								if ($app->{'state'} == "RUNNING" && $app->{'applicationType'} == "Samza") {
									$id = $app->{'id'};
									echo "$.get('./helper/killApp.php?id=" . $id . "');\n";
								}
							}
							?>
							setTimeout(function () {
								location.reload();
							}, 1000);
						}
					}
				</script>

				<p>List of all Samza jobs deployed on YARN.</p>

				<div class="col-md-12">
					<table class="table table-hover">
						<thead>
						<tr>
							<th>Name</th>
							<th>Id</th>
							<th>YARN Web UI</th>
							<th>Application Master UI</th>
							<th>Started</th>
							<th>State</th>
							<th>Kill</th>
						</thead>
						<tbody>
						<?php
							foreach ($appArray as $app) {
								if ($app->{'applicationType'} == "Samza") {
									$name = $app->{'name'};
									$id = $app->{'id'};
									$state = $app->{'state'};
									$date = date('D, d M Y H:i:s e', $app->{'startedTime'} / 1000);
									echo '<tr';
									switch ($state) {
										case "RUNNING":
											echo ' class="bg-success"';
											break;
										case "FAILED":
											echo ' class="bg-danger"';
											break;
										case "NEW":
											echo ' class="bg-warning"';
											break;
										case "ACCEPTED":
											echo ' class="bg-warning"';
											break;
									}
									echo '><td>' . $name . '</td><td>' . $id . '</td><td class="bg-active"><a href="http://' . YarnResourceManager::getIp() . ':8088/cluster/app/' . $id . '">YARN WEB UI</a></td><td class="bg-active"><a href="http://' . YarnResourceManager::getIp() . ':8088/proxy/' . $id . '/#application-master">Application Master UI</a></td><td>' . $date . '</td><td>' . $state . '</td><td><button onClick="killApp(\'' . $id . '\')">Kill</button></td></tr>';
								}
							}
						?>
						</tbody>
					</table>
				</div>
				<button onClick="killAllApps()">Kill all running applications</button>

				<?php
			}
			break;
		case 0:
			echo '<div class="alert alert-danger" role="alert">Cannot reach YARN REST API. Check if YARN runs properly.</div>';
			break;
		default:
			echo '<div class="alert alert-danger" role="alert">Error ' . $statusCode . '</div>';
			break;
	}
?>

<p>REST API URL: <a href="<?php echo $url; ?>"><?php echo $url; ?></a></p>

<?php
	include('foot.php');
?>
