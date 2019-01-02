<?php
// View to simply display a error message
/** @var string $msg The error message to display */

\Managers\ViewManager::getView('header');

echo <<<EOL
There was an error with your request: {$msg}<br/>
Please return to the <a href="index.php">index page</a>.
EOL;


\Managers\ViewManager::getView('footer');