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

### Templating

Templates are rendered with the [Symfony Templating Component](http://symfony.com/doc/current/components/templating/introduction.html).  Some considerations for the implementation in Cobalt:

The lookup paths are as follows:
* <root>/themes/<template>/layouts/<View>/<layout>.php
* <root>/src/Cobalt/View/<View>/tmpl/<layout>.php
* <root>/themes/<template>/layouts/<layout>.php

Only base template layouts (such as the main index layout) should go in the root of the template's layouts folder.  Otherwise, everything else should go into the folder for the specific view.

In the lookup paths, <View> requires the first character to be uppercase, similar to how the Views are structured in the `\Cobalt\View` namespace.

When a layout extends another, you will call `$view->extend()`.  Please exclude the .php extension from the names here, it is automatically appended during the actual lookup processing.  For most layouts that extend the base template layout, you will call `$view->extend('index');` to properly extend the base template.

To render the contents of another layout, you will call `$view->render()`.  The render method accepts two parameters; the template name and an optional array of parameters to inject into the layout.  The template name should be passed as an instance of `\Cobalt\Templating\TemplateReference` to allow the correct paths to be extracted.  So, for example, to render the `dashboard_event_dock` layout from the `Events` view and inject an `$events` variable, you would call `$view->render(new TemplateReference('dashboard_event_dock', 'events'), array('events' => $events))`.

An `AssetsHelper` class is available in the layouts in order to add and render media.  To access it, you would reference `$view['assets']`.  Please see the [Bootstrap theme index](/themes/bootstrap/layouts/index.php) for examples on how to use this helper.

The View class is responsible for injecting data into the layouts; the layout is not within the instance of a view class and therefore `$this` is not in scope for the layouts as expected.  Please see the [Dashboard HTML View](/src/Cobalt/View/Dashboard/Html.php) for an example on how to correctly construct a view and inject the data.
