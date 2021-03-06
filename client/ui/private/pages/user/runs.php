<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
-->
<!--
 --- FORMS
-->
<!-- CREATE FOLDER FORM -->
<div id="form_create_folder" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:CREATE") ?></h4>
  <div class="required field">
    <input name="name" placeholder="<?php echo _("FIELD:PLACEHOLDER:FOLDER:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_folder" class="ui positive button"><?php echo _("BUTTON:CREATE") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>    
<!-- RENAME FOLDER FORM -->
<div id="form_rename_folder" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:RENAME") ?></h4>
  <div class="required field">
    <input name="name" placeholder="<?php echo _("FIELD:PLACEHOLDER:FOLDER:NEW-NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_rename_folder" class="ui positive button"><?php echo _("BUTTON:RENAME") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- DELETE FOLDER FORM -->
<div id="form_delete_folder" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:FOLDER:DELETE") ?></h4>
  <div class="ui header">
    <?php echo _("TEXT:QUESTION:FOLDER:DELETE:CONFIRMATION") ?>
  </div>
  <div class="ui buttons">
    <div id="button_delete_folder" class="ui negative button"><?php echo _("BUTTON:DELETE") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui positive button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- CREATE RUN FORM -->
<div id="form_create_run" class="tc ui form hidden">
  <h4 class="ui dividing header"><?php echo _("FORM:TITLE:RUN:CREATE") ?></h4>
  <div class="required field">
    <input name="run-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:RUN:NAME") ?>" type="text">
  </div>
  <div class="ui buttons">
    <div id="button_create_run" class="ui positive button"><?php echo _("BUTTON:CREATE") ?></div>
    <div class="<?php echo _("HELPER:CLASS:OR") ?>"></div>
    <div id="button_cancel" class="ui negative button"><?php echo _("BUTTON:CANCEL") ?></div>
  </div>
  <div class="ui error message"></div>
</div>      
<!-- MAIN PAGE -->
<div id="navigator">
  <h3 class="ui top attached centered inverted header">
    <?php echo _("PAGE:SECTION:RUNS:NAVIGATOR") ?>
  </h3>
  <div class="ui attached segment">
    <div class="ui divided grid">
      <div id="folders" class="four wide column">
        <h3 class="ui header centered inverted" style="background-color: black">
          <?php echo _("NAVIGATOR:PANE:FOLDERS") ?>
        </h3>
      </div>
      <div class="twelve wide column">
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("NAVIGATOR:PANE:SETS") ?>
        </h3>
        <div id="list_sets"></div>
        <h3 class="ui header centered inverted" style="background-color: blue">
          <?php echo _("NAVIGATOR:PANE:RUNS") ?>
        </h3>
        <div id="list_runs"></div>
      </div>
    </div>
  </div>
  <h3 class="ui centered attached inverted header">
    <?php echo _("PAGE:SECTION:RUN:DETAILS") ?>
  </h3>
  <div class="ui attached segment">
    <div id="form_update_run" class="ui form">
      <div class="ui error message"></div>
      <h4 class="ui dividing header"><?php echo _("PAGE:USER:RUNS:RUN:DETAILS") ?></h4>
      <div class="required field">
        <label><?php echo _("FIELD:TITLE:RUN:NAME") ?></label>
        <input name="run-name" placeholder="<?php echo _("FIELD:PLACEHOLDER:RUN:NAME") ?>" type="text">
      </div>
      <div class="field">
        <label><?php echo _("FIELD:TITLE:RUN:DESCRIPTION") ?></label>
        <textarea name="run-description" placeholder="<?php echo _("FIELD:PLACEHOLDER:RUN:DESCRIPTION") ?>" type="text"></textarea>
      </div>
      <div id="button_update_run" class="ui positive button"><?php echo _("BUTTON:UPDATE") ?></div>
    </div>
  </div>
</div>
