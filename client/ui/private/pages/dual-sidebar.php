<!-- 
 --- copyright 2015 Paulo Ferreira <pf at sourcenotes.org>
 --- license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 -->
<body id="page_lorem" style="background-color: #F6E1D3">
  <!-- JS ON-READY SCRIPTS -->
  <?php $this->includeJSScripts('on-ready'); ?>
  <!-- SIDEBARS -->
  <?php include $this->templatesPath() . $this->setting('includes/lsidebar'); ?>
  <?php include $this->templatesPath() . $this->setting('includes/rsidebar'); ?>
  <div class="ui pusher">
    <div id="loader" class="ui active dimmer">
      <div class="ui text loader">Loading</div>
    </div>    
    <?php include $this->templatesPath() . $this->setting('includes/header'); ?>
    <div id="btn_menu" class="ui black huge launch right attached button" style="position: fixed; z-index: 100">
      <i class="icon list layout"></i>
      <span class="text" style="display: none;">Menu</span>
    </div>
    <div id="btn_context" class="ui black huge launch left attached button" style="position: fixed; right: 0px; z-index: 100">
      <span class="text" style="display: none; margin-right: 10px">Context</span>
      <i class="icon list layout"></i>
    </div>
    <div id="div_content" class="segment" style="margin: auto 100px;">
      <?php include $this->templatesPath() . $this->setting('includes/content'); ?>
    </div>
    <?php include $this->templatesPath() . $this->setting('includes/footer'); ?>
  </div>  
  <!-- JS BODY SCRIPTS -->
  <?php $this->includeJSScripts('body'); ?>
  <!-- JS ON-LOAD SCRIPTS -->
  <?php $this->includeJSScripts('on-load'); ?>
</body>
