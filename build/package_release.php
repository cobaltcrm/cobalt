<?php
/*
 * Build a production package from a version tag, this should be run after a 'composer install' and the upcoming release has been tagged
 */

// We need the version number so include the main index.php file
include_once dirname(__DIR__) . '/index.php';
$version = COBALT_VERSION;

// Preparation - Remove previous packages
echo "Preparing environment\n";
umask(022);
chdir(__DIR__);
system('rm -rf packaging');
@unlink(__DIR__ . '/packages/cobalt-' . $version . '.zip');

// Preparation - Provision packaging space
mkdir(__DIR__ . '/packaging');

// Common steps
include_once __DIR__ . '/processfiles.php';

// Uncomment this at 1.0.1
/*// In this step, we'll compile a list of files that may have been deleted so our update script can remove them
ob_start();
passthru('which git', $systemGit);
$systemGit = trim(ob_get_clean());

// First, get a list of git tags
ob_start();
passthru($systemGit . ' tag -l', $tags);
$tags = explode("\n", trim(ob_get_clean()));

// Get the list of modified files from the initial tag
ob_start();
passthru($systemGit . ' diff tags/' . $tags[0] . ' tags/' . $version . ' --name-status', $fileDiff);
$fileDiff = explode("\n", trim(ob_get_clean()));

// Only add deleted files to our list; new and modified files will be covered by the archive
$deletedFiles = array();

foreach ($fileDiff as $file)
{
	if (substr($file, 0, 1) == 'D')
	{
		$deletedFiles[] = substr($file, 2);
	}
}*/

// Post-processing - ZIP it up
echo "Packaging Cobalt\n";
system('find . -type d -name .git -exec rm -rf {} \\; > /dev/null');
system('zip -r ../packages/cobalt-' . $version . '.zip language/ libraries/ media/ src/ themes/ uploads/ vendor/ .htaccess index.php > /dev/null');
