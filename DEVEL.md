Making of a Mantis Plugin
=========================

2017-02

Documentation of the process
-------------

* [Developers Guide](http://mantisbt.org/docs/master-1.3.x/en-US/Developers_Guide/html-single/)
  A small tutorial, and a explanation of Mantis events.
  Many features are undocumented.
  You need to read the source code of Mantis or other plugins to get aware of their existence.

* `core/classes/MantisPlugin.class.php`
  This is the base class that the plugin must extend.
  Some features are only described within this code.

* `core/plugin_api.php`
  This is the code that will load the plugin.


Debug mode
----------

Described in `config_defaults_inc.php`.
Put the following code in `config/config_inc.php`:

```
$g_display_errors = array(
    E_USER_ERROR        => DISPLAY_ERROR_HALT,
    E_RECOVERABLE_ERROR => DISPLAY_ERROR_HALT,
    E_WARNING           => DISPLAY_ERROR_HALT,
    E_ALL               => DISPLAY_ERROR_INLINE,
);
```


Database
--------

As of 2017-02, the Mantis documentation does not mention that
a plugin can declare DB operations.

See `core/classes/MantisPlugin.class.php` for the method `schema()`
and its phpdoc block.

Unfortunately, `createTableSql` has no embedded documentation.
See the [createTableSql](http://adodb.org/dokuwiki/doku.php?id=v5:dictionary:createtablesql)
page on the ADOdb wiki.

For an example of the querying syntax (painful and fragile),
see <http://mantisbt.org/wiki/doku.php/mantisbt:executing_db_queries>.


Upgrading
---------

What happens when the version increases?
Auto upgrade? Manual upgraded required or non-blocking?
Which consequences on `schema()` and `config()`?

