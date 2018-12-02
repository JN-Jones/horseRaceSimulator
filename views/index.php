<?php
// View to display the main index page including all elements

// Include the header. The header could also include a refresh to automatically update the page to make sure it shows the current results
// An AJAX solution would also be possible but it would need to update nearly every part of the page
ViewManager::getView('header', [
//	'additionalHeaders' => '<meta http-equiv="refresh" content="60; URL=./index.php">'
]);

// First display the links to handle user requests (could be styled via js to look like buttons or a form with actual buttons could be used)
echo <<<EOL
<a href="index.php?module=createRace">Create new race</a> &bullet; <a href="index.php?module=advanceRaces">Advance races by 10 seconds</a> &bullet; <a href="index.php">Refresh</a><br />
EOL;

// First display active races if we have some
echo "<div>";
if(empty($activeRaces))
{
	echo "There's currently no active race, feel free to create one.";
}
else
{
	foreach($activeRaces as $activeRace)
	{
		ViewManager::getView('activeRace', ['race' => $activeRace]);
	}
}
echo "</div>";

// Afterwards display last races if we have some
echo "<div>";
if(empty($lastRaces))
{
	echo "It seems like there are no finished races to display.";
}
else
{
	foreach($lastRaces as $finishedRace)
	{
		ViewManager::getView('finishedRace', ['race' => $finishedRace]);
	}
}
echo "</div>";

// And at the bottom display the fastest horse if we have one
echo "<div>";
if(empty($fastestHorse))
{
	echo "No horse has finished a race so far.";
}
else
{
	ViewManager::getView('fastestHorse', ['horse' => $fastestHorse]);
}
echo "</div>";

// Finish everything with the footer
ViewManager::getView('footer');