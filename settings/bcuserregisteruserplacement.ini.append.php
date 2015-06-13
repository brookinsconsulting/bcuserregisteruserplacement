<?php /* #?ini charset="utf8"?

[BCUserRegisterUserPlacement]
# MoveToUserGroup is setting which contains the user group nodeID you want to place the user in.
# This depends on the coresponding value in user class attribute of datatype ezselection aka 'Selection',
# the order the nodeIDs are listed here MUST correspond to the order of options in the Selection attribute
# values list in the user attribute.

MoveToUserGroupId[]
# MoveToUserGroupId[]=12

# Examples of other user group nodeIDs provided by the default installation of eZ Publish
# MoveToUserGroupId[]=14
# MoveToUserGroupId[]=213

# The user content class selection attribute which holds the various
# user groups storage choice criteria the user can select from.
# Note that you do not have to expose user groups directly in this selection
UserAttributeSelectionIdentifier=type

# Example: The above setting means that the user class has an selection attribute with identifier type with
# two options, if the use selects the first option he is moved to usergroup with node id 59,
# the second option will lead him to usergroup with node id 60

# Enable this to move the current main node to the selected location
# if this is not enabled, a new node location assignment will be created instead
Move=enabled

# Only taken into account when Move is not enabled
# set the node at the selected location as the main node
SetMainNode=enabled

*/ ?>