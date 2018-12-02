<!-- Basic header view. Could also include some general JS or a menu and logo -->
<html>
<head>
	<title>Horse Race Simulator</title>
	<!-- This view offers the possibility for individual page to add additional headers, eg for a redirect -->
	<?php if(!empty($additionalHeaders)) { echo $additionalHeaders; } ?>
	<!-- Basic style to highlight divs -->
	<style>
		div {
			border: 1px solid black;
			margin-top: 2em;
			padding: 1em;
		}
	</style>
</head>
<body>