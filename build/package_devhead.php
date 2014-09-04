<?php
/*
 * Build a "production" package from the current development HEAD, this should be run after a 'composer install'
 */

// Step 1 - Remove previous packages
echo "Preparing environment\n";
umask(022);
chdir(__DIR__);
system('rm -rf packaging');
@unlink(__DIR__ . '/packages/cobalt-head.zip');

// Step 2 - Provision packaging space
mkdir(__DIR__ . '/packaging');

// Step 3 - Copy files to packaging space
echo "Copying files\n";
system('cp -r ../install packaging/');
system('cp -r ../language packaging/');
system('cp -r ../libraries packaging/');
system('cp -r ../plugins packaging/');
system('cp -r ../src packaging/');
system('cp -r ../themes packaging/');
system('cp -r ../uploads packaging/');
system('cp -r ../vendor packaging/');
system('cp ../.htaccess packaging/');
system('cp ../index.php packaging/');

// Step 4 - Remove stuff that shouldn't be distro'ed
echo "Removing extra files\n";
chdir(__DIR__ . '/packaging');

// filp/whoops
system('rm -rf vendor/filp/whoops/docs');
system('rm -rf vendor/filp/whoops/examples');
system('rm -rf vendor/filp/whoops/tests');
system('rm vendor/filp/whoops/.gitignore');
system('rm vendor/filp/whoops/.scrutinizer.yml');
system('rm vendor/filp/whoops/.travis.yml');
system('rm vendor/filp/whoops/composer.json');
system('rm vendor/filp/whoops/CONTRIBUTING.md');
system('rm vendor/filp/whoops/phpunit.xml.dist');
system('rm vendor/filp/whoops/README.md');

// joomla/framework
system('rm -rf vendor/joomla/framework/build');
system('rm -rf vendor/joomla/framework/docs');
system('rm -rf vendor/joomla/framework/tests');
system('rm vendor/joomla/framework/.gitattributes');
system('rm vendor/joomla/framework/.gitignore');
system('rm vendor/joomla/framework/.gitmodules');
system('rm vendor/joomla/framework/.travis.yml');
system('rm vendor/joomla/framework/build.xml');
system('rm vendor/joomla/framework/composer.json');
system('rm vendor/joomla/framework/CONTRIBUTING.markdown');
system('rm vendor/joomla/framework/phpunit.*');
system('rm vendor/joomla/framework/README.markdown');

// league/di
system('rm -rf vendor/league/di/test');
system('rm vendor/league/di/.gitignore');
system('rm vendor/league/di/.travis.yml');
system('rm vendor/league/di/composer.json');
system('rm vendor/league/di/CONTRIBUTING.md');
system('rm vendor/league/di/phpunit.xml.dist');
system('rm vendor/league/di/README.md');

// psr/log
system('rm -rf vendor/psr/log/Psr/Log/Test');
system('rm vendor/psr/log/.gitignore');
system('rm vendor/psr/log/composer.json');
system('rm vendor/psr/log/README.md');

// symfony/http-foundation
system('rm -rf vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/Tests');
system('rm vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/.gitignore');
system('rm vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/CHANGELOG.md');
system('rm vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/composer.json');
system('rm vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/phpunit.xml.dist');
system('rm vendor/symfony/http-foundation/Symfony/Component/HttpFoundation/README.md');

// tracy/tracy
system('rm -rf vendor/tracy/tracy/examples');
system('rm -rf vendor/tracy/tracy/tools');
system('rm vendor/tracy/tracy/composer.json');
system('rm vendor/tracy/tracy/readme.md');

// Step 5 - ZIP it up
echo "Packaging Cobalt\n";
system('find . -type d -name .git -exec rm -rf {} \\; > /dev/null');
system('zip -r ../packages/cobalt-head.zip install/ language/ libraries/ plugins/ src/ themes/ uploads/vendor/ .htaccess index.php > /dev/null');
