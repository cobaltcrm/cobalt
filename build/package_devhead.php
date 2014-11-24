<?php
/*
 * Build a "production" package from the current development HEAD, this should be run after a 'composer install'
 */

// Preparation - Remove previous packages
echo "Preparing environment\n";
umask(022);
chdir(__DIR__);
system('rm -rf packaging');
@unlink(__DIR__ . '/packages/cobalt-head.zip');

// Preparation - Provision packaging space
mkdir(__DIR__ . '/packaging');

// Common steps
include_once __DIR__ . '/processfiles.php';

// Post-processing - ZIP it up
echo "Packaging Cobalt\n";
system('find . -type d -name .git -exec rm -rf {} \\; > /dev/null');
system('zip -r ../packages/cobalt-head.zip language/ libraries/ media/ src/ themes/ uploads/ vendor/ .htaccess index.php > /dev/null');
