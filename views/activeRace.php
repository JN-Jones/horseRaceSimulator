<?php
// View to display a single, currently active race

// Get all horses for this race
/** @var Race $race */
$horses = $race->getSortedHorses();

// Display the amount of seconds this race has run for and the covered distance of the first horse
echo <<<EOL
<h3>Race #{$race->getId()}</h3>
Race has run for {$race->getTimeRun()} seconds, the first horse has covered {$horses[0]->getCoveredDistance($race->getTimeRun())} meters.
<ul>
EOL;

// Display each horse in a numbered list
$rank = 1;
foreach ($horses as $horse)
{
	// Display the covered distance
	echo "<li>#{$rank}: Horse #{$horse->getId()} ({$horse->getCoveredDistance($race->getTimeRun())}m";
	// And in case the horse has finished the race already also display the time it needed to do so
	if($horse->getCoveredDistance($race->getTimeRun()) >= ConfigManager::getInstance()->getRaceDistance())
	{
		echo " - " . $horse->getTimeNeeded() . "s";
	}
	echo ")</li>";

	$rank++;
}
echo "</ul>";