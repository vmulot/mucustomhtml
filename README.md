prestashop-customHtmlBlocks
===========================

v1 :

Module to create custom html blocks on Prestashop 1.5

- Unzip folder to your prestashop module directory
- in Prestashop 1.5 admin, go to Modules, find Mu Custom Html, and install it.
This module is hooked in Header, Home and Customer Account (displayHeader, displayHome & displayCustomerAccount)

- Then add a new tab, Administration => Menus => add/create
    - Name : what ever you want
    - Class : AdminMuCustomHtml
    - Module : mucustomhtml
    - Icon : what ever you want
    - Published or not
    - Parent Menu

You can now start to add some blocks.


Features to come :
------------------

- Add admin tab when installing module
- Choice of the hook to display html blocks
