Cobalt App
==========

Prototype Standalone CRM on Joomla! Framework

## Installation via Git and Composer

At folder you want to install Cobalt in execute these commands:

```
git clone git@github.com:cobaltcrm/cobalt.git
```
```
composer install
```

## Notes for developers

Cobalt went through refactoring last year. The goal was and still is to move Cobalt from Joomla Platform to [Joomla Framework](https://github.com/joomla-framework) and make this new version available for end users.

### JavaScript

New refactored JS is at https://github.com/cobaltcrm/cobalt/blob/master/themes/bootstrap/js/cobalt.js. The main inovation is there is namespace _Cobalt_ which contains all Cobalt JS functions. Most of JS functionality does not work now, so let's do it better when we have a chance now.

[Older version](https://github.com/cobaltcrm/cobalt/blob/master/src/Cobalt/media/js/cobalt.js) used many JS functions for saving a form like:

* addConvoEntry()
* save()
* saveAjax()
* saveCf()
* addDeal()
* .. and so on ..

The idea is to use only [one](https://github.com/cobaltcrm/cobalt/blob/master/themes/bootstrap/js/cobalt.js#L110) so let's try it this easy way from now on. 
