<?php

/*
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2015 Paulo Ferreira <pf at sourcenotes.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace controllers\user;

use api\controller\ActionContext;
use common\utility\Strings;
use api\controller\BaseServiceController;

/**
 * Controller used to Manage Run Executions
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ContainersController extends BaseServiceController {

  protected static $instance = null;

  /**
   * Singleton Pattern - Get Instance of the Controller
   * 
   * @return ContainersController Instance of Controller
   */
  public static function getInstance() {
    if (!isset(self::$instance)) {
      self::$instance = new ContainersController();
    }

    return self::$instance;
  }

  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the Root Folder from a Container or Container Entry
   * 
   * @param integer $entry Container or Container Entry ID
   * @return string HTTP Body Response
   */
  public function root($entry) {
    // Create and Initialize Action Context
    $context = new ActionContext('root');
    $context = $context
      ->setRequiredInteger('entry:id', $entry);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Retrieve the Parent Folder for a Container or Container Entry
   * 
   * @param integer $entry Container or Container Entry ID
   * @return string HTTP Body Response
   */
  public function parentFolder($entry) {
    // Create and Initialize Action Context
    $context = new ActionContext('parent');
    $context = $context
      ->setRequiredInteger('entry:id', $entry);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Does an Entry with the Given Name already Exist as a Child of the Parent?
   * 
   * @param integer $parent Parent Container ID
   * @param string $name Folder Name
   * @return string HTTP Body Response
   */
  public function exists($parent, $name) {
    // Create and Initialize Action Context
    $context = new ActionContext('exists');

    // Initialize Context
    $context = $context
      ->setRequiredInteger('parent:id', $parent)
      ->setRequiredString('name', $name);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Create a Child Folder
   * 
   * @param integer $parent Parent Container ID
   * @param string $name Folder Name
   * @param boolean $single_level [DEFAULT: YES] Allow Child Containers
   * @return string HTTP Body Response
   */
  public function createFolder($parent, $name, $single_level = false) {
    // Create and Initialize Action Context
    $context = new ActionContext('create');

    // Process Single Level Parameter
    $single_level = Strings::nullOnEmpty($single_level);
    switch ($single_level) {
      case 'true':
        $single_level = true;
        break;
      case null:
        $single_level = false;
        break;
      default:
        $single_level = !!$single_level;
    }

    // Initialize Context
    $context = $context
      ->setRequiredInteger('parent:id', $parent)
      ->setRequiredString('folder:name', $name)
      ->setParameter('folder:single_level', $single_level);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Rename a Folder
   * 
   * @param integer $folder Container ID
   * @param string $new_name New Folder Name
   * @return string HTTP Body Response
   */
  public function renameFolder($folder, $new_name) {
    // Create and Initialize Action Context
    $context = new ActionContext('rename');
    $context = $context
      ->setRequiredInteger('folder:id', $folder)
      ->setRequiredString('folder:name', $new_name);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Move a Folder within Folder Structure
   * 
   * @param integer $entry Container or Container Entry ID
   * @param integer $new_parent New Folder's Parent Container ID
   * @return string HTTP Body Response
   */
  public function moveEntry($entry, $new_parent) {
    // Create and Initialize Action Context
    $context = new ActionContext('move');
    $context = $context
      ->setRequiredInteger('entry:id', $entry)
      ->setRequiredInteger('parent:id', $new_parent);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Delete a Folder or Folder Entry
   * 
   * @param integer $entry Container or Container Entry ID
   * @return string HTTP Body Response
   */
  public function deleteEntry($entry) {
    // Create and Initialize Action Context
    $context = new ActionContext('delete');
    $context = $context
      ->setRequiredInteger('entry:id', $entry);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Folder Entries (With Optional Filter and Sort Conditions)
   * 
   * @param integer $folder Container to List Contents for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @param string $sort [DEFAULT null = By Name Ascending] Sort Conditions
   * @return string HTTP Body Response
   */
  public function listFolders($folder, $filter = null, $sort = null) {
    return $this->listEntries($folder, 'F', $filter, $sort);
  }

  /**
   * List Tests in Folder (With Optional Filter and Sort Conditions)
   * 
   * @param integer $folder Container to List Contents for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @param string $sort [DEFAULT null = By Name Ascending] Sort Conditions
   * @return string HTTP Body Response
   */
  public function listTests($folder, $filter = null, $sort = null) {
    // Create and Initialize Action Context
    $context = new ActionContext('list_tests');
    $context = $context
      ->setRequiredInteger('folder:id', $folder)
      ->setOptionalString('filter', $filter)
      ->setOptionalString('sort', $sort);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Test Set in Folder (With Optional Filter and Sort Conditions)
   * 
   * @param integer $folder Container to List Contents for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @param string $sort [DEFAULT null = By Name Ascending] Sort Conditions
   * @return string HTTP Body Response
   */
  public function listSets($folder, $filter = null, $sort = null) {
    // Create and Initialize Action Context
    $context = new ActionContext('list_sets');
    $context = $context
      ->setRequiredInteger('folder:id', $folder)
      ->setOptionalString('filter', $filter)
      ->setOptionalString('sort', $sort);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Runs in Folder (With Optional Filter and Sort Conditions)
   * 
   * @param integer $folder Container to List Contents for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @param string $sort [DEFAULT null = By Name Ascending] Sort Conditions
   * @return string HTTP Body Response
   */
  public function listRuns($folder, $filter = null, $sort = null) {
    // Create and Initialize Action Context
    $context = new ActionContext('list_runs');
    $context = $context
      ->setRequiredInteger('folder:id', $folder)
      ->setOptionalString('filter', $filter)
      ->setOptionalString('sort', $sort);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * List Folder Entries (With Optional Type Filter)
   * 
   * @param integer $folder Container to List Contents for
   * @param string $type [DEFAULT null = All Items] List Items in the Container
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @param string $sort [DEFAULT null = By Name Ascending] Sort Conditions
   * @return string HTTP Body Response
   */
  public function listEntries($folder, $type = null, $filter = null, $sort = null) {
    // Create and Initialize Action Context
    $context = new ActionContext('list');
    $context = $context
      ->setRequiredInteger('folder:id', $folder)
      ->setOptionalString('type', $type)
      ->setOptionalString('filter', $filter)
      ->setOptionalString('sort', $sort);

    // Call Action
    return $this->doAction($context);
  }

  /**
   * Count Folder Entries (With Optional Name Filter)
   * 
   * @param integer $folder Container to Count Entries for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @return string HTTP Body Response
   */
  public function countFolders($folder, $filter = null) {
    return $this->countEntries($folder, 'F', $filter);
  }

  /**
   * Count Test Entries (With Optional Name Filter)
   * 
   * @param integer $folder Container to Count Entries for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @return string HTTP Body Response
   */
  public function countTests($folder, $filter = null) {
    return $this->countEntries($folder, 'T', $filter);
  }

  /**
   * Count Test Set Entries (With Optional Name Filter)
   * 
   * @param integer $folder Container to Count Entries for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @return string HTTP Body Response
   */
  public function countSets($folder, $filter = null) {
    return $this->countEntries($folder, 'S', $filter);
  }

  /**
   * Count Run Entries (With Optional Name Filter)
   * 
   * @param integer $folder Container to Count Entries for
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @return string HTTP Body Response
   */
  public function countRuns($folder, $filter = null) {
    return $this->countEntries($folder, 'R', $filter);
  }

  /**
   * Count Folder Entries (With Optional Type Filter)
   * 
   * @param integer $folder Container to Count Entries for
   * @param string $type [DEFAULT null = All Items] List Items in the Container
   * @param string $filter [DEFAULT null = All Items] Name Filter
   * @return string HTTP Body Response
   */
  public function countEntries($folder, $type = null, $filter = null) {
    // Create and Initialize Action Context
    $context = new ActionContext('count');
    $context = $context
      ->setRequiredInteger('folder:id', $folder)
      ->setOptionalString('type', $type)
      ->setOptionalString('filter', $filter);

    // Call Action
    return $this->doAction($context);
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Get Root Container for an  Entry
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container Root Container
   * @throws \Exception On failure to perform the action
   */
  protected function doRootAction($context) {
    // Get the Current Container
    $entry = $context->getParameter('entry');
    assert('isset($entry)');

    // $root = $entry->getRoot();
    $root_id = $entry->root;

    // Did we find the Container Entry with the Given ID?
    $root = \models\Container::findFirst($root_id);
    if ($root === FALSE) { // NO
      throw new \Exception("Root Container ID [$root_id] not found", 1);
    }

    return $root;
  }

  /**
   * Get Parent Container for an Entry
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container Root Container
   * @throws \Exception On failure to perform the action
   */
  protected function doParentAction($context) {
    // Get the Current Container
    $entry = $context->getParameter('entry');
    assert('isset($entry)');

    // $parent = $entry->getParent();
    $parent_id = $entry->parent;

    // Did we find the Container Entry with the Given ID?
    $parent = \models\Container::findFirst($parent_id);
    if ($parent === FALSE) { // NO
      throw new \Exception("Container ID [$root_id] not found", 1);
    }

    return $parent;
  }

  /**
   * Create a Child Container
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container Root Container
   * @throws \Exception On failure to perform the action
   */
  protected function doExistsAction($context) {
    // Get the Current Container
    $parent = $context->getParameter('parent');
    $name = $context->getParameter('name');
    assert('isset($parent) && isset($name)');

    return $this->_exists($parent, $name);
  }

  /**
   * Create a Child Container
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container Root Container
   * @throws \Exception On failure to perform the action
   */
  protected function doCreateAction($context) {
    // Get the Current Container
    $parent = $context->getParameter('parent');
    $name = $context->getParameter('folder:name');
    $single_level = $context->getParameter('folder:single_level');
    assert('isset($parent) && isset($name) && isset($single_level)');

    if (!$this->_exists($parent, $name)) {
      // Create Child Folder and Save It
      $folder = \models\Container::newChildContainer($parent, $name, $single_level);

      // If the Entity Allows it Set the Creation User and Date
      $this->setCreator($folder, $context->getParameter('cm_user'));

      $this->_persist($folder);
      return $folder;
    } else {
      throw new \Exception("Child with name [{$name}] already exist in Container ID [{$parent->id}] not found", 1);
    }
  }

  /**
   * Rename a Child Container
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container Renamed folder
   * @throws \Exception On failure to perform the action
   */
  protected function doRenameAction($context) {
    // Get the Current Container
    $folder = $context->getParameter('folder');
    $name = $context->getParameter('folder:name');
    assert('isset($folder) && isset($name)');

    // Rename the Folder and Save It
    $folder->name = $name;

    // If the Entity Allows it Set the Modification User and Date
    $this->setModifier($folder, $context->getParameter('cm_user'));

    $this->_persist($folder);
    return $folder;
  }

  /**
   * Move a Child Container
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container Moved Folder
   * @throws \Exception On failure to perform the action
   */
  protected function doMoveAction($context) {
    // Get the Current Container
    $parent = $context->getParameter('parent');
    $entry = $context->getParameter('entry');
    assert('isset($parent) && isset($entry)');

    // Change Parent and Save It
    $entry->parent = $parent->id;

    // If the Entity Allows it Set the Modification User and Date
    $this->setModifier($entry, $context->getParameter('cm_user'));

    $this->_persist($entry);
    return $entry;
  }

  /**
   * Delete a Child Entry
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return boolean 'true' always (failure is known by an exception)
   * @throws \Exception On failure to perform the action
   */
  protected function doDeleteAction($context) {
    // Get the Current Container
    $entry = $context->getParameter('entry');
    assert('isset($entry)');

    // User to Perform the Change
    $user = $context->getParameter('cm_user');

    switch ($entry->type) {
      case 'F':
        // Is it a Child Folder?
        if (isset($entry->parent)) { // YES
          // Did we delete all the child entries?
          if ($this->_deleteChildren($entry)) { // YES
            // Delete the Folder
            $this->_delete($user, $entry);
          } else { // NO
            // Root Containers Can only Be Deleted along with the Owner
            throw new \Exception("Container [$entry->id] is not Empty.", 1);
          }
        } else { // NO: Root Container
          // Root Containers Can only Be Deleted along with the Owner
          throw new \Exception("Container [$entry->id] can not be deleted.", 1);
        }
        break;
      default:
        $this->_delete($user, $entry);
    }
    return true;
  }

  /**
   * List Tests in a Specific Folder
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListTestsAction($context) {
    // Get the Current Container
    $folder = $context->getParameter('folder');
    $filter = $context->getParameter('filter');
    $sort = $context->getParameter('sort');
    assert('isset($folder)');

    // Do we have a Filter Defined?
    $filter = $this->_buildFilter(null, $filter);

    // Do we have a Sort Condition Defined?
    $orderBy = $this->_buildOrderBy($sort, 'name');

    // Find Child Entries
    $tests = \models\Test::listInFolder($folder, $filter, $orderBy);

    // Return Result Set
    return $tests;
  }

  /**
   * List Tests in a Specific Folder
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListSetsAction($context) {
    // Get the Current Container
    $folder = $context->getParameter('folder');
    $filter = $context->getParameter('filter');
    $sort = $context->getParameter('sort');
    assert('isset($folder)');

    // Do we have a Filter Defined?
    $filter = $this->_buildFilter(null, $filter);

    // Do we have a Sort Condition Defined?
    $orderBy = $this->_buildOrderBy($sort, 'name');

    // Find Sets
    $sets = \models\Set::listInFolder($folder, $filter, $orderBy);

    // Return Result Set
    return $sets;
  }

  /**
   * List Runs in a Specific Folder
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListRunsAction($context) {
    // Get the Current Container
    $folder = $context->getParameter('folder');
    $filter = $context->getParameter('filter');
    $sort = $context->getParameter('sort');
    assert('isset($folder)');

    // Do we have a Filter Defined?
    $filter = $this->_buildFilter(null, $filter);

    // Do we have a Sort Condition Defined?
    $orderBy = $this->_buildOrderBy($sort, 'name');

    // Find Runs
    $runs = \models\Run::listInFolder($folder, $filter, $orderBy);

    // Return Result Set
    return $runs;
  }

  /**
   * List Child Entries
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    // Get the Current Container
    $folder = $context->getParameter('folder');
    $type = $context->getParameter('type');
    $filter = $context->getParameter('filter');
    $sort = $context->getParameter('sort');
    assert('isset($folder)');

    // Do we have a Filter Defined?
    $filter = $this->_buildFilter($type, $filter);

    // Do we have a Sort Condition Defined?
    $orderBy = $this->_buildOrderBy($sort, 'type, name');

    // Find Child Entries
    $entities = \models\Container::listEntries($folder, $filter, $orderBy);

    // Return Result Set
    return $entities;
  }

  /**
   * Count Child Entries
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \models\Container[] Container Entries
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    // Get the Current Container
    $folder = $context->getParameter('folder');
    $type = $context->getParameter('type');
    $filter = $context->getParameter('filter');
    assert('isset($folder)');

    // Do we have a Filter Defined?
    $filter = $this->_buildFilter($type, $filter);

    // Count Child Entries
    $count = \models\Container::countEntries($folder, $filter);

    // Return Result Set
    return (integer) $count;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseServiceController: CHECKS
   * ---------------------------------------------------------------------------
   */

  /**
   * 
   * @param type $context
   * @return type
   * @throws \Exception
   */
  protected function sessionChecks($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Check we have Basic Requirements
    $this->sessionManager->checkInSession();
    $this->sessionManager->checkLoggedIn();

    return $context;
  }

  /*
   * ---------------------------------------------------------------------------
   * BaseController: STAGES
   * ---------------------------------------------------------------------------
   */

  /**
   * Perform any required setup, before the Action Handler is Called.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return \api\controller\ActionContext Outgoing Context for Action
   * @throws \Exception On any type of failure condition
   */
  protected function preAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Process 'entry:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'entry:id', function($controller, $context, $action, $value) {
      // Did we find the Container Entry with the Given ID?
      $entry = \models\Container::findFirst($value);
      if ($entry === FALSE) { // NO
        throw new \Exception("Container Entry with ID [$value] not found", 1);
      }

      return $context->setParameter('entry', $entry);
    }, null, ['Root', 'Parent', 'Move', 'Delete']);

    // Process 'folder:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'folder:id', function($controller, $context, $action, $value) {
      // Did we find the Container with the Given ID?
      $folder = \models\Container::findFirst($value);
      if (($folder === FALSE) || ($folder->type !== 'F')) { // NO
        throw new \Exception("Container with ID [$value] not found", 1);
      }

      return $context->setParameter('folder', $folder);
    }, null, [
      'Rename',
      'List', 'ListTests', 'ListSets', 'ListRuns',
      'Count', 'CountTests', 'CountSets', 'CountRuns'
    ]);

    // Process 'parent:id' Parameter (if it exists)
    $context = $this->onParameterDo($context, 'parent:id', function($controller, $context, $action, $value) {
      // Did we find the Container with the Given ID?
      $parent = \models\Container::findFirst($value);
      if (($parent === FALSE) || ($parent->type !== 'F')) { // NO
        throw new \Exception("Container with ID [$value] not found", 1);
      }

      return $context->setParameter('parent', $parent);
    }, null, ['Exists', 'Create', 'Move']);

    /* TODO: Problem
     * How do we verify the access rights to the Container or Entry?
     * i.e. How do we verify if the current user, should have access or not,
     * to the entries that the service refers to?
     * 
     * Possible Solution:
     * By using a type registry, we can create a public static method (i.e. like
     * allowed($item_id, $user, $permissions) would return 'true' if the 
     * required access is allowed, or 'false' otherwise.
     */
    // Get the User for the Active Session
    $id = $this->sessionManager->getUser();
    $user = \models\User::findFirst($id);

    // Did we find the user?
    if ($user === FALSE) { // NO
      throw new \Exception("User [$id] not found", 6);
    }

    // Save the User in the Context
    return $context->setParameter('user', $user)
        ->setParameter('cm_user', $user);
  }

  /**
   * Perform any required setup, before we perform final rendering of the Action's
   * Result.
   * 
   * @param \api\controller\ActionContext $context Incoming Context for Action
   * @return mixed Action Output that is to be Rendered
   * @throws \Exception On any type of failure condition
   */
  protected function preRender($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get Results
    $results = $context->getActionResult();
    assert('isset($results)');

    // Get the Action Name
    $action = $context->getAction();
    assert('isset($action)');
    switch ($action) {
      case 'Root':
      case 'Parent':
      case 'Create':
      case 'Rename':
      case 'Move':
        $return = $results->toArray();
        break;
      case 'List':
      case 'ListTests':
      case 'ListSets':
      case 'ListRuns':
        $return = [];
        $entities = [];
        $header = true;
        foreach ($results as $entries) {
          $entities[] = $entries->toArray($header);
          $header = false;
        }

        // Do we have entities to display?
        if (count($entities)) { // YES
          // Move the Entity Information to become Result Header
          $this->moveEntityHeader($entities[0], $return);
          $return['entities'] = $entities;
        } else {
          $return['entities'] = [];
        }

        // Create Base Entity Set Identified
        $return['__type'] = 'entity-set';
        break;
      default:
        $return = $results;
    }

    return $return;
  }

  /*
   * ---------------------------------------------------------------------------
   * HELPER FUNCTIONS: Entity DB Persistance
   * ---------------------------------------------------------------------------
   */

  /**
   * If the Entity Manages it, this sets the User and Timestamp of when the
   * Entity was created
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param \User $user User to set as the Objects Modifier
   * @return \api\model\AbstractEntity Modified Entity
   */
  protected function setCreator($entity, $user) {
    // Is the User Set?
    if (isset($user)) { // YES
      // Does this Entity Object Have a Creator Property?
      if (property_exists($entity, 'creator')) { // YES
        // Set Modifier
        $entity->creator = \models\User::extractID($user);

        // Set the Modification Date and Time
        $now = new \DateTime();
        $entity->date_created = $now->format('Y-m-d H:i:s');
      }
    }

    return $entity;
  }

  /**
   * If the Entity Manages it, this sets the User and Timestamp of when the
   * Entity was modified
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param \User $user User to set as the Objects Modifier
   * @return \api\model\AbstractEntity Modified Entity
   */
  protected function setModifier($entity, $user) {
    // Is the User Set?
    if (isset($user)) { // YES
      // Does this Entity Object Have a Modifier Property?
      if (property_exists($entity, 'modifier')) { // YES
        // Set Modifier
        $entity->modifier = \models\User::extractID($user);

        // Set the Modification Date and Time
        $now = new \DateTime();
        $entity->date_modified = $now->format('Y-m-d H:i:s');
      }
    }

    return $entity;
  }

  protected function _exists(\models\Container $parent, $name) {
    $found = \models\Container::findFirst([
        'conditions' => 'parent = :parent: and name = :name:',
        'bind' => [
          'parent' => $parent->id,
          'name' => $name
        ]
    ]);

    return !($found === FALSE);
  }

  /**
   * Save New or Modified Entities to the Database
   * 
   * @param \api\model\AbstractEntity $entity Entity to Save
   * @throws \Exception On failure to save 
   */
  protected function _persist($entity) {
    // Were we able to save the Entity?
    if ($entity->save() == false) { // NO      
      $messages = [];
      foreach ($entity->getMessages() as $message) {
        $messages[] = $message->getMessage() . '.';
      }

      throw new \Exception(implode('\n', $messages), 1);
    }
  }

  /**
   * 
   * @param \models\Container $folder
   */
  protected function _deleteChildren(\models\Container $folder) {
    /* (TODO) PROBLEM: How to Implement Deletion
     * In order to Delete a Folder we have to:
     * 1. Delete All Child Entries (recursively)
     * 2. Delete the Folder
     * 
     * Current Solution:
     * 1. For every Entry, use the Type Registry to Obtain the Function
     * capable of "DELETING" the Entry (i.e. call a static method to remove
     * the entry).
     * 
     * Wether the entry is deleted or just marked as deleted has to be decided.
     */
    return true;
  }

  /**
   * Delete Entry from the Database
   * 
   * @param \api\model\AbstractEntity $entity Entity to Delete
   * @throws \Exception On failure to delete 
   */
  protected function _delete($user, $entity) {
    // Call the Type
    $registry = $this->typeRegistry;

    /* Step-by-Step
     * - Verify if the User Can Perform the Action on the Entry?
     * -- YES: Action allowed
     * --- Call Special Entry Action to Delete the Linked Entity
     * --- Linked Entity Deleted?
     * ---- YES: Delete the Container Link
     * ---- NO: Abort - Throw Exception
     * -- NO: Abort - Throw Exception
     */

    // TODO Verify if the User Has Permissions to Delete the Entry
    return $registry->executeAction($entity->type, 'delete', [$entity], false);
  }

  protected function _buildFilter($type, $filter = null) {
    $condition = null;
    if (isset($type)) {
      $type = strtoupper($type);
      $type = str_split($type);
      $type = array_map(function($type) {
        return "'{$type}'";
      }, $type);

      if (count($type)) {
        return count($type) === 1 ? ['type' => ['=', $type[0]]] : ['type' => ['IN', $type]];
      }
    }

    return null;
  }

  protected function _buildOrderBy($sort, $default = null) {
    // Create Default OrderBy Condition
    $default = isset($default) ? $this->_sequenceOrderBy($default) : null;
    return isset($sort) ? $this->_sequenceOrderBy($default) : $default;
  }

  protected function _sequenceOrderBy($sequence) {
    // Explode the Sort Condition into Seperate Fields
    $fields = explode(',', $sequence);

    // Process Each Field Extracting Order By Condition
    $conditions = [];
    foreach ($fields as $field) {
      $field = Strings::nullOnEmpty($field);
      if (!isset($field)) {
        continue;
      }

      $ascending = $this->_orderBy($field);
      if (isset($field)) {
        $conditions[$field] = $ascending;
      }
    }

    return $conditions;
  }

  protected function _orderBy(&$field) {
    $ascending = true;
    $field = Strings::nullOnEmpty($field);
    if (isset($field)) {
      if ($field[0] === '!') {
        $ascending = false;
        $field = count($field) > 1 ? Strings::nullOnEmpty(substr($field, 1)) : null;
      }
    }

    return $ascending;
  }

}
