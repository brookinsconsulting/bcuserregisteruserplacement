BC User Register User Placement
===================

This extension provides a workflow event and settings to move a user upon creation to a specific user group or other content tree node based on the selection choice made during user registration (via user input).

Great for organizing user object within specific groups automatically!


Version
=======

* The current version of BC User Register User Placement is 3.0.0

* Last Major update: June 13, 2015


Copyright
=========

* BC User Register User Placement is copyright 1999 - 2016 Brookins Consulting and Tore Skobba

* See: [COPYRIGHT.md](COPYRIGHT.md) for more information on the terms of the copyright and license


License
=======

BC User Register User Placement is licensed under the GNU General Public License.

The complete license agreement is included in the [LICENSE](LICENSE) file.

BC User Register User Placement is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as published by
the Free Software Foundation.

BC User Register User Placement is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

The GNU GPL gives you the right to use, modify and redistribute
BC User Register User Placement under certain conditions. The GNU GPL license
is distributed with the software, see the file doc/LICENSE.

It is also available at [http://www.gnu.org/licenses/gpl.txt](http://www.gnu.org/licenses/gpl.txt)

You should have received a copy of the GNU General Public License
along with BC User Register User Placement in doc/LICENSE.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Using BC User Register User Placement under the terms of the GNU GPL is free (as in freedom).

For more information or questions please contact: license@brookinsconsulting.com


Requirements
============

The following requirements exists for using BC User Register User Placement extension:


### eZ Publish version

* Make sure you use eZ Publish version 4.3 (required) or higher.

* Designed and tested with eZ Publish Community Project 2014.11


### PHP version

* Make sure you have PHP 5.x or higher.


Features
========

### Dependencies

* This solution depends on eZ Publish Legacy only


### Workflow event

This solution overrides the following workflow event:

* Workflow event : `bcuserregisteruserplacement`


### Settings

This solution provides many settings. Some settings are required and must be customized upon installation and other settings are optional.

This solution's current settings are compatible with previous users of the eZ Publish 3.x `placeusers` extension.

Review the settings and educational comments in the `bcuserregisteruserplacement.ini.append.php` file, [bcuserregisteruserplacement.ini.append.php](settings/bcuserregisteruserplacement.ini.append.php)


#### Debugging

This solution's workflow event logs execution notices to the var/log/notice.log file

This solution's workflow event logs execution debug notices (when enabled via ini settings) to the var/log/debug.log file


Installation
============

### Extension Installation via Composer

Run the following command from your project root to install the extension:

    bash$ composer require brookinsconsulting/bcuserregisteruserplacement dev-master;


### Extension Activation

Activate this extension by adding the following to your `settings/override/site.ini.append.php`:

    [ExtensionSettings]
    # <snip existing active extensions list />
    ActiveExtensions[]=bcuserregisteruserplacement


### Regenerate kernel class override autoloads

Regenerate kernel class override autoloads (Required).

    php ./bin/php/ezpgenerateautoloads.php;


### Clear ini caches

Clear eZ Publish Platform / eZ Publish Legacy caches (Required).

    php ./bin/php/ezcache.php --clear-tag=ini;


Configuration
=============

### User Group Creation

* Then navigate to the admin siteaccess `User accounts` tab

* Create the user groups (two or more) new users should be moved into based on user register selection choice

* **Note** the new user group's `Node ID` value and name


### User Class Customization

* Then navigate to the admin siteaccess `Setup` tab, then click the `Classes` left side menu link, then click the `Users` `Class Groups` list item link

* Click the edit icon on the default user class (or the user class you wish to use with this solution)

* Add a new attribute of type 'selection' (using the ezselection datatype)

* Customize the new selection attribute name and identifier. **Note** the class attribute selection identifier as you will need to store it within ini settings

* Add two or more selection options. **Note** that the selection option text will be displayed as the selection name and the text entered here does not technically have to have anything specifically user group storage choice (this association is determined by matching option / settings order in the setup of ini override settings and user class selection option order)

* Save the user class changes by clicking 'Ok'


### Settings Configuration

* Create a settings override of the settings file `extension/bcuserregisteruserplacement/settings/bcuserregisteruserplacement.ini.append.php` in the `settings/override` directory. IE: `settings/override/bcuserregisteruserplacement.ini.append.php`.

* Customize the settings override settings as required or needed

* **Required**: You must add the `nodeID`s of the user groups into the MoveToUserGroupId[] settings array. Here is a example that would work in a default installation of eZ Publish Legacy:

    MoveToUserGroupId[]
    MoveToUserGroupId[]=14
    MoveToUserGroupId[]=213

* **Required**: Please pay specific attention and notice that the **order** of the entries in the `MoveToUserGroupId[]` settings array **must** match / corespond to the order of the user class selection attribute options order for the users to be moved into the correct user group upon user creation

* **Required**: You must review and customize the user class selection attribute identifier used when adding the content class selection attribute content. the user class selection attribute identifier expected by default is `type`. You can use any supported identifier you wish but if you change the identifier then you must customize the `UserAttributeSelectionIdentifier` setting. Here is a custom example:

    UserAttributeSelectionIdentifier=user_group_selection_type


### Workflow Configuration

* Then navigate to the admin siteaccess `Setup` tab, then click the `Workflow` left side menu link, then click the `Standard` `Workflow groups` list item link

* Click `New workflow` button

* Add the text `BC User Registration User Placement` into the name field or any other text you prefer as this text is only displayed in the trigger list workflow selection menu choices

* Select `Event / BC User Register User Placement` workflow event item in the workflow event selection menu and click, `Add event`, wait for page to reload normally and click `Ok`

### Workflow Trigger Configuration

Now add the workflow to an trigger, i.e:

* Then navigate to the admin siteaccess `Setup` tab, then click the `Triggers` left side menu link

* For the `Module, Function, Connection type` entry, `content, publish, after` in the `Workflow` selection menu, select the name of the workflow you created earlier, we suggested, `BC User Registration User Placement`

* Click `Apploy changes` button and wait for the page to refresh normally and thus saving your changes

**Note**: This solution can also be used with a Workflow Multiplexer if you prefer or require additional workflow event flexibility and control


Usage
=====

### Example Usage Setup

* Employee users having selection with identifier `type` with options `journalist, driver, cheif` in that order

* Creating user groups `drivers , journalists, cheifs` with nodeIDs `23, 24, 25`

* Edit settings override: `settings/override/bcuserregisteruserplacement.ini.append.php` and customize required settings with nodeIDs and selection attribute identifiers. Here is an example:

    [BCUserRegisterUserPlacement]
    UserAttributeSelectionIdentifier=type
    MoveToUserGroupId[]=24
    MoveToUserGroupId[]=23
    MoveToUserGroupId[]=25


### Testing

The solution is configured to work once properly installed and configured.

Simply navigate to the `user/register` module view and register a user.

**Note**: If you do not edit your `settings/override/site.ini.append.php` settings override file and have the following (empty) setting than all new user registrations will require new user registration email activation (user must check for email sent from eZ Publish containing a unique link to activate the user account before the user can login ).

    [UserSettings]
    VerifyUserType=

**Note**: If you use any kind of user account creation moderation then this solution will not be used until after the admin user moderating has approve the user account creation. This solution has been designed to work regardless of user account creation moderation system used.

Then navigate to the admin siteaccess `User accounts` tab, to the expected user group node and confirm that the newly registered user has been created.


Troubleshooting
===============

### Read the FAQ

Some problems are more common than others. The most common ones are listed in the the [doc/FAQ.md](doc/FAQ.md)


### Support

If you have find any problems not handled by this document or the FAQ you can contact Brookins Consulting through the support system: [http://brookinsconsulting.com/contact](http://brookinsconsulting.com/contact)

