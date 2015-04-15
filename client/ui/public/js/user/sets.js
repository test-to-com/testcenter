/* 
 * Copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 * License http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 */

/*******************************
 * INITIALZATION FUNCTIONS
 *******************************/

/**
 * 
 * @returns {undefined}
 */
function initialize() {
// Initialize the Session
  initialize_session();
  // Initialize Forms
  initialize_forms();
  $(document).on('testcenter.organization.change', function(event) {
    console.log('Captured Organization Change');
  });
  $(document).on('testcenter.project.change', function(event) {
    console.log('Captured Project Change');
    initialize_folders_list();
  });
  // Initialize Organizations
  initialize_dropdowns();

  // Initialize set Lists
  list_initialize(4);

  $('#folders').on('load-folder', function(event, id) {
    console.log('load-folder [' + id + ']');
    load_sets(id);
  });

  $('#sets').click(clicked_set);
  $('#sets').on('load-set', function(event, id) {
    console.log('load-set [' + id + ']');
    load_set(id);
  });

  // Remove Loader
  $('#loader').removeClass('active');
  $.contextMenu({
    selector: '#sets',
    reposition: false, // Force Menu Rebuild on Each Click
    build: contextmenu_sets
  });
}

function initialize_folders_list() {
  var $tree = $('#folders');
  // Has the Folders List been Initialized?
  if ($tree.data('initialized')) { // YES
    $tree.fancytree('destroy');
  }

// Initialize Tree
  $tree.fancytree({
    extensions: ['contextMenu'],
    source: [{title: 'ROOT', key: 'f:' + window.__session.project.container, folder: true, lazy: true, expandend: false}],
    lazyLoad: lazy_loader,
    selectMode: 1,
    postProcess: entities_to_nodes,
    activate: select_node,
    contextMenu: {
      menu: contextmenu_folder,
      actions: menu_folder_action
    }
  });
  // Mark Folders List as Initialized
  $tree.data('initialized', true);
}

function lazy_loader(event, data) {
  var key = data.node.key.split(':');
  if (key.length > 1) {
    data.result = {
      url: window.testcenter.services.url.service(['folders', 'list'], [key[1], 'F'], null),
      cache: false
    };
  }
}

function entities_to_nodes(event, data) {
  var response = data.response;
  var entity_set = response['return'];
  var nodes = [];
  var entities = entity_set.entities;
  var key_field = entity_set.__key;
  var display_field = entity_set.__display;
  var defaults = {
    folder: true,
    lazy: true
  };
  // Build Initial Folder List
  $.each(entities, function(i, entity) {
    var node = $.extend({}, defaults, {
      key: 'f:' + entity[key_field],
      title: entity[display_field]
    });
    nodes.push(node);
  });
  data.result = nodes;
}

function select_node(event, data) {
  console.log("Event [" + event.type + "] -  Node [" + data.node.key + ":" + data.node.title + "]");
  // If we have a Form Displayed Hide it
  if ($.isset(window.$form_displayed)) {
    form_hide(window.$form_displayed);
  }

// Set the Current Selected Node
  window.$selected_node = data.node;
  // fire event
  var id = data.node.key.split(':');
  $('#folders').trigger('load-folder', id[1]);
}

function clicked_set(event) {
  var element = event.target;
  var $set = $(element).closest('div.tc_set');
  if ($set.length) {
    var id = $set.attr('id').split(':');
    id = id.length > 1 ? id[1] : 'Missing ID';
    console.log('Clicked Set #' + id);
    $('#sets').trigger('load-set', id);
    return $set;
  } else {
    console.log("Didn't Click a Set!")
  }

// Stop Propogation
  return false;
}

function set_node(node) {
  return set_key(node.key);
}

function set_key(node_id) {
  var id = node_id.split(':');
  return id.length > 1 ? id[1] : null;
}

function contextmenu_folder(node) {
// All The Possible Actions on a Folder
  var items = {
    'rename': {'name': 'Rename', 'icon': 'edit'},
    'create': {'name': 'New Folder..', 'icon': 'add'},
    'seperator': '---------',
    'delete': {'name': 'Delete', 'icon': 'delete'}
  };
  /* NOTE: The Node we created as 'ROOT' is not the Root Node but the
   * 1st and only child of the Fancy Tree Root Node.
   */
  var isRoot = !node.isRootNode() && node.getParent().isRootNode();
  // Are we building the Context Menu for the Root Node?
  if (isRoot) { // YES: Disable Invalid Actions
    items['rename'].disabled = true;
    items['delete'].disabled = true;
  }

  return items;
}

function contextmenu_sets($trigger, event) {
  // List of Context Menu Items
  var items = {
    'edit': {'name': 'Edit', 'icon': 'edit'},
    'delete': {'name': 'Delete', 'icon': 'delete'},
    'seperator': '---------',
    'create': {'name': 'New Set..', 'icon': 'add'}
  };
  // Did we click on a set?
  var $set = clicked_set(event);
  if ($set) { // YES
    var id = $set.attr('id').split(':');
    return {
      'callback': function(key, options) {
        menu_set_action(key === 'create' ? null : $set, key, options);
      },
      'items': items
    };
  } else { // NO - Only Option is Create
    items['edit'].disabled = true;
    items['delete'].disabled = true;
    return {
      'callback': function(key, options) {
        menu_set_action(null, key, options);
      },
      'items': items
    };
  }
}

function menu_folder_action(node, action, options) {
  console.log('Selected action "' + action + '" on node ' + node.key);
  var $form = $('#form_' + action + '_folder');
  if ($form.length) {
    form_show($form);
  } else {
    console.log('Missing Form for action[' + action + ']');
  }
}

function menu_set_action($node, action, options) {
  if ($node) {
    console.log('Selected action "' + action + '" on Set [' + $node.attr('id') + ']');
    window.$selected_set = $node;
  } else {
    console.log('Selected action "' + action + '"');
  }
  var $form = $('#form_' + action + '_set');
  if ($form.length) {
    form_show($form);
  } else {
    console.log('Missing Form for action[' + action + ']');
  }
}

function load_sets(folder_id) {
  var $list = $('#sets');
  // Flag the DIV as Loading
  $list.addClass('loading');
  // Remove Existing Sets from the List
  $list.empty();
  // Load the Sets into the Folder
  testcenter.services.call(['folders', 'list'], [folder_id, 'S'], null, {
    call_ok: function(entity_set) {

      var nodes = [];
      var entities = entity_set.entities;
      var key_field = 'link';
      var display_field = entity_set.__display;
      var $template = $.templates('<div id="t:{{:' + key_field + '}}" class="tc_set aligned column"><i class="file icon"></i><div>{{:' + display_field + '}}</div></div>');
      // Build the Nodes List
      $.each(entities, function(i, entity) {
        var node = $template.render(entity);
        nodes.push(node);
      });
      // Do we have Nodes?
      if (nodes.length) { // YES
        // Build the Rows of Sets
        var n_columns = list_getColumns($list);
        var n_rows = Math.floor(nodes.length / n_columns) + 1;
        var offset, rows = [], row_nodes;
        for (var i = 0; i < n_rows; i++) {
          offset = i * n_columns;
          row_nodes = nodes.slice(offset, offset + n_columns);
          rows.push('<div class="centered row">' + row_nodes.join('') + '</div>');
        }

        // Set the Set List Content
        $list.html(rows.join(''));
      } else {
        $list.html('<div class="aligned column">Empty Folder</div>');
      }

      // Finished Loading
      $list.removeClass('loading');
    },
    call_nok: function(code, message) {
      $list.html('<div class="tc_set aligned column">Error [' + code + ':' + message + '] loading set list</div>');
    }
  });
}

function load_set(id) {
  var $form = $('#form_update_set');
  // Clear the Form
  form_reset($form);
  // Flag the DIV as Loading
  $form.addClass('loading');
  // Load the Sets into the Folder
  testcenter.services.call(['set', 'read'], id, null, {
    call_ok: function(set) {
      // Save the Set Data Information with the Form
      form_load($form, set, 'set');
      $form.data('set', set);
    },
    call_nok: function(code, message) {
      form_disable($form, message);
      // Finished Loading
      $form.removeClass('loading');
      $form.data('set', null);
    }
  });

  /*
   // Load the Set Tests
   testcenter.services.call(['steps', 'list'], id, null, {
   call_ok: function(steps) {
   var $list = $('#list_steps');
   
   // Clear the Current List
   $list.empty();
   
   var entities = steps['entities'];
   var entity = null;
   var $entity = null;
   var first = true;
   var last = false;
   
   for (var i = 0, count = entities.length; i < count; ++i) {
   last = i == (count - 1);
   entity = entities[i];
   $entity = create_step(id, entity.sequence, entity[steps.__display], entity.description, first, last);
   $list.append($entity);
   first = false;
   }
   
   // Finished Loading
   $form.removeClass('loading');
   },
   call_nok: function(code, code) {
   console.log('ERROR: Failed to List Steps [' + code + ':' + code + ']');
   }
   });
   */
}

function create_step_button(name, icon) {
  var $button = $('<div name="' + name + '" class="ui button"><i class="' + icon + ' icon"></i></div>')

  $button.click(function(event) {
    var $button = $(event.target).closest('.button');
    var $step = $button.closest('.item');
    if ($step.length) {
      var id = step_key($step.attr('id'));
      do_click_step($button.attr('name'), id[0], id[1]);
    }
  });

  return $button;
}

function add_button_after($buttons, $button, list_ltr) {
  var $list = $buttons.find('> .button');

  // Do we have atleast one Button?
  if ($list.length === 0) { // NO: Simply Add
    $buttons.after($button)
  } else {
    if (!$.isArray(list_ltr)) {
      list_ltr = [list_ltr];
    }

    var matched = -1, button;
    for (var i = 0, j = 0; (i < $buttons.lenght) && (j < list_ltr.length); i++) {
      button = $buttons.get(i);
      if (button.name === list_ltr[j]) {
        matched = i;
        j++;
      }
    }

    if (matched >= 0) {
      $($buttons.get(matched)).after($button);
    } else {
      $buttons.prepend($button);
    }
  }
}

function step_key(step_id) {
  step_id = $.strings.nullOnEmpty(step_id);
  if ($.isset(step_id)) {
    var elements = step_id.split('-');
    if (elements.length >= 3) {
      var test = parseInt(elements[1]);
      var sequence = parseInt(elements[2]);
      return [test, sequence];
    }
  }

  return null;
}

function add_step_button(button, $item) {
  var $buttons = $item.find('.extra > .buttons');

  var $button = $buttons.find('[name="' + button + '"]');
  if ($button.length === 0) {
    var id = step_key($item.attr('id'));
    switch (button) {
      case 'up':
        $button = create_step_button('up', 'arrow up');
        $buttons.prepend($button);
        break;
      case 'edit':
        $button = create_step_button('edit', 'write');
        add_button_after($buttons, $button, 'up');
        break;
      case 'new':
        $button = create_step_button('new', 'plus');
        add_button_after($buttons, $button, ['up', 'edit']);
        break;
      case 'delete':
        $button = create_step_button('delete', 'erase');
        add_button_after($buttons, $button, ['up', 'edit', 'new']);
        break;
      case 'down':
        $button = create_step_button('down', 'arrow down');
        $buttons.append($button);
        break;
    }
  }
}

function remove_step_button(button, $item) {
  var $buttons = $item.find('.extra > .buttons');

  var $button = $buttons.find('[name="' + button + '"]');
  if ($button.length) {
    $button.remove();
    return true;
  }

  return false;
}

function move_step_up(test, sequence, new_sequence) {
  if (sequence > new_sequence) {
    var $steps = $('#list_steps > .item');

    var was_top = false, to_bottom = false;
    var id = 'step-' + test + '-' + sequence;
    var $step = null, $prev = null;
    for (var i = 0; i < $steps.length; ++i) {
      if ($steps.get(i).id === id) {
        to_bottom = i === 1;
        was_top = i === ($steps.length - 1);
        $step = $($steps.get(i));
        break;
      }
    }
    $prev = $($steps.get(i - 1));

    // Detach and Modify the Step
    $step.detach();
    $step.attr('id', 'step-' + test + '-' + new_sequence);

    if (to_bottom) {
      add_step_button('up', $prev);
      remove_step_button('up', $step);
    }

    if (was_top) {
      remove_step_button('down', $prev);
      add_step_button('down', $step);
    }

    // Re-attach Step
    $prev.before($step);
    return true;
  }

  return false;
}

function move_step_down(test, sequence, new_sequence) {
  if (sequence < new_sequence) {
    var $steps = $('#list_steps > .item');

    var was_bottom = false, to_top = false;
    var id = 'step-' + test + '-' + sequence;
    var $step = null, $next = null;
    for (var i = 0; i < $steps.length; ++i) {
      if ($steps.get(i).id === id) {
        was_bottom = i === 0;
        to_top = i === ($steps.length - 2);
        $step = $($steps.get(i));
        $next = $($steps.get(i + 1));
        break;
      }
    }

    // Detach Step
    $step.detach();
    $step.attr('id', 'step-' + test + '-' + new_sequence);

    if (was_bottom) {
      add_step_button('up', $step);
      remove_step_button('up', $next);
    }

    if (to_top) {
      remove_step_button('down', $step);
      add_step_button('down', $next);
    }

    // Re-attach Step
    $next.after($step);
    return true;
  }

  return false;
}

function get_step(test, sequence) {
  var $step = $('#list_steps > #step-' + test + '-' + sequence);
  return $step.length ? $step : null;
}

function delete_step(test, sequence) {
  var $step = $('#list_steps > #step-' + test + '-' + sequence);
  if ($step.length) {
    $step.remove();

    var $items = $('#list_steps > item');
    if ($items.length) {
      var $first, $last;
      if ($items.length === 1) {
        $first = $last = $items.first();
      } else {
        $first = $items.first();
        $last = $items.last();
      }
      remove_step_button('up', $items.first())
      remove_step_button('down', $items.last());
    }
    return true;
  }

  return false;
}

function do_click_step(action, test, sequence) {
  console.log('Do [' + action + '] on Step [' + test + ':' + sequence + ']');
  switch (action) {
    case 'up':
      // Load the Test Steps
      testcenter.services.call(['step', 'move', 'up'], [test, sequence], null, {
        call_ok: function(step) {
          move_step_up(test, sequence, step['sequence']);
        },
        call_nok: function(code, message) {
          console.log('ERROR: Failed to Move Step [' + code + ':' + message + ']');
        }
      });
      break;
    case 'down':
      // Load the Test Steps
      testcenter.services.call(['step', 'move', 'down'], [test, sequence], null, {
        call_ok: function(step) {
          move_step_down(test, sequence, step['sequence']);
        },
        call_nok: function(code, message) {
          console.log('ERROR: Failed to Move Step [' + code + ':' + message + ']');
        }
      });
      break;
    case 'new':
      // Display Create Step Form
      var $form = $('#form_create_step');
      $form.data('set', test);
      $form.data('after', sequence);
      form_show($form);
      break;
    case 'edit':
      // Display Create Step Form
      var $form = $('#form_edit_step');
      $form.data('set', test);
      $form.data('step', sequence);
      $form.addClass('loading');
      form_show($form);

      // Load the Test Steps
      testcenter.services.call(['step', 'read'], [test, sequence], null, {
        call_ok: function(step) {
          form_load($form, step, 'step');
        },
        call_nok: function(code, message) {
          console.log('ERROR: Failed to Delete Step [' + code + ':' + message + ']');
        }
      });
      break;
    case 'delete':
      // Load the Test Steps
      testcenter.services.call(['step', 'delete'], [test, sequence], null, {
        call_ok: function(result) {
          if (result) {
            delete_step(test, sequence);
          }
        },
        call_nok: function(code, message) {
          console.log('ERROR: Failed to Delete Step [' + code + ':' + message + ']');
        }
      });
      break;
  }
}

function create_step(test, sequence, title, description, first, last) {
  description = $.isset(description) ? description : 'No Step Description';

  var $step = $('<div id="step-' + test + '-' + sequence + '" class="item">' +
    '<div class="tc_step ui tiny image">' +
    '<i class="inverted circular big terminal icon"></i>' +
    '</div>' +
    '<div class="middle aligned content">' +
    '<div class="header">' + title + '</div>' +
    '<div class="description">' +
    '<p>' + description + '</p>' +
    '</div>' +
    '<div class="extra">' +
    '<div class="ui right floated buttons">' +
    '</div>' +
    '</div>' +
    '</div>' +
    '</div>');

  var $buttons = $step.find('.extra .buttons');

  if (!first) {
    $buttons.append(create_step_button('up', 'arrow up'));
  }

  $buttons.append(create_step_button('edit', 'write'));
  $buttons.append(create_step_button('new', 'plus'));
  $buttons.append(create_step_button('delete', 'erase').addClass('negative'));

  if (!last) {
    $buttons.append(create_step_button('down', 'arrow down'));
  }

  return $step;
}

function list_initialize(columns) {
  var $list = $('#sets');
  columns = $.isset(columns) &&
    $.isNumeric(columns) &&
    (columns > 0) ? Math.floor(columns) : 4;
  if ($list.length) {
    $list.data('list.columns', columns);
  }

  return $list;
}

function list_clear() {
  var $list = $('#sets');
  $list.empty();
  return $list;
}

function list_getColumns($list) {
  $list = $list ? $list : $('#sets');
  return $list.length ? $list.data('list.columns') : 0;
}

function list_setColumns(columns) {
  var $list = $('#sets');
  if ($list.length && $.isset(columns) && $.isNumeric(columns) && (columns > 0)) {
    $list.data('list.columns', Math.floor(columns));
  }
  return $list;
}

function list_getRows() {
  var $list = $('#sets');
  if ($list.length) {
    return $list.find('.row').length;
  }
  return 0;
}

function list_getRow(row) {
  var $list = $('#sets > .row');
  if ($.isset(row) && $.isNumeric(row) && ((row >= 0) && (row < $list.length))) {
    return $($list.get(row));
  }

  return null;
}

function list_countNodesInRow(row) {
  var $row = null;
  if ($.isset(row)) {
    if ($.isNumeric(row)) {
      $row = list_getRow(row);
    } else if (row instanceof jQuery) {
      $row = row;
    }
  }

  return $row ? $row.children().length : 0;
}

function list_appendRow($row) {
  var $list = $('#sets');
  if ($list.length) {
    $list.append($row);
    return $list;
  }

  return null;
}

function list_appendToRow($row, $node) {
  $row.append($node);
  return $row;
}

function list_createNode(id, name) {
  return  $('<div id="t:' + id + '" class="tc_set aligned column"><i class="file icon"></i><div>' + name + '</div></div>');
}
function list_createRow() {
  return $('<div class="centered row"></div>');
}

function list_appendNode(id, name) {
  // Create Test Node
  var $node = list_createNode(id, name);

  // Do we have any rows?
  var rows = list_getRows();
  if (rows) { // YES
    var columns = list_getColumns();
    var $last_row = list_getRow(rows - 1);
    if (list_countNodesInRow($last_row) < columns) {
      list_appendToRow($last_row, $node);
      return $node;
    }
  } else { // NO: Make Sure the List is Clear
    list_clear();
  }
  // ELSE: NO ROWS or LAST ROW IS FULL
  return list_appendRow(list_appendToRow(list_createRow(), $node));
}

/*******************************
 * CREATE FOLDER FORM
 *******************************/

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_create_folder($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_rename_folder($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_delete_folder($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_create_set($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_update_set($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_create_step($form) {
  __initialize_form($form);
}

/**
 * 
 * @param {type} $form
 * @returns {undefined}
 */
function __initialize_form_edit_step($form) {
  __initialize_form($form);
}

function __initialize_form($form) {
  // Link Buttons to Handlers
  $form.find(".button").each(function() {
    var $this = $(this);
    var name = $.strings.nullOnEmpty($this.attr('id'));
    if (name !== null) {
      var handler = '__' + name;
      if (window.hasOwnProperty(handler) && $.isFunction(window[handler])) {
        $this.click($form, window[handler]);
      }
    }
  });
}
/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_create_folder(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  /* Load the Node if Not Already Loaded.
   * Why? 
   * Beacuse if we add a child node to a node, that hasn't been loaded,
   * FancyTree will assume the node is loaded, and no the ajax request to load.
   */
  var node = window.$selected_node;
  node.load();
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Folder Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_folder,
    onFailure: function() {
      alert("Create Folder: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_rename_folder(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Folder Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_rename_folder,
    onFailure: function() {
      alert("Create Folder: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_delete_folder(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  __do_delete_folder();
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_create_set(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'set-name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Set Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_set,
    onFailure: function() {
      alert("Create Set: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_update_set(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Test Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_update_set,
    onFailure: function() {
      alert("Update Test: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_create_step(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      test: {
        identifier: 'test',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Test ID'
          }
        ]
      },
      title: {
        identifier: 'title',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Step Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_create_step,
    onFailure: function() {
      alert("Create Test: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_update_step(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  form_submit($form,
    {
      name: {
        identifier: 'name',
        rules: [
          {
            type: 'empty',
            prompt: 'Missing Step Name'
          }
        ]
      }
    }, {
    revalidate: false,
    onSuccess: __do_update_step,
    onFailure: function() {
      alert("Update Test: Missing or Invalid Fields");
    }
  });
}

/**
 * 
 * @param {type} event
 * @returns {undefined}
 */
function __button_cancel(event) {
  // Form jQuery Object is Store in the Event Data
  var $form = event.data;
  // Hide the Form
  form_hide($form);
}


function __do_create_folder() {
  var $form = $('#form_create_folder');
  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });
  var node = window.$selected_node;
  var key = node.key.split(':');
  console.log('Create Child Node [' + values.name + '] under Parent Node [' + key[1] + ':' + node.title + ']');
  testcenter.services.call(['folder', 'create'], [key[1], values.name], null, {
    call_ok: function(entity) {
      var defaults = {
        folder: true,
        lazy: true
      };
      var key_field = entity.__key;
      var display_field = entity.__display;
      var child = $.extend({}, defaults, {
        key: 'f:' + entity[key_field],
        title: entity[display_field]
      });
      node.addChildren(child);
      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });
  return values;
}

function __do_rename_folder() {
  var $form = $('#form_rename_folder');
  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });
  var node = window.$selected_node;
  var key = node.key.split(':');
  console.log('Rename Folder [' + key[1] + ':' + node.title + '] to [' + values.name + ']');
  testcenter.services.call(['folder', 'rename'], [key[1], values.name], null, {
    call_ok: function(entity) {
      // Change the Nodes Label      
      var display_field = entity.__display;
      node.setTitle(entity[display_field]);
      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });
  return values;
}

function __do_delete_folder() {
  var $form = $('#form_delete_folder');
  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });
  var node = window.$selected_node;
  var key = node.key.split(':');
  console.log('Delete Folder [' + key[1] + ':' + node.title + ']');
  testcenter.services.call(['folder', 'delete'], key[1], null, {
    call_ok: function(entity) {
      // Delete the Child Folder
      var parent = node.getParent();
      parent.removeChild(node);
      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });
  return values;
}

function __do_create_set() {
  var $form = $('#form_create_set');
  // Extract Field Values
  var values = {};
  $form.find('.field input').each(function() {
    values[this.name] = $(this).val();
  });
  // Build Route Parameters
  var node = window.$selected_node;
  var params = [values['set-name']];
  if (node) {
    var key = node.key.split(':');
    params.push(key[1]);
    console.log('Create Child Node [' + values.name + '] under Parent Node [' + key[1] + ':' + node.title + ']');
  } else {
    console.log('Create Child Node [' + values.name + '] under Project Root');
  }

  testcenter.services.call(['set', 'create'], params, null, {
    call_ok: function(entity) {
      var key_field = entity.__key;
      var display_field = entity.__display;
      list_appendNode(entity[key_field], entity[display_field]);

      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });
  return values;
}

function __do_update_set() {
  var $form = $('#form_update_set');

  // Get the Set Associated with the Form
  var set = $form.data('set');

  // Extract Field Values
  var values = {};
  $form.find('.field > :input').each(function() {
    var name = this.name;
    if ($.isset(name)) {
      // Remove Leading 'set'
      var property = name.split('-');
      property = (property.length > 1) ? property[1] : property[0];
      if ($.inArray(property, set.__fields) >= 0) {
        if (property !== set.__key) {
          values['set:' + property] = $(this).val();
        }
      }
    }
  });

  testcenter.services.call(['set', 'update'], set[set.__key], values, {
    type: 'POST',
    call_ok: function(entity) {
      console.log("Updated Set [" + set[set.__key] + "]");
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

function __do_create_step() {
  var $form = $('#form_create_step');

  // Extract Field Values
  var values = {};
  $form.find('.field > :input').each(function() {
    values[this.name] = $(this).val();
  });

  // Get the Test Associated with the Form
  var test = $form.data('test');
  var after = $form.data('after');

  // Create Service to Call and Parameters
  var service, params;
  if ($.isset(after)) {
    service = ['step', 'create', 'after'];
    params = [test, after, values['title']];
  } else {
    service = ['step', 'create'];
    params = [test, values['title']];
  }

  testcenter.services.call(service, params, null, {
    call_ok: function(entity) {
      var $list = $('#list_steps');
      var first = false, last = false;
      if ($.isset(after)) {
        var $previous = $list.find('#step-' + test + '-' + after);
        last = $previous.next().length === 0;
        var $step = create_step(entity.test, entity.sequence, entity.title, entity.description, false, last);
        $previous.after($step);
      } else {
        first = $list.find('> .item').length === 0;
        var $step = create_step(entity.test, entity.sequence, entity.title, entity.description, first, true);
        $list.append($step);
      }

      // Hide the Form 
      form_hide($form);
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });
  return values;
}

function __do_update_step() {
  var $form = $('#form_edit_step');

  // Get the Test Associated with the Form
  var test = $form.data('test');
  var step = $form.data('step');

  // Extract Field Values
  var values = {};
  $form.find('.field > :input').each(function() {
    var name = this.name;
    if ($.isset(name)) {
      // Remove Leading 'test'
      var property = name.split('-');
      property = (property.length > 1) ? property[1] : null;
      if ($.isset(property)) {
        values['step:' + property] = $(this).val();
      }
    }
  });

  testcenter.services.call(['step', 'update'], [test, step], values, {
    type: 'POST',
    call_ok: function(entity) {
      var $step = $('#list_steps > #step-' + entity.test + '-' + entity.sequence);
      $step.find('.header').html(entity.title);
      $step.find('.description').html($.isset(entity.description) ? entity.description : "No Descrption Set");
      // Hide the Form 
      form_hide($form);
      // Log
      console.log("Updated Step [" + test + ":" + step + "]");
    },
    call_nok: function(code, message) {
      form_show_errors($form, message);
    }
  });

  return values;
}

/*******************************
 * HELPER FUNCTIONS
 *******************************/

function form_show($form) {
  if (window.hasOwnProperty('__session')) {
    // Do we have a form with th given id?
    if ($form.length) { // YES
      form_center($form);
      $form.removeClass('hidden');
      window.$form_displayed = $form;
    } else {
      // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
      alert("No Form with the id [" + id + "]");
    }
  } else {
    // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
    alert("Failed Initializing Comunication with Server.");
  }
}

function form_hide($form) {
  if (window.hasOwnProperty('__session')) {
    // Do we have a form with th given id?
    if ($form.length) { // YES
      // Hide the Form
      $form.addClass('hidden');
      window.$form_displayed = null;
      // Reset the Form
      form_reset($form);
    } else {
      // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
      alert("No Form with the id [" + id + "]");
    }
  } else {
    // TODO : Display Message in a More Friendly Fashion (Toaster, etc.)
    alert("Failed Initializing Comunication with Server.");
  }
}

function form_load($form, values, prefix) {
  $form.find('.field > :input').each(function() {
    var field = field_key(this.name, prefix);

    var value = $.objects.get(field, values);
    if (value !== null) {
      $this = $(this);
      if (this.type === 'checkbox') {
        value = (value === 'true') ? true : false;
        $this.prop('checked', value);
      } else if ((this.type === 'hidden') && $this.parent().hasClass('dropdown')) {
        $this.parent().dropdown('set selected', value).dropdown('set value', value);
      } else {
        $this.val(value);
      }
    }
  });

  // Remove the Loading Symbol
  $form.removeClass('loading');
}

function field_key(name, prefix) {
  // Cleanup Variables
  name = $.strings.nullOnEmpty(name);
  if ($.isset(name)) {
    prefix = $.strings.nullOnEmpty(prefix);
    if ($.isset(prefix)) {
      prefix += '-';
      if (name.slice(0, prefix.length) === prefix) {
        name = name.slice(prefix.length);
      }
    }

    name = name.replace(/-/g, '.');
  }

  return name;
}

function create_navigator(selector) {
  var $container = $(selector);

  if ($container.length) {
    $container.navigator({
      title: 'PAGE:USER:TESTS:TEST_MANAGER',
      panes: {
        folders: {
          order: 1,
          size: 4,
          creator: function($pane, settings) {
            $pane.foldertree(settings);
          },
          settings: {
            title: 'PAGE:USER:TESTS:FOLDERS',
            root: {
              id: 4,
              title: 'ROOT'
            },
            callbacks: {
              data_url: function(id) {
                return window.testcenter.services.url.service(['folders', 'list'], [id, 'F'], null);
              },
              data_to_nodes: null,
              'context-menu': contextmenu_folder
            }
          }
        },
        items: {
          order: 2,
          size: 12,
          creator: function($pane, settings) {
            $pane.gridlist(settings);
          },
          settings: {
            title: 'PAGE:USER:TESTS:TESTS',
            columns: 4,
            callback: {
              loader: null,
              post_process: null,
              'context-menu': contextmenu_sets
            }
          }
        }
      }
    });
  } else {
    console.log('Container for Navigator not Found.');
  }
}

/* TODO:
 * 1. We need to be able to handle organization change event (i.e. the event is
 * fired when the organization is changed in the dropdown).
 * 
 * POSSIBLE SOLUTION:
 * 1. On Capturing the event we could do:
 * a) Destroy/Clear the Tests Navigation Page.
 * b) Place Dimmer over the Navigation Pane.
 * 
 * 2. We need to be able to handle changge of project (i.e. the event is
 * fired when the project is changed in the dropdown).
 * 
 * POSSIBLE SOLUTION:
 * 1. On Capturing the event we could do:
 * a) Clear the Test Pane Window (Replace it with the filler it had at the
 * beginning).
 * b) Create/Reset the fancytree.
 * 
 * 3. Currently, when the Test Navigator is created, no folder is selected
 * (i.e. nothing is loaded in the tests part of the window), therefore it
 * does not make sense to display the test context-menu since we can't really
 * do anything.
 * 
 * POSSIBLE SOLUTIONS:
 * 1. When creating the test navigator, automatically select the ROOT FOLDER
 * for the project.
 * 2. Don't display / create the Context Menun until the root folder has been
 * selected.
 */