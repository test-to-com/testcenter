/* ************************************************************************
 
 TestCenter Client - Simplified Functional/User Acceptance Testing
 
 Copyright:
 2012-2013 Paulo Ferreira <pf at sourcenotes.org>
 
 License:
 AGPLv3: http://www.gnu.org/licenses/agpl.html
 See the LICENSE file in the project's top-level directory for details.
 
 Authors:
 * Paulo Ferreira
 
 ************************************************************************ */

/* ************************************************************************
 #require(qx.ui.form.Label)
 ************************************************************************ */
qx.Class.define("tc.forms.ProjectsForm", {
  extend: qx.ui.form.Form,
  /*
   *****************************************************************************
   EVENTS
   *****************************************************************************
   */
  events: {
    "cancel": "qx.event.type.Event",
    "ok": "qx.event.type.Event"
  },
  /*
   *****************************************************************************
   PROPERTIES
   *****************************************************************************
   */
  properties: {
    /** Project ID */
    id: {
      check: 'Integer',
      init: null,
      nullable: true
    },
    /** Project Name */
    name: {
      check: 'String',
      init: null,
      nullable: true
    }
  },
  /*
   *****************************************************************************
   CONSTRUCTOR / DESTRUCTOR
   *****************************************************************************
   */
  construct: function(id) {
    this.base(arguments);

    // Is Project ID Provided?
    if (id != null) {
      this.setId(id);
    }

    // FIELD: Projects List
    this.__listProjects = new qx.ui.form.List();
    this.__listProjects.setWidth(150);
    this.__listProjects.setHeight(6 * 20);
    this.__listProjects.add(new qx.ui.form.ListItem('Loading...', null, null));
    this.__loadList();

    // BUTTON: Confirmation
    this.__btnOk = new tc.buttons.ButtonOk();
    this.__btnOk.setEnabled(false);
    this.__btnOk.addState("default");
    this.__btnOk.addListener("execute", function(e) {
      this.__setProject();
    }, this);

    // Form Fields
    this.add(this.__listProjects, 'Projects');

    // Form Buttons
    this.addButton(this.__btnOk);
  },
  /*
   *****************************************************************************
   MEMBERS
   *****************************************************************************
   */
  members: {
    __listProjects: null,
    __btnOk: null,
    __bReady: false,
    __dataStore: null,
    /**
     * @lint ignoreUndefined(__TC_SERVICES_ROOT)
     */
    __loadList: function() {

      // Create the JSON Data Store
      var save_this = this;
      this.__dataStore = new qx.data.store.Json(__TC_SERVICES_ROOT + '/org/projects/list', {
        manipulateData: function(response) {
          var error = response.error;
          if (error.code) {
            save_this.error('TestCenter Request Returned an Error [' + error.code + ':' + error.message + ']');
            return null;
          } else {
            var data = [];
            var entries = response['return'];
            for (var i = 0; i < entries.length; i++) {
              var entry = new tc.data.model.ProjectEntry();
              entry.setId(entries[i].id);
              entry.setProject(entries[i].name);
              data.push(entry);
            }

            save_this.info('Processed [' + data.length + '] entries');
            return data;
          }
        }
      });
      this.__dataStore.addListener('error', function(e) {
        this.error('Request for Organization-Project List Failed.');
      }, this);
      this.__dataStore.addListener('loaded', function(e) {
        this.info('Request for Organization-Project List Succeeded.');

        /* Note that:
         * 1. the List Can Only be Created AFTER the Data Store Has Loaded, or
         * 2. We have to change the model, associated with the controller, after the JSON store has loaded.
         */
        this.__populateList();
      }, this);
    }, // FUNCTION: __loadList

    __populateList: function() {
      // Create a New Controller (Able to MAP the JSON Data Store to the List)
      var controller = new qx.data.controller.List(null, this.__listProjects);

      // create the delegate to change the bindings
      var delegate = {
        configureItem: function(item) {
          item.setPadding(3);
        },
        bindItem: function(controller, item, id) {
          controller.bindProperty("project", "label", null, item, id);
          controller.bindProperty("", "model", null, item, id);
        }
      };
      controller.setDelegate(delegate);

      // Get a Model for the JSON Data Store
      var model = this.__dataStore.getModel();

      // Clear the List Before Setting the New Model
      this.__listProjects.removeAll();

      // Change the Model (Data) for the List
      controller.setModel(model);

      // Enable the OK
      this.__btnOk.setEnabled(true);
    }, // FUNCTION: __displayList

    __setProject: function() {
      // Get the Current Selected Item
      var selection = this.__listProjects.getSelection();

      if (selection.length !== 1) {
        this.error('Invalid Selection.');
        this.fireEvent("cancel");
      } else {
        var selected = selection[0].getModel();
        this.setId(selected.getId());
        this.setName(selected.getProject());
        this.fireEvent("ok");
      }
    } // FUNCTION: __doSelection
  }
});


