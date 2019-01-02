<?php
// View to display the fastest horse
/** @var \Models\Horse $horse The fastest horse */

echo <<<EOL
Fastest horse ever was horse #{$horse->getId()} in race #{$horse->getRaceId()} with a time of {$horse->getTimeNeeded()} seconds.<br />
It's stats were: {$horse->getSpeed()} for speed, {$horse->getStrength()} for strength and {$horse->getEndurance()} for endurance.
EOL;
