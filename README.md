# This Magento 1 extension is orphaned, unsupported and no longer maintained.

If you use it, you are effectively adopting the code for your own project.

Magento Post Code Filter Extension
===================================
Only allow customer groups with a shipping address within a specific postcode list to place orders.

Description
-----------
When installed, the administrator can manage Post Code Filter rules in the admin area under the *Customer* menu item.

Each Post Code Filter consists of

* a country (from a select input field)
* one or more customer groups (from a multiselect input field)
* a list of postcodes (comma or newline separated list of records in a text area input field)

The filter rules are applied during checkout as follows:

1. If a filter rule exists for the shipping address country and the customers customer group, and the shipping address postcode is **within** the associated list of **postcodes**, **allow** order placement.
2. If a filter rule exists for the shipping address country and the customers customer group, but the shipping address postcode is **not within** the associated list of **postcodes**, **deny** order placement.
3. If **no filter rule** exists for the shipping address country and the customers customer group, **allow** order placement.

The third rule might be confusing at first, but the idea is to create a rule for all allowed countries for the NOT LOGGED IN (a.k.a guest) and General customer group, and then the all other customer groups automatically become reseller groups to whom them filter rule do not apply.


Usage
-----
Use [modman](https://github.com/colinmollenhour/modman) to install the extension, or copy the files within the *src/* directory into your Magento installation manually.

Compatibility
-------------
- Magento >= 1.6
- PHP >= 5.4

Installation Instructions
-------------------------
If you are using the Magento compiler, disable compilation before the installation, and after the module is installed, you need to run the compiler again.

1. Use [modman](https://github.com/colinmollenhour/modman) to install the extension, or copy the files within the *src/* directory into your Magento installation manually.
2. Clear the cache.
3. If you use the Magento compiler tool, recompile after installation

There are no system configuration options available for this extension.

Uninstallation
--------------
To uninstall this extension you need to run the following SQL after removing the extension files:
```sql
  DELETE FROM `core_resource` WHERE code = 'vinaikopp_postcodefilter_setup';
  DROP TABLE IF EXISTS `vinaikopp_postcodefilter_rule`;
```

Developer
---------
Vinai Kopp  
Web: [http://vinaikopp.com](http://vinaikopp.com)  
Twitter: [@VinaiKopp](https://twitter.com/VinaiKopp)

Licence
-------
BSD 3-Clause. Please refer to the file [LICENSE.md](./LICENSE.md) for details.

Copyright
---------
(c) 2015 Vinai Kopp
