<!DOCTYPE html>
<!--
  ~ StreamTeam
  ~ Copyright (C) 2019  University of Basel
  ~
  ~ This program is free software: you can redistribute it and/or modify
  ~ it under the terms of the GNU Affero General Public License as
  ~ published by the Free Software Foundation, either version 3 of the
  ~ License, or (at your option) any later version.
  ~
  ~ This program is distributed in the hope that it will be useful,
  ~ but WITHOUT ANY WARRANTY; without even the implied warranty of
  ~ MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  ~ GNU Affero General Public License for more details.
  ~
  ~ You should have received a copy of the GNU Affero General Public License
  ~ along with this program.  If not, see <https://www.gnu.org/licenses/>.
  -->

<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<meta name="description" content="">
	<meta name="author" content="">

	<link rel="apple-touch-icon" sizes="180x180" href="./favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" href="./favicons/favicon-32x32.png" sizes="32x32">
	<link rel="icon" type="image/png" href="./favicons/favicon-16x16.png" sizes="16x16">
	<link rel="manifest" href="./favicons/manifest.json">
	<link rel="mask-icon" href="./favicons/safari-pinned-tab.svg" color="#00a300">
	<link rel="shortcut icon" href="./favicons/favicon.ico">
	<meta name="msapplication-config" content="./favicons/browserconfig.xml">
	<meta name="theme-color" content="#00a300">

	<title>StreamTeam Cluster Monitor</title>

	<!-- Bootstrap core CSS -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Bootstrap theme -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" rel="stylesheet">

	<!-- Custom styles for this website -->
	<link href="css/main.css" rel="stylesheet">

	<script src="https://cdn.jsdelivr.net/npm/jquery@3.1.1/dist/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/protobufjs@6.8.8/dist/protobuf.min.js"></script>

	<!-- StreamTeam Data Model for Kafka page -->
	<script src="../streamteam-data-model/streamteam-data-model-lib-1.0.1.js"></script>
</head>

<body>

<!-- Fixed navbar -->
<?php
	$page = preg_replace("/\/streamteam-cluster-monitor\//", "", $_SERVER['REQUEST_URI']);
	if ($page == '') {
		$page = 'index.php';
	}
?>


<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
					aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="http://dbis.dmi.unibas.ch">
				<img alt="DBIS" src="dbis.png">
			</a>
			<a class="navbar-brand" href="http://dbis.dmi.unibas.ch/research/projects/streamTeam/">
				<img alt="StreamTeam" src="streamTeam.png">
			</a>
			<a class="navbar-brand hidden-xs" href="./index.php">
				StreamTeam Cluster Monitor
			</a>
			<a class="navbar-brand hidden-sm hidden-md hidden-lg" href="./index.php">
				STCM
			</a>
		</div>
		<div id="navbar" class="navbar-collapse collapse">
			<ul class="nav navbar-nav">
				<li<?php echo $page == 'index.php' ? ' class="active"' : '' ?>><a href="index.php">Home</a></li>
				<li<?php echo $page == 'netdata.php' ? ' class="active"' : '' ?>><a href="netdata.php">Netdata</a></li>
				<li<?php echo $page == 'kafka.php' ? ' class="active"' : '' ?>><a href="kafka.php">Kafka</a></li>
				<li<?php echo $page == 'samza.php' ? ' class="active"' : '' ?>><a href="samza.php">Samza</a></li>
				<li<?php echo $page == 'hadoop.php' ? ' class="active"' : '' ?>><a href="hadoop.php">Hadoop</a></li>
				<li<?php echo $page == 'logs.php' ? ' class="active"' : '' ?>><a href="logs.php">Logs</a></li>
			</ul>
		</div>
	</div>
</nav>

<div class="container-fluid" role="main">