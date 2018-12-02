<?php
// View to display the fastest horse
/** @var Horse $horse The fastest horse */

echo <<<EOL
Fastest horse ever was horse #{$horse->getId()} in race #{$horse->getRaceId()}<br />
It's stats were: {$horse->getSpeed()} for speed, {$horse->getStrength()} for strength and {$horse->getEndurance()} for endurance.
EOL;
