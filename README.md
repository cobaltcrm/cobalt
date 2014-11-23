Cobalt App
==========

Prototype Standalone CRM on Joomla! Framework

## Installation via Git and Composer

At the folder you want to install Cobalt in, execute these commands:

```
git clone https://github.com/cobaltcrm/cobalt.git.
```
```
composer install
```

## Notes for developers

Cobalt went through refactoring last year. The goal was and still is to move Cobalt from Joomla Platform to [Joomla Framework](https://github.com/joomla-framework) and make this new version available for end users.

### JavaScript

New refactored JS is at https://github.com/cobaltcrm/cobalt/blob/master/themes/bootstrap/js/cobalt.js. The main innovation is there is namespace _Cobalt_ which contains all Cobalt JS functions. Most of JS functionality does not work now, so let's do it better when we have a chance now.

[Older version](https://github.com/cobaltcrm/cobalt/blob/master/src/Cobalt/media/js/cobalt.js) used many JS functions for saving a form like:

* addConvoEntry()
* save()
* saveAjax()
* saveCf()
* addDeal()
* .. and so on ..

The idea is to use only [one](https://github.com/cobaltcrm/cobalt/blob/master/themes/bootstrap/js/cobalt.js#L110) so let's try it this easy way from now on.

## Cobalt Autocomplete

The Autocomplete feature use [Twitter Typeahead.js](http://twitter.github.io/typeahead.js/) and [Bloodhound](https://github.com/twitter/typeahead.js/blob/master/doc/bloodhound.md) as sugestion engine.

### API Methods

* CobaltAutocomplete.create(config);

This Method will create an autocomplete.

```javascript
config = {
    id: 'addPerson', //ID from autocomplete (Optional: default will be object value)
    object: 'deal', //Object will be Cobalt/Table/DealTable (Required)
    fields: 'id,name', //specify what fields will return from ajax request (Required)
    display_key: 'name', //Field name that will be used for list in autocomplete (Required)
    prefetch: {}, //For details see Bloodhound Documentation (Optional)
}
```

* CobaltAutocomplete.getConfig(id);

Return Bloodhound Configuration Object

* CobaltAutocomplete.getBloodhound(id);

Return Bloodhound Object

### Examples

Below few examples how to use CobaltAutocomplete

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

Here's how to create an autocomplete with people object using Bloodhound as suggestion engine.

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

Since we are using the Joomla Framework, let's follow it's [Coding Standards](http://joomla.github.io/coding-standards/).
