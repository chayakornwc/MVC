<?php
// Namespace configuration
use Core;

// Global configurations
include dirname(__FILE__) . '/../Library/global.php';

// Start the router
new Core\Front('MyProject');