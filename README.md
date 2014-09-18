Cobalt App
==========

Prototype Standalone CRM on Joomla! Framework

## Installation via Git and Composer

At folder you want to install Cobalt in execute these commands:

```
git clone git@github.com:cobaltcrm/cobalt.git .
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

### Cobalt Autocomplete

The Autocomplete feature use [Twitter Typeahead.js](http://twitter.github.io/typeahead.js/) and [Bloodhound](https://github.com/twitter/typeahead.js/blob/master/doc/bloodhound.md) as sugestion engine.

Example 1

Simple Autocomplete with Deals name that are published

```javascript

CobaltAutocomplete.create({
    object: 'deal',
    fields: 'name',
    prefetch: {
        ajax: {
            type: 'post',
            data: {
                published: 1
            }
        }
    }
});
$('#input_id').typeahead(null,CobaltAutocomplete.getConfig('deal'));
```

Example 2

Here how to create a autocomplete with people object using Bloodhound as sugestion engine.

```javascript
CobaltAutocomplete.create({
    object: 'people',
    fields: 'id,first_name,last_name',
    display_key: 'name',
    prefetch: {
        filter: function(list) {
            return $.map(list, function (item){ item.name = item.first_name+' '+item.last_name; return item; });
        },
        ajax: {
            type: 'post',
            data: {
                published: 1
            }
        }
    }
});

$('#input_id').typeahead({
    highlight: true
},CobaltAutocomplete.getConfig('people'));
```

Ps.1: People Autocomplete not have 'name' attribute so we're creating at filter by join two attributes.
Ps.2: You can specify some filter condition like: published=1

### Coding standards

Since we are using Joomla Framework, let's follow it's [Coding Standars](http://joomla.github.io/coding-standards/).