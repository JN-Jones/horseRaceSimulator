<?php
// View to display a single, already finished race
/** @var \Models\Race $race The race to display */

$horses = $race->getSortedHorses();

// Display the actual time this race finished and the first three horses including their time
echo <<<EOL
<h3>Race #{$race->getId()}</h3>
Race finished {$race->getTimeFinished()->format("l, d. M. Y H:i:s")}<br />
Top Three:
<ul>
<li>1. Horse #{$horses[0]->getId()} in {$horses[0]->getTimeNeeded()}s</li>
<li>2. Horse #{$horses[1]->getId()} in {$horses[1]->getTimeNeeded()}s</li>
<li>3. Horse #{$horses[2]->getId()} in {$horses[2]->getTimeNeeded()}s</li>
</ul>
EOL;
