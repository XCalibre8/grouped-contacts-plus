Ext.data.JsonP.dev_api_changes({"guide":"<!--\nCopyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.\nFor licensing, see LICENSE.md.\n-->\n\n\n<h1 id='dev_api_changes-section-api-changes-in-ckeditor-4'>API Changes in CKEditor 4</h1>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/dev_api_changes-section-overview'>Overview</a></li>\n<li>\n<a href='#!/guide/dev_api_changes-section-changes'>Changes</a></li></ol>\n</div>\n\n<p>This article describes CKEditor API changes between version 3 and version 4.</p>\n\n<h2 id='dev_api_changes-section-overview'>Overview</h2>\n\n<p>CKEditor 4 is almost totally backward compatible with CKEditor 3 (v3) in terms of the <a href=\"#!/api\">JavaScript API</a>. Upgrading should generally be hassle-free, although some API elements have changed. They will mostly impact custom plugins that extensively used the <a href=\"http://docs.cksource.com/ckeditor_api/\">CKEditor 3 API</a>.</p>\n\n<p>This page lists the most relevant changes and the proper way to port them to CKEditor 4.</p>\n\n<p class=\"tip\">\n    Please note that this list was created before the <strong>CKEditor 4.0 release</strong> and is not updated anymore. For further API changes see the <a href=\"http://ckeditor.com/whatsnew\">CKEditor changelog</a>.\n</p>\n\n\n<h2 id='dev_api_changes-section-changes'>Changes</h2>\n\n<p><code><a href=\"#!/api/CKEDITOR.event-method-fire\" rel=\"CKEDITOR.event-method-fire\" class=\"docClass\">CKEDITOR.event.fire</a></code> now returns <code>false</code> if the event was canceled (in v3 it returns <code>true</code>).</p>\n\n<p>The listener function sent to <code><a href=\"#!/api/CKEDITOR.event-method-on\" rel=\"CKEDITOR.event-method-on\" class=\"docClass\">CKEDITOR.event.on</a></code> can now return the Boolean <code>false</code> to cancel the event.</p>\n\n<hr />\n\n<p><code>CKEDITOR.config.corePlugins</code> is not needed anymore. The \"core plugins\" are now really part of the core, although their API signatures are left untouched:</p>\n\n<ul>\n<li><code>plugins/selection/plugin.js</code> => <code>core/selection.js</code>.\n The \"Select All\" feature originally provided by the <code>selection</code> plugin is now a standalone <a href=\"http://ckeditor.com/addon/selectall\">Select All</a> plugin.</li>\n<li><code>plugins/styles/plugin.js</code> => <code>core/style.js</code></li>\n<li><code>plugins/styles/styles/default.js</code> => <code>core/styles.js</code></li>\n<li><code>plugins/domiterator/plugin.js</code> => <code>core/dom/iterator.js</code></li>\n<li><code>plugins/htmldataprocessor/plugin.js</code> => <code>core/htmldataprocessor.js</code></li>\n</ul>\n\n\n<hr />\n\n<p>The editor will now support <strong>only one single skin per page</strong> (all editors will use the same skin).</p>\n\n<p>Because of the above, the following skin-related properties were moved global or deleted:</p>\n\n<ul>\n<li><code>CKEDITOR.skins</code> => <code><a href=\"#!/api/CKEDITOR.skin\" rel=\"CKEDITOR.skin\" class=\"docClass\">CKEDITOR.skin</a></code></li>\n<li><code>CKEDITOR.skins.add</code> => <strong>removed</strong></li>\n<li><code>CKEDITOR.skins.load( editor, partName, callback )</code> => <code><a href=\"#!/api/CKEDITOR.skin-method-loadPart\" rel=\"CKEDITOR.skin-method-loadPart\" class=\"docClass\">CKEDITOR.skin.loadPart</a>( partName, callback )</code></li>\n<li><code>CKEDITOR.editor#skinName</code> => <code><a href=\"#!/api/CKEDITOR.skin-property-name\" rel=\"CKEDITOR.skin-property-name\" class=\"docClass\">CKEDITOR.skin.name</a></code></li>\n<li><code>CKEDITOR.editor#skinPath</code> => <code><a href=\"#!/api/CKEDITOR.skin-method-getPath\" rel=\"CKEDITOR.skin-method-getPath\" class=\"docClass\">CKEDITOR.skin.getPath</a>( 'editor' )</code></li>\n<li><code>CKEDITOR.editor#skinClass</code> => <strong>removed</strong></li>\n</ul>\n\n\n<p>The skin definition file (<code>skin.js</code>) was simplified as follows:</p>\n\n<ul>\n<li>It no longer specifies the stylesheet file for the skin part. The editor will now expect the CSS file name to be the same as the part name, e.g. the <code>dialog</code> part will be requiring the <code>dialog.css</code> file in the skin directory.</li>\n<li>It no longer defines theme-related properties, e.g. dialog margins, combo grouping.</li>\n</ul>\n\n\n<hr />\n\n<p>The \"theme\" concept is removed, the DOM structure of the editor is now defined by creators or plugins individually, thus the <code>CKEDITOR.themes</code> namespace is removed.</p>\n\n<hr />\n\n<p><code><a href=\"#!/api/CKEDITOR.editor-method-setMode\" rel=\"CKEDITOR.editor-method-setMode\" class=\"docClass\">CKEDITOR.editor.setMode</a></code> and <code>CKEDITOR.editor#getMode</code> are features provided by the themedui creator only,\nwhich is not available in an instance created by the inline creator, where <code><a href=\"#!/api/CKEDITOR.editor-property-mode\" rel=\"CKEDITOR.editor-property-mode\" class=\"docClass\">CKEDITOR.editor.mode</a></code> property will be always <code>'wysiwyg'</code>.</p>\n\n<hr />\n\n<p><code>CKEDITOR.config.editingBlock</code> was removed, with the <code>editingBlock</code> being renewed as <code>editable</code>.</p>\n\n<hr />\n\n<p><code><a href=\"#!/api/CKEDITOR.focusManager\" rel=\"CKEDITOR.focusManager\" class=\"docClass\">CKEDITOR.focusManager</a></code> is now managing the overall \"active\" state of the entire editor\ninstead of just the editing block, so all editor UI parts (toolbar, dialog, panel)\nthat receive DOM focus will turn <code><a href=\"#!/api/CKEDITOR.focusManager-property-hasFocus\" rel=\"CKEDITOR.focusManager-property-hasFocus\" class=\"docClass\">CKEDITOR.focusManager.hasFocus</a></code> to <code>true</code>.</p>\n\n<p>Because of the above, <code><a href=\"#!/api/CKEDITOR.editable-property-hasFocus\" rel=\"CKEDITOR.editable-property-hasFocus\" class=\"docClass\">CKEDITOR.editable.hasFocus</a></code> should now be used instead for <code><a href=\"#!/api/CKEDITOR.focusManager-property-hasFocus\" rel=\"CKEDITOR.focusManager-property-hasFocus\" class=\"docClass\">CKEDITOR.focusManager.hasFocus</a></code> to check the focus state of the editing block.</p>\n\n<p>The <code>CKEDITOR.focusManager#forceBlur</code> method was removed.</p>\n\n<hr />\n\n<p><code>CKEDITOR.config.toolbar_Basic</code> and <code>CKEDITOR.config.toolbar_Full</code> were removed. Custom toolbar layout can be easily managed with <code><a href=\"#!/api/CKEDITOR.config-cfg-toolbarGroups\" rel=\"CKEDITOR.config-cfg-toolbarGroups\" class=\"docClass\">CKEDITOR.config.toolbarGroups</a></code>.</p>\n\n<hr />\n\n<p>The \"additional CSS\" feature provided by <code>CKEDITOR.editor#addCss</code> is now moved to the global <code><a href=\"#!/api/CKEDITOR-method-addCss\" rel=\"CKEDITOR-method-addCss\" class=\"docClass\">CKEDITOR.addCss</a></code>, with specified style rules applied <strong>document wide</strong>.</p>\n\n<p>Thus the proper way for a plugin to style its editable content is to call <code><a href=\"#!/api/CKEDITOR-method-addCss\" rel=\"CKEDITOR-method-addCss\" class=\"docClass\">CKEDITOR.addCss</a></code>\ninside of the plugin's <code>onLoad</code> function, rather than its <code>init</code> function in v3.</p>\n\n<hr />\n\n<p><code><a href=\"#!/api/CKEDITOR.env-property-version\" rel=\"CKEDITOR.env-property-version\" class=\"docClass\">CKEDITOR.env.version</a></code> now reflects the \"document mode\" in <strong>IE</strong> browsers. The following properties are <strong>deprecated</strong>:</p>\n\n<ul>\n<li><code><a href=\"#!/api/CKEDITOR.env-property-ie6Compat\" rel=\"CKEDITOR.env-property-ie6Compat\" class=\"docClass\">CKEDITOR.env.ie6Compat</a></code></li>\n<li><code><a href=\"#!/api/CKEDITOR.env-property-ie7Compat\" rel=\"CKEDITOR.env-property-ie7Compat\" class=\"docClass\">CKEDITOR.env.ie7Compat</a></code></li>\n<li><code><a href=\"#!/api/CKEDITOR.env-property-ie8Compat\" rel=\"CKEDITOR.env-property-ie8Compat\" class=\"docClass\">CKEDITOR.env.ie8Compat</a></code></li>\n<li><code><a href=\"#!/api/CKEDITOR.env-property-ie9Compat\" rel=\"CKEDITOR.env-property-ie9Compat\" class=\"docClass\">CKEDITOR.env.ie9Compat</a></code></li>\n</ul>\n\n\n<p>If you wanted to check for old IEs before IE9, instead of checking for each of the above properties as in v3:</p>\n\n<pre><code>if ( CKEDITOR.ie6Compat || CKEDITOR.ie7Compat || CKEDITOR.ie8Compat )\n</code></pre>\n\n<p>You should check in the following simpler way in v4:</p>\n\n<pre><code>if ( <a href=\"#!/api/CKEDITOR.env-property-version\" rel=\"CKEDITOR.env-property-version\" class=\"docClass\">CKEDITOR.env.version</a> &lt; 9 )\n</code></pre>\n\n<hr />\n\n<p>In plugin language files the usual <code><a href=\"#!/api/CKEDITOR.plugins-method-setLang\" rel=\"CKEDITOR.plugins-method-setLang\" class=\"docClass\">CKEDITOR.plugins.setLang</a></code> call now enforces\na namespace in the format of <code>editor.lang.pluginName</code>, which contains the provided\nlanguage entries.</p>\n\n<p>So, in v3 you had:</p>\n\n<pre><code><a href=\"#!/api/CKEDITOR.plugins-method-setLang\" rel=\"CKEDITOR.plugins-method-setLang\" class=\"docClass\">CKEDITOR.plugins.setLang</a>( 'myplugin', 'en', {\n    myplugin: {\n        title: 'My Plugin'\n    }\n} );\n</code></pre>\n\n<p>In v4 it should be changed to:</p>\n\n<pre><code><a href=\"#!/api/CKEDITOR.plugins-method-setLang\" rel=\"CKEDITOR.plugins-method-setLang\" class=\"docClass\">CKEDITOR.plugins.setLang</a>( 'myplugin', 'en', {\n    title: 'My Plugin'\n} );\n</code></pre>\n\n<p>In this way the entry will be available under <code>editor.lang.myplugin.title</code>.</p>\n\n<hr />\n\n<p>The <code><a href=\"#!/api/CKEDITOR.editor\" rel=\"CKEDITOR.editor\" class=\"docClass\">CKEDITOR.editor</a></code> constructor now receives two additional optional parameters (besides the configuration object)\nto simplify creator implementation:</p>\n\n<pre><code><a href=\"#!/api/CKEDITOR.editor\" rel=\"CKEDITOR.editor\" class=\"docClass\">CKEDITOR.editor</a>( config,\n    /** @type {<a href=\"#!/api/CKEDITOR.dom.element\" rel=\"CKEDITOR.dom.element\" class=\"docClass\">CKEDITOR.dom.element</a>} */ element,\n    /** @type {Number} */ elementMode );\n</code></pre>\n\n<hr />\n\n<p>CKEDITOR creators (<code><a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a></code>, <code><a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a></code> and <code><a href=\"#!/api/CKEDITOR-method-appendTo\" rel=\"CKEDITOR-method-appendTo\" class=\"docClass\">CKEDITOR.appendTo</a></code>)\nare no longer available within <code>ckeditor_basic.js</code> and are now provided by <code>core/creators/themedui.js</code>.</p>\n\n<hr />\n\n<p>The <code>iconOffset</code> property used in button definitions must now point to the\nexact offset position of the image in the icon file, instead of its logical order.</p>\n\n<p>For example, in v3 its value could be set to <code>2</code>. Now, in that same case,\nit should be set to <code>-32 (2 x -16)</code>.</p>\n\n<hr />\n\n<p>The default value for <code><a href=\"#!/api/CKEDITOR.config-cfg-toolbarCanCollapse\" rel=\"CKEDITOR.config-cfg-toolbarCanCollapse\" class=\"docClass\">CKEDITOR.config.toolbarCanCollapse</a></code> was changed to <code>false</code>.</p>\n\n<hr />\n\n<p>The default value for <code><a href=\"#!/api/CKEDITOR.config-cfg-docType\" rel=\"CKEDITOR.config-cfg-docType\" class=\"docClass\">CKEDITOR.config.docType</a></code> is now <code>'&lt;!DOCTYPE html&gt;'</code>,\nthe HTML5 doctype.</p>\n\n<hr />\n\n<p>The <code>CKEDITOR.editor#getThemeSpace</code> method was moved to <code>CKEDITOR.editor#space</code>.</p>\n\n<p>The <code>CKEDITOR.editor#themeSpace</code> event was replaced with <code>CKEDITOR.editor#uiSpace</code>.</p>\n\n<hr />\n\n<p>The <code><a href=\"#!/api/CKEDITOR.htmlParser.fragment-static-method-fromHtml\" rel=\"CKEDITOR.htmlParser.fragment-static-method-fromHtml\" class=\"docClass\">CKEDITOR.htmlParser.fragment.fromHtml</a>( fragmentHtml, fixForBody, /** @type {<a href=\"#!/api/CKEDITOR.htmlParser.element\" rel=\"CKEDITOR.htmlParser.element\" class=\"docClass\">CKEDITOR.htmlParser.element</a>} */ contextNode )</code> method changed signature to <code><a href=\"#!/api/CKEDITOR.htmlParser.fragment-static-method-fromHtml\" rel=\"CKEDITOR.htmlParser.fragment-static-method-fromHtml\" class=\"docClass\">CKEDITOR.htmlParser.fragment.fromHtml</a>( fragmentHtml, /** @type {<a href=\"#!/api/CKEDITOR.htmlParser.element\" rel=\"CKEDITOR.htmlParser.element\" class=\"docClass\">CKEDITOR.htmlParser.element</a>/String} */ parent, fixForBody )</code>.</p>\n\n<hr />\n\n<p>In the <code><a href=\"#!/api/CKEDITOR.editor-event-paste\" rel=\"CKEDITOR.editor-event-paste\" class=\"docClass\">CKEDITOR.editor.paste</a></code> event, the <code>evt.data.html</code> and <code>evt.data.text</code> properties are not available anymore.\nThey were replaced with <code>evt.data.dataValue</code> and <code>evt.data.type</code> to help identify the data type.</p>\n\n<hr />\n\n<p>The <code>CKEDITOR.replaceByClassEnabled</code> option is not available anymore. It is now enough to set <code><a href=\"#!/api/CKEDITOR-cfg-replaceClass\" rel=\"CKEDITOR-cfg-replaceClass\" class=\"docClass\">CKEDITOR.replaceClass</a></code> to empty/null to disable the auto-replace.</p>\n\n<hr />\n\n<p><code>CKEDITOR.dtd.$captionBlock</code> was removed. In order to check if one element can appear inside a table caption, use the DTD check instead:</p>\n\n<pre><code>assert.isTrue( !!CKEDITOR.dtd.caption[ element.getName() ] );\n</code></pre>\n","title":"API Changes from v3 to v4","meta_description":"API Changes between CKEditor 3 and CKEditor 4.","meta_keywords":"ckeditor, editor, upgrade, upgrading, api, changes, changelog, install, installation"});