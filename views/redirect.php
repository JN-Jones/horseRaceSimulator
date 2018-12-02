<?php
/** @var string $module The module to redirect to */

// Include the header but add an automatic refresh as redirect
ViewManager::getView('header', [
	'additionalHeaders' => '<meta http-equiv="refresh" content="30; URL=./index.php?module=' . $module . '">'
]);

// Add some basic text which includes a link in case the automatic redirect is blocked
echo <<<EOL
<p>Done. If the redirect doesn't work click <a href="index.php?module={$module}">here</a>.</p>
EOL;

// And close everything with our footer
ViewManager::getView('footer');