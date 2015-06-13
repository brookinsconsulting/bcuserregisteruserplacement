<?php
/**
 * File containing the BCUserRegisterUserPlacementType class.
 *
 * @copyright Copyright (C) 1999 - 2016 Brookins Consulting. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package bcuserregisteruserplacement
 */
class BCUserRegisterUserPlacementType extends eZWorkflowEventType
{
    /**
     * Workflow Event Type String
     */
    const WORKFLOW_TYPE_STRING = "bcuserregisteruserplacement";

    /**
     * Workflow Event Type Debug Parameters
     */
    const WORKFLOW_TYPE_DEBUG = false;
    const WORKFLOW_TYPE_DEBUG_STOP_EXECUTION = false;

    /**
     * Default constructor
     */
    function BCUserRegisterUserPlacementType()
    {
        /**
         * Define workflow event type. This assigns the name of the workflow event within the eZ Publish administration module views
         */
        $this->eZWorkflowEventType( self::WORKFLOW_TYPE_STRING, "BC User Register User Placement" );

        /**
         * Define trigger type. This workflow event requires the following to 'content, after, publish'
         */
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );

        self::writeDebug( 'writeNotice', "Start '" . self::WORKFLOW_TYPE_STRING . "' workflow event construction method", "Workflow : BC User Register User Placement : Constructor" );
    }

    /**
     * Workflow Event Type execute method
     */
    function execute( $process, $event )
    {
        /**
         * Fetch workflow process parameters
         */
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = $parameters[ 'object_id' ];

        self::writeDebug( 'writeNotice', "Start '" . self::WORKFLOW_TYPE_STRING . "' workflow event execute method" );

        $ini = eZINI::instance( 'site.ini' );
        $bcUserRegisterUserPlacementINI = eZINI::instance( 'bcuserregisteruserplacement.ini' );

        /**
         * Reading the default user class id as it is required to check that we only
         * perform the workflow event on user class content and not other class of objects
         */
        $defaultUserClassID = $ini->hasVariable( 'UserSettings', 'UserClassID' ) == true ? $ini->variable( 'UserSettings', 'UserClassID' ) : false;

        /**
         * Reading the default user placement nodeID as it is the default location where new users are stored
         */
        $userGroupID = $ini->hasVariable( 'UserSettings', 'DefaultUserPlacement' ) == true ? $ini->variable( 'UserSettings', 'DefaultUserPlacement' ) : 0;

        self::writeDebug( 'writeNotice', "User class id is: " . $defaultUserClassID . ' Default user group is: ' . $userGroupID );

        $userGroups = $bcUserRegisterUserPlacementINI->hasVariable( 'BCUserRegisterUserPlacement', 'MoveToUserGroupId' ) == true ? $bcUserRegisterUserPlacementINI->variable( 'BCUserRegisterUserPlacement', 'MoveToUserGroupId' ) : array();
        $objectSelectionAttributeIdentifier = $bcUserRegisterUserPlacementINI->hasVariable( 'BCUserRegisterUserPlacement', 'UserAttributeSelectionIdentifier' ) == true ? $bcUserRegisterUserPlacementINI->variable( 'BCUserRegisterUserPlacement', 'UserAttributeSelectionIdentifier' ) : false;
        $move = $bcUserRegisterUserPlacementINI->hasVariable( 'BCUserRegisterUserPlacement', 'Move' ) == true && strtolower( $bcUserRegisterUserPlacementINI->variable( 'BCUserRegisterUserPlacement', 'Move' ) ) == 'enabled' ? true : false;
        $setMainNode = $bcUserRegisterUserPlacementINI->hasVariable( 'BCUserRegisterUserPlacement', 'SetMainNode' ) == true && strtolower( $bcUserRegisterUserPlacementINI->variable( 'BCUserRegisterUserPlacement', 'SetMainNode' ) ) == 'enabled' ? true : false;

        $selectedNodeID = false;

        // Fetch content object from the workflow process provided object_id
        $object = eZContentObject::fetch( $objectID );

        // Fetch content object attributes required
        $objectName = $object->attribute( 'name' );

        self::writeDebug( 'writeNotice', "Object name: " . $objectName );

        $objectContentClass = $object->attribute( 'class_name' );

        self::writeDebug( 'writeNotice', "Content Class is: " . $objectContentClass );

        $objectContentClassID = $object->attribute( 'contentclass_id' );

        self::writeDebug( 'writeNotice', "Default user class id is: " . $defaultUserClassID . ". This object class id is: " . $objectContentClassID );

        /**
         * Test if content object class ID matches ini settings default user content object class ID
         * Only perform workflow event operations on content objects of the correct content class
         */
        if ( $objectContentClassID == $defaultUserClassID )
        {
            // Fetch content object attributes needed
            $assignedNodes = $object->attribute( 'assigned_nodes' );
            $objectDataMap = $object->attribute( 'data_map' );
            $objectNodeAssignments = eZNodeAssignment::fetchForObject( $objectID, $object->attribute( 'current_version' ), 0, false );
            //$objectNodeAssignments = $object->attribute( 'assigned_nodes' );

            // Get the selection content
            $objectSelectionAttributeContent = $objectDataMap[ $objectSelectionAttributeIdentifier ]->attribute( 'content' );
            $objectSelectionAttributeContentString = implode( ',', $objectSelectionAttributeContent );

            self::writeDebug( 'writeNotice', "User object attribute " . $objectSelectionAttributeIdentifier . " content is set to: " . $objectSelectionAttributeContentString );

            /**
             * Test to ensure that object selection attribute content is greater than 0 (no selection) or
             * that object selection attribute count is less than the count of userGroups (defined in ini settings)
             */
            if ( $objectSelectionAttributeContent > 0 || $objectSelectionAttributeContent < count( $userGroups ) )
            {
                // Set userGroupID from ini defined user groups based on content object selection attribute content
                $userGroupID = $userGroups[ $objectSelectionAttributeContentString ];
                $selectedNodeID = $userGroupID;
            }

            $parentNodeIDs = array();
            $ourNode = false;

            /**
             * Iterate over object assigned nodes and object node assignements
             * test for parent node id matches and build array of parent_node_ids
             * test for user content object selection attribute content selected node id
             * and set node to move based on match
             */
            foreach ( $assignedNodes as $assignedNode )
            {
                $append = false;

                foreach ( $objectNodeAssignments as $nodeAssignment )
                {
                    if ( $nodeAssignment[ 'parent_node' ] == $assignedNode->attribute( 'parent_node_id' ) )
                    {
                        $append = true;
                        break;
                    }
                }

                if ( $append )
                {
                    $parentNodeIDs[] = $assignedNode->attribute( 'parent_node_id' );
                }

                if ( $assignedNode->attribute( 'parent_node_id' ) == $selectedNodeID )
                {
                    $ourNode = $assignedNode;
                }
            }

            /**
             * Test if we are to move the current main node to the selected location
             */
            if ( $move )
            {
                self::writeDebug( 'writeDebug', 'Moving tactic' );

                if ( !is_object( $ourNode ) )
                {
                    self::writeDebug( 'writeDebug', 'Node not found, so moving existing main node...' );

                    eZContentObjectTreeNodeOperations::move( $object->attribute( 'main_node_id' ), $selectedNodeID );
                }
            }
            else
            {
                /**
                 * Create a new node location assignment
                 */

                self::writeDebug( 'writeDebug', 'New node tactic' );

                if ( !is_object( $ourNode ) )
                {
                    self::writeDebug( 'writeDebug', 'Node not found, so creating a new one ...' );

                    $parentNode = eZContentObjectTreeNode::fetch( $selectedNodeID );
                    $parentNodeObject = $parentNode->attribute( 'object' );

                    // Add user content object location
                    $ourNode = $object->addLocation( $selectedNodeID, true );

                    // Now set node as published and fix main_node_id
                    $ourNode->setAttribute( 'contentobject_is_published', 1 );
                    $ourNode->setAttribute( 'main_node_id', $object->attribute( 'main_node_id' ) );
                    $ourNode->setAttribute( 'contentobject_version', $object->attribute( 'current_version' ) );

                    // Make sure the node's path_identification_string is set correctly
                    $ourNode->updateSubTreePath();
                    $ourNode->sync();

                    eZUser::cleanupCache();
                }

                if ( $setMainNode )
                {
                    self::writeDebug( 'writeDebug', "'Setting as main node is enabled'", "", true );

                    if ( $object->attribute( 'main_node_id' ) != $ourNode->attribute( 'node_id' ) )
                    {
                        self::writeDebug( 'writeDebug', 'Existing main node is not our node, so updating main node', "", true );

                        eZContentObjectTreeNode::updateMainNodeID( $ourNode->attribute( 'node_id' ), $objectID, false, $selectedNodeID );
                        eZContentCacheManager::clearContentCacheIfNeeded( $objectID );
                    }
                }
            }
        }
        else
        {
            self::writeDebug( 'writeNotice', $objectName . ' is not a user class object' );
        }

        if( self::WORKFLOW_TYPE_DEBUG_STOP_EXECUTION === true )
        {
            die( "<hr />\n\nWorkflow: " . self::WORKFLOW_TYPE_STRING . " execution has been ended before normal completion for debugging" );
        }

        /**
         * Return default succesful workflow event status code, by default, regardless of results of execution, always.
         * Image alias image variation image files may not always need to be created. Also returning any other status
         * will result in problems with the succesfull and normal completion of the workflow event process
         */
        return eZWorkflowType::STATUS_ACCEPTED;
    }

    /**
     * Workflow Event Type writeDebug method
     */
    function writeDebug( $type = 'writeDebug', $string = null, $label = 'Workflow : BC User Register User Placement', $force = false )
    {
        if( $string != null && $force === true ||
            $string != null && self::WORKFLOW_TYPE_DEBUG === true )
        {
            eZDebug::$type( $string, $label );
        }
    }
}

/**
 * Register workflow event type class BCUserRegisterUserPlacementType
 */
eZWorkflowEventType::registerEventType( BCUserRegisterUserPlacementType::WORKFLOW_TYPE_STRING, "BCUserRegisterUserPlacementType" );

?>