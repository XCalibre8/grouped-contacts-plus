Ext.data.JsonP.dev_example_setups({"guide":"<!--\nCopyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.\nFor licensing, see LICENSE.md.\n-->\n\n\n<h1 id='dev_example_setups-section-example-ckeditor-setups'>Example CKEditor Setups</h1>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/dev_example_setups-section-article-editor'>Article Editor</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-document-editor'>Document Editor</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-inline-editor'>Inline Editor</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-developer-site-editor'>Developer Site Editor</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-drag-%26amp%3B-drop'>Drag &amp; Drop</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-accessibility-checker'>Accessibility Checker</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-hints-for-production-environment-%28all-setups%29'>Hints for Production Environment (All Setups)</a><ol>\n<li>\n<a href='#!/guide/dev_example_setups-section-manual-download-and-installation-of-additional-plugins-%28not-recommended%29'>Manual Download and Installation of Additional Plugins (Not Recommended)</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-using-cdn'>Using CDN</a></li>\n<li>\n<a href='#!/guide/dev_example_setups-section-using-builder-%28recommended%29'>Using Builder (Recommended)</a></li></ol></ol>\n</div>\n\n<p>This article is a detailed explanation of sample editor configurations shown on the <a href=\"http://ckeditor.com\">CKEditor Homepage</a> and <a href=\"http://ckeditor.com/features\">CKEditor Features page</a>.</p>\n\n<h2 id='dev_example_setups-section-article-editor'>Article Editor</h2>\n\n<p>The Article Editor demo showcases an editor designed mainly for writing web text content like blog posts, articles etc.</p>\n\n<p class=\"tip\">\n  Visit the <a href=\"https://github.com/ckeditor/ckeditor-docs-samples/tree/master/editors\">ckeditor-docs-samples</a> GitHub repository to learn more about this configuration.\n</p>\n\n\n<p>The Article Editor is based on the <a href=\"http://ckeditor.com/download\">Standard package</a> with a few modifications:</p>\n\n<ul>\n<li>Added five additional plugins:\n\n<ul>\n<li><a href=\"http://ckeditor.com/addon/autoembed\">Auto Embed</a> and <a href=\"http://ckeditor.com/addon/embedsemantic\">Semantic Media Embed</a> for inserting <a href=\"#!/guide/dev_media_embed\">embedded media resources</a> like videos (e.g. from YouTube, Vimeo), tweets or slideshows.</li>\n<li><a href=\"http://ckeditor.com/addon/image2\">Enhanced Image</a> to provide <a href=\"#!/guide/dev_captionedimage\">captioned images</a>.</li>\n<li><a href=\"http://ckeditor.com/addon/uploadimage\">Upload Image</a> and <a href=\"http://ckeditor.com/addon/uploadfile\">Upload File</a> to support <a href=\"#!/guide/dev_drop_paste\">file uploads via drag&amp;drop and pasting</a> images from clipboard.</li>\n</ul>\n</li>\n<li>Adjusted the <a href=\"#!/guide/dev_toolbar\">toolbar configuration</a> to display buttons in a single row.</li>\n<li>Adjusted <a href=\"#!/guide/dev_styles\">content CSS styles</a>, including usage of Roboto font from <a href=\"https://developers.google.com/fonts/docs/getting_started\">Google Fonts</a>.</li>\n</ul>\n\n\n<div class=\"responsive\">\n<p><img src=\"guides/dev_example_setups/editor1.png\" alt=\"Article Editor\" width=\"1151\" height=\"546\"></p>\n</div>\n\n\n<h2 id='dev_example_setups-section-document-editor'>Document Editor</h2>\n\n<p>The Document Editor demo showcases a more robust editor designed for creating documents which are usually later printed or exported to PDF files using tools like <a href=\"https://github.com/wkhtmltopdf/wkhtmltopdf\">wkhtmltopdf</a> or <a href=\"https://github.com/ariya/phantomjs\">PhantomJS</a> (note: PhantomJS 2.x currently has a <a href=\"https://github.com/ariya/phantomjs/issues/13997\">known zoom issue</a>).</p>\n\n<p class=\"tip\">\n  Visit the <a href=\"https://github.com/ckeditor/ckeditor-docs-samples/tree/master/editors\">ckeditor-docs-samples</a> GitHub repository to learn more about this configuration.\n</p>\n\n\n<p>The Document Editor is based on the <a href=\"http://ckeditor.com/download\">Full package</a> with a few modifications:</p>\n\n<ul>\n<li>Added three additional plugins:\n\n<ul>\n<li><a href=\"http://ckeditor.com/addon/tableresize\">Table Resize</a> to enable <a href=\"#!/guide/dev_table-section-table-resizing-with-your-mouse\">table columns resizing</a>.</li>\n<li><a href=\"http://ckeditor.com/addon/uploadimage\">Upload Image</a> and <a href=\"http://ckeditor.com/addon/uploadfile\">Upload File</a> to support <a href=\"#!/guide/dev_drop_paste\">file uploads via drag&amp;drop and pasting</a> images from clipboard.</li>\n</ul>\n</li>\n<li>Adjusted the <a href=\"#!/guide/dev_toolbar\">toolbar configuration</a> to display buttons in a single row.</li>\n<li>Reduced the number of buttons in the toolbar (Full preset comes with plenty of them).</li>\n<li>Adjusted <a href=\"#!/guide/dev_styles\">content CSS styles</a> to render the document in a way that resembles a real sheet of paper.</li>\n</ul>\n\n\n<div class=\"responsive\">\n<p><img src=\"guides/dev_example_setups/editor2.png\" alt=\"Document Editor\" width=\"1150\" height=\"545\"></p>\n</div>\n\n\n<h2 id='dev_example_setups-section-inline-editor'>Inline Editor</h2>\n\n<p>The Inline Editor demo showcases <a href=\"#!/guide/dev_inline\">inline editing</a> that allows you to edit any element on the page in-place.</p>\n\n<p class=\"tip\">\n  Visit the <a href=\"http://sdk.ckeditor.com/samples/inline.html\">CKEditor SDK</a> website to try out this configuration.\n</p>\n\n\n<p>Inline editor provides a real WYSIWYG experience \"out of the box\" because unlike in <a href=\"#!/guide/dev_framed\">classic editor</a>, there is no <code>&lt;iframe&gt;</code> element surrounding the editing area. The CSS styles used for editor content are exactly the same as on the target page where the content is rendered.</p>\n\n<div class=\"responsive\">\n<p><img src=\"guides/dev_example_setups/editor3.png\" alt=\"Inline Editor\" width=\"1145\" height=\"629\"></p>\n</div>\n\n\n<h2 id='dev_example_setups-section-developer-site-editor'>Developer Site Editor</h2>\n\n<p>The Developer Site Editor demo showcases a sample editor for technical websites. The most interesting features presented in this configuration are <a href=\"#!/guide/dev_codesnippet\">Code Snippets</a> and <a href=\"#!/guide/dev_mathjax\">Mathematical Formulas</a>. Both plugins support independent blocks of content rendered with the help of external JavaScript libraries.</p>\n\n<p class=\"tip\">\n  Visit the <a href=\"https://github.com/ckeditor/ckeditor-docs-samples/tree/master/editors\">ckeditor-docs-samples</a> GitHub repository to learn more about this configuration.\n</p>\n\n\n<p>The Developer Site Editor is based on the <a href=\"http://ckeditor.com/download\">Standard package</a> with a few modifications:</p>\n\n<ul>\n<li>Added five additional plugins:\n\n<ul>\n<li><a href=\"http://ckeditor.com/addon/codesnippet\">Code Snippet</a> and <a href=\"http://ckeditor.com/addon/mathjax\">Mathematical Formulas</a> for inserting code snippets and mathematical formulas.</li>\n<li><a href=\"http://ckeditor.com/addon/image2\">Enhanced Image</a> to provide <a href=\"#!/guide/dev_captionedimage\">captioned images</a>.</li>\n<li><a href=\"http://ckeditor.com/addon/uploadimage\">Upload Image</a> and <a href=\"http://ckeditor.com/addon/uploadfile\">Upload File</a> to support <a href=\"#!/guide/dev_drop_paste\">file uploads via drag&amp;drop and pasting</a> images from clipboard.</li>\n</ul>\n</li>\n<li>Adjusted the <a href=\"#!/guide/dev_toolbar\">toolbar configuration</a> to display buttons in a single row and removed a few redundant buttons.</li>\n</ul>\n\n\n<div class=\"responsive\">\n<p><img src=\"guides/dev_example_setups/editor4.png\" alt=\"Developer Site Editor\" width=\"1154\" height=\"552\"></p>\n</div>\n\n\n<h2 id='dev_example_setups-section-drag-%26amp%3B-drop'>Drag &amp; Drop</h2>\n\n<p>The Drag &amp; Drop demo showcases possible usage of CKEditor interface for handling drag and drop operations.</p>\n\n<p class=\"tip\">\n  Visit the <a href=\"http://sdk.ckeditor.com/samples/draganddrop.html\">CKEditor SDK</a> website to try out this configuration.\n</p>\n\n\n<p>The Drag &amp; Drop sample allows you to drag contacts from the list on the right-hand side to the inline editor on the left-hand side. The contacts are inserted into the editor as custom <a href=\"#!/guide/dev_widgets\">widgets</a> representing the h-card microformat.</p>\n\n<div class=\"responsive\">\n<p><img src=\"guides/dev_example_setups/editor5.png\" alt=\"Drag &amp;amp; Drop\" width=\"1160\" height=\"574\"></p>\n</div>\n\n\n<h2 id='dev_example_setups-section-accessibility-checker'>Accessibility Checker</h2>\n\n<p>This demo showcases the <strong>Accessibility Checker</strong> plugin &mdash; an innovative solution that lets you inspect the accessibility level of content created in CKEditor and immediately solve any accessibility issues that are found.</p>\n\n<p class=\"tip\">\n  Visit the <a href=\"http://sdk.ckeditor.com/samples/accessibilitychecker.html\">CKEditor SDK</a> website to try out this configuration.\n</p>\n\n\n\n\n<div class=\"responsive\">\n<p><img src=\"guides/dev_example_setups/editor6.png\" alt=\"Accessibility Checker\" width=\"1154\" height=\"544\"></p>\n</div>\n\n\n<h2 id='dev_example_setups-section-hints-for-production-environment-%28all-setups%29'>Hints for Production Environment (All Setups)</h2>\n\n<p>All setups above used some additional plugins which are not included by default in the Basic, Standard or Full distributions.</p>\n\n<p>There are multiple ways of loading the CKEditor library inside an application (each of them having its pros and cons) that are especially important when multiple additional plugins are loaded.</p>\n\n<p>For better understanding of key differences please refer to <a href=\"#!/guide/dev_best_practices\">CKEditor Best Practices</a> and <a href=\"#!/guide/dev_advanced_installation\">Advanced Installation Concepts</a>.</p>\n\n<h3 id='dev_example_setups-section-manual-download-and-installation-of-additional-plugins-%28not-recommended%29'>Manual Download and Installation of Additional Plugins (Not Recommended)</h3>\n\n<p>Although at a first glance it looks like the simplest way of adding plugins to CKEditor, it is not only inefficient but also may result\nin a headache when trying to add plugin A, that requires plugin B, that requires plugin C (...and so on).</p>\n\n<p>In a brief summary it involves the following steps:</p>\n\n<ol>\n    <li>Downloading the predefined package (Basic/Standard/Full) from the <a href=\"http://ckeditor.com/download\">Download page</a>.</li>\n    <li>Downloading additional plugins manually from the <a href=\"http://ckeditor.com/addons/plugins/all\">Add-ons Repository</a>.</li>\n    <li>Downloading plugins required by additional plugins manually.</li>\n    <li>Enabling additional plugins manually through <a href=\"#!/api/CKEDITOR.config-cfg-extraPlugins\" rel=\"CKEDITOR.config-cfg-extraPlugins\" class=\"docClass\">CKEDITOR.config.extraPlugins</a>.</li>\n</ol>\n\n\n\n\n<table border=\"1\" class=\"hints\">\n<tr><td class=\"hints-benchmark\">Benchmark</td><td class=\"hints-result\">Result</td><td>Comments</td></tr>\n<tr><th>Plugin installation complexity</th><td style=\"width:60px\">High</td>\n<td>Need to manually download all dependencies.</td></tr>\n<tr><th>Toolbar configuration complexity</th><td>Moderate</td><td>Only after <a href=\"#!/api/CKEDITOR.config-cfg-extraPlugins\" rel=\"CKEDITOR.config-cfg-extraPlugins\" class=\"docClass\">CKEDITOR.config.extraPlugins</a> is set the toolbar configurator will render all available buttons.</td></tr>\n<tr><th>Complexity of future upgrades</th><td>High</td><td>Need to manually download all plugins and dependencies again.</td></tr>\n<tr><th>Number of files requested by the browser</th><td>High</td><td>Each plugin results in a couple of additional HTTP requests (plugin, language file, icon).</td></tr>\n<tr><th>Performance</th><td>Low</td><td>Large number of HTTP requests.</td></tr>\n</table>\n\n\n<h3 id='dev_example_setups-section-using-cdn'>Using CDN</h3>\n\n<p>This is the easiest way of using CKEditor if additional third-party plugins are not used. Using CKEditor from CDN involves the following steps:</p>\n\n<ol>\n    <li>Adding a <code>&lt;script&gt;</code> tag that loads <code>ckeditor.js</code> from CDN. For more information refer to the <a href=\"http://cdn.ckeditor.com/\">CDN documentation</a>.</li>\n    <li>In case of using third-party plugins:\n        <ol>\n            <li>Downloading them manually from the <a href=\"http://ckeditor.com/addons/plugins/all\">Add-ons Repository</a>.</li>\n            <li>Downloading plugin requirements manually.</li>\n        </ol>\n     </li>\n    <li>Enabling additional plugins manually through <a href=\"#!/api/CKEDITOR.config-cfg-extraPlugins\" rel=\"CKEDITOR.config-cfg-extraPlugins\" class=\"docClass\">CKEDITOR.config.extraPlugins</a>.</li>\n</ol>\n\n\n\n\n<table border=\"1\" class=\"hints\">\n<tr><td class=\"hints-benchmark\">Benchmark</td><td class=\"hints-result\">Result</td><td>Comments</td></tr>\n<tr><th>Plugin installation complexity</th><td style=\"width:60px\">Moderate - High</td>\n<td>Plugins authored by CKSource are available on CDN and can be easily enabled throogh <a href=\"#!/api/CKEDITOR.config-cfg-extraPlugins\" rel=\"CKEDITOR.config-cfg-extraPlugins\" class=\"docClass\">CKEDITOR.config.extraPlugins</a>. Third-party plugins need to be downloaded locally and <a href=\"http://cdn.ckeditor.com/#plugins\">enabled as external plugins</a>.</td></tr>\n<tr><th>Toolbar configuration complexity</th><td>High</td><td>The toolbar configurator is not available so understanding what button names to use may be challenging.</td></tr>\n<tr><th>Complexity of future upgrades</th><td>Low - Moderate</td><td>As easy as changing the version number in a single <code>&gt;script&lt;</code> tag. Locally stored third-party plugins must be updated manually.</td></tr>\n<tr><th>Number of files requested by the browser</th><td>High</td><td>Each plugin results in a couple of additional HTTP requests (plugin, language file, icon).</td></tr>\n<tr><th>Performance</th><td>Moderate - High</td><td>Larger number of HTTP requests is compensated by fast network and endpoints located very close to the end user. Additional benefit caused by the fact that browsers load requests from multiple domains in parallel.</td></tr>\n</table>\n\n\n<h3 id='dev_example_setups-section-using-builder-%28recommended%29'>Using Builder (Recommended)</h3>\n\n<p>Using <a href=\"http://ckeditor.com/builder\">Builder</a> to build a bundle with all required plugins is highly recommended in case of using customized packages, especially those with additional third-party plugins.</p>\n\n<p>Refer to the <a href=\"#!/guide/dev_plugins-section-through-ckbuilder\">Installing Plugins &ndash; Online Builder Installation</a> article for information about building a custom editor package.</p>\n\n<table border=\"1\" class=\"hints\">\n<tr><td class=\"hints-benchmark\">Benchmark</td><td class=\"hints-result\">Result</td><td>Comments</td></tr>\n<tr><th>Plugin installation complexity</th><td style=\"width:60px\">Low</td>\n<td>As simple as selecting additional plugins from the list of all available plugins.</td></tr>\n<tr><th>Toolbar configuration complexity</th><td>Low</td><td>The toolbar configurator is included in the downloaded CKEditor package and is aware of all included plugins.</td></tr>\n<tr><th>Complexity of future upgrades</th><td>Low</td><td>As easy as uploading the <code>build-config.js</code> file to the <a href=\"http://ckeditor.com/builder\">Online Builder</a> and regenerating the package.</td></tr>\n<tr><th>Number of files requested by the browser</th><td>Low</td><td>Plugins are bundled into a single <code>ckeditor.js</code> file. Icons are merged into a sprite. Language files are merged.</td></tr>\n<tr><th>Performance</th><td>High</td><td>The only problem might be in slow networks where CKEditor is hosted and/or in a misconfigured server without file compression enabled. Such setup would influence not only CKEditor but would also slow down the whole web application.</td></tr>\n</table>\n\n","title":"Example CKEditor Setups","meta_description":"Setups showcased on demo pages explained in details.","meta_keywords":"ckeditor, editor, wysiwyg, classic, framed, inline, save, saving, submit, submitting, post, posting, data, content, server, ajax, change, form, textarea"});