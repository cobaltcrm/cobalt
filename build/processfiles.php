<?php
/*
 * Common file for preparing an installation package
 */

// Copy files to packaging space
echo "Copying files\n";
system('cp -r ../installer packaging/');
system('cp -r ../language packaging/');
system('cp -r ../libraries packaging/');
system('cp -r ../media packaging/');
system('cp -r ../src packaging/');
system('cp -r ../themes packaging/');
system('cp -r ../uploads packaging/');
system('cp -r ../vendor packaging/');
system('cp ../.htaccess packaging/');
system('cp ../index.php packaging/');

// emove stuff that shouldn't be distro'ed
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

// joomla/application
system('rm -rf vendor/joomla/application/Tests');
system('rm vendor/joomla/application/.gitattributes');
system('rm vendor/joomla/application/.gitignore');
system('rm vendor/joomla/application/.travis.yml');
system('rm vendor/joomla/application/composer.json');
system('rm vendor/joomla/application/phpunit.*');
system('rm vendor/joomla/application/README.md');

// joomla/authentication
system('rm -rf vendor/joomla/authentication/Docs');
system('rm -rf vendor/joomla/authentication/Tests');
system('rm vendor/joomla/authentication/composer.json');
system('rm vendor/joomla/authentication/phpunit.*');
system('rm vendor/joomla/authentication/README.md');

// joomla/compat
system('rm vendor/joomla/compat/composer.json');
system('rm vendor/joomla/compat/README.md');

// joomla/controller
system('rm -rf vendor/joomla/controller/.travis');
system('rm -rf vendor/joomla/controller/Tests');
system('rm vendor/joomla/controller/.gitattributes');
system('rm vendor/joomla/controller/.gitignore');
system('rm vendor/joomla/controller/.travis.yml');
system('rm vendor/joomla/controller/composer.json');
system('rm vendor/joomla/controller/phpunit.*');
system('rm vendor/joomla/controller/README.md');

// joomla/database
system('rm -rf vendor/joomla/database/.travis');
system('rm -rf vendor/joomla/database/Tests');
system('rm vendor/joomla/database/.gitattributes');
system('rm vendor/joomla/database/.gitignore');
system('rm vendor/joomla/database/.travis.yml');
system('rm vendor/joomla/database/composer.json');
system('rm vendor/joomla/database/phpunit.*');
system('rm vendor/joomla/database/README.md');

// joomla/date
system('rm -rf vendor/joomla/date/Tests');
system('rm vendor/joomla/date/composer.json');
system('rm vendor/joomla/date/phpunit.*');
system('rm vendor/joomla/date/README.md');

// joomla/di
system('rm -rf vendor/joomla/di/.travis');
system('rm -rf vendor/joomla/di/docs');
system('rm -rf vendor/joomla/di/Tests');
system('rm vendor/joomla/di/.travis.yml');
system('rm vendor/joomla/di/composer.json');
system('rm vendor/joomla/di/phpunit.*');
system('rm vendor/joomla/di/README.md');

// joomla/event
system('rm -rf vendor/joomla/event/.travis');
system('rm -rf vendor/joomla/event/Tests');
system('rm vendor/joomla/event/.travis.yml');
system('rm vendor/joomla/event/composer.json');
system('rm vendor/joomla/event/phpunit.*');
system('rm vendor/joomla/event/README.md');

// joomla/filesystem
system('rm -rf vendor/joomla/filesystem/Tests');
system('rm vendor/joomla/filesystem/.gitattributes');
system('rm vendor/joomla/filesystem/.gitignore');
system('rm vendor/joomla/filesystem/.travis.yml');
system('rm vendor/joomla/filesystem/composer.json');
system('rm vendor/joomla/filesystem/phpunit.*');
system('rm vendor/joomla/filesystem/README.md');

// joomla/filter
system('rm -rf vendor/joomla/filter/.travis');
system('rm -rf vendor/joomla/filter/Tests');
system('rm vendor/joomla/filter/.gitattributes');
system('rm vendor/joomla/filter/.gitignore');
system('rm vendor/joomla/filter/.travis.yml');
system('rm vendor/joomla/filter/composer.json');
system('rm vendor/joomla/filter/phpunit.*');
system('rm vendor/joomla/filter/README.md');

// joomla/image
system('rm -rf vendor/joomla/image/.travis');
system('rm -rf vendor/joomla/image/Tests');
system('rm vendor/joomla/image/.gitattributes');
system('rm vendor/joomla/image/.gitignore');
system('rm vendor/joomla/image/.travis.yml');
system('rm vendor/joomla/image/composer.json');
system('rm vendor/joomla/image/phpunit.*');
system('rm vendor/joomla/image/README.md');

// joomla/input
system('rm -rf vendor/joomla/input/Tests');
system('rm vendor/joomla/input/composer.json');
system('rm vendor/joomla/input/phpunit.*');
system('rm vendor/joomla/input/README.md');

// joomla/language
system('rm -rf vendor/joomla/language/.travis');
system('rm -rf vendor/joomla/language/Tests');
system('rm vendor/joomla/language/.gitattributes');
system('rm vendor/joomla/language/.gitignore');
system('rm vendor/joomla/language/.travis.yml');
system('rm vendor/joomla/language/composer.json');
system('rm vendor/joomla/language/phpunit.*');
system('rm vendor/joomla/language/README.md');

// joomla/model
system('rm -rf vendor/joomla/model/.travis');
system('rm -rf vendor/joomla/model/Tests');
system('rm vendor/joomla/model/.gitattributes');
system('rm vendor/joomla/model/.gitignore');
system('rm vendor/joomla/model/.travis.yml');
system('rm vendor/joomla/model/composer.json');
system('rm vendor/joomla/model/phpunit.*');
system('rm vendor/joomla/model/README.md');

// joomla/registry
system('rm -rf vendor/joomla/registry/.travis');
system('rm -rf vendor/joomla/registry/Tests');
system('rm vendor/joomla/registry/.gitattributes');
system('rm vendor/joomla/registry/.gitignore');
system('rm vendor/joomla/registry/.gitmodules');
system('rm vendor/joomla/registry/.travis.yml');
system('rm vendor/joomla/registry/composer.json');
system('rm vendor/joomla/registry/CONTRIBUTING.md');
system('rm vendor/joomla/registry/phpunit.*');
system('rm vendor/joomla/registry/README.md');

// joomla/router
system('rm -rf vendor/joomla/router/.travis');
system('rm -rf vendor/joomla/router/Tests');
system('rm vendor/joomla/router/.gitattributes');
system('rm vendor/joomla/router/.gitignore');
system('rm vendor/joomla/router/.travis.yml');
system('rm vendor/joomla/router/composer.json');
system('rm vendor/joomla/router/phpunit.*');
system('rm vendor/joomla/router/README.md');

// joomla/session
system('rm -rf vendor/joomla/session/Joomla/Session/_Tests');
system('rm -rf vendor/joomla/session/Joomla/Session/build');
system('rm -rf vendor/joomla/session/Joomla/Session/Tests');
system('rm vendor/joomla/session/Joomla/Session/composer.json');
system('rm vendor/joomla/session/Joomla/Session/phpunit.*');
system('rm vendor/joomla/session/Joomla/Session/README.md');

// joomla/string
system('rm -rf vendor/joomla/string/.travis');
system('rm -rf vendor/joomla/string/Tests');
system('rm vendor/joomla/string/.gitattributes');
system('rm vendor/joomla/string/.gitignore');
system('rm vendor/joomla/string/.travis.yml');
system('rm vendor/joomla/string/composer.json');
system('rm vendor/joomla/string/phpunit.*');
system('rm vendor/joomla/string/README.md');

// joomla/uri
system('rm -rf vendor/joomla/uri/.travis');
system('rm -rf vendor/joomla/uri/Tests');
system('rm vendor/joomla/uri/.gitattributes');
system('rm vendor/joomla/uri/.gitignore');
system('rm vendor/joomla/uri/.travis.yml');
system('rm vendor/joomla/uri/composer.json');
system('rm vendor/joomla/uri/phpunit.*');
system('rm vendor/joomla/uri/README.md');

// joomla/utilities
system('rm -rf vendor/joomla/utilities/.travis');
system('rm -rf vendor/joomla/utilities/Tests');
system('rm vendor/joomla/utilities/.gitattributes');
system('rm vendor/joomla/utilities/.gitignore');
system('rm vendor/joomla/utilities/.gitmodules');
system('rm vendor/joomla/utilities/.travis.yml');
system('rm vendor/joomla/utilities/composer.json');
system('rm vendor/joomla/utilities/CONTRIBUTING.md');
system('rm vendor/joomla/utilities/phpunit.*');
system('rm vendor/joomla/utilities/README.md');

// joomla/view
system('rm -rf vendor/joomla/view/.travis');
system('rm -rf vendor/joomla/view/Tests');
system('rm vendor/joomla/view/.gitattributes');
system('rm vendor/joomla/view/.gitignore');
system('rm vendor/joomla/view/.travis.yml');
system('rm vendor/joomla/view/composer.json');
system('rm vendor/joomla/view/phpunit.*');
system('rm vendor/joomla/view/README.md');

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

system('find . -type d -name .git -exec rm -rf {} \\; > /dev/null');
