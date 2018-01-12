Ext.data.JsonP.dev_colorbutton({"guide":"<!--\nCopyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.\nFor licensing, see LICENSE.md.\n-->\n\n\n<h1 id='dev_colorbutton-section-setting-text-and-background-color'>Setting Text and Background Color</h1>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/dev_colorbutton-section-more-colors-option-and-color-dialog'>More Colors Option and Color Dialog</a></li>\n<li>\n<a href='#!/guide/dev_colorbutton-section-custom-color-list'>Custom Color List</a></li>\n<li>\n<a href='#!/guide/dev_colorbutton-section-custom-color-style-definition'>Custom Color Style Definition</a></li>\n<li>\n<a href='#!/guide/dev_colorbutton-section-text-and-background-color-demo'>Text and Background Color Demo</a></li>\n<li>\n<a href='#!/guide/dev_colorbutton-section-related-features'>Related Features</a></li></ol>\n</div>\n\n<p class=\"requirements\">\n    This feature is provided through optional plugins that are only included in the Full preset available from the official CKEditor <a href=\"http://ckeditor.com/download\">Download</a> site. You can also <a href=\"#!/guide/dev_plugins\">add them to your custom build</a> with <a href=\"http://ckeditor.com/builder\">CKBuilder</a>.\n</p>\n\n\n<p>The optional <a href=\"http://ckeditor.com/addon/colorbutton\">Color Button</a> plugin provides the ability to define font and background color for text created in CKEditor. When enabled, it adds the <strong>Text Color</strong> and <strong>Background Color</strong> toolbar buttons that open a color selection drop-down list. If you want to quickly <a href=\"#!/guide/dev_removeformat\">remove colors</a> from your document, use the <strong>Remove Format</strong> button provided by the <a href=\"http://ckeditor.com/addon/removeformat\">Remove Format</a> plugin.</p>\n\n<p><p><img src=\"guides/dev_colorbutton/colorbutton_05.png\" alt=\"The Text Color and Background Color features\" width=\"939\" height=\"275\"></p></p>\n\n<h2 id='dev_colorbutton-section-more-colors-option-and-color-dialog'>More Colors Option and Color Dialog</h2>\n\n<p>You can also add the optional <a href=\"http://ckeditor.com/addon/colordialog\">Color Dialog</a> plugin which extends the color selector with the <strong>More Colors</strong> option and a user-friendly way to select the desired color through a dedicated <strong>Select Color</strong> dialog window. When this plugin is enabled, the <strong>More Colors</strong> option appears automatically for the text and background color.</p>\n\n<p><p><img src=\"guides/dev_colorbutton/colordialog_03.png\" alt=\"The Select Color dialog window\" width=\"387\" height=\"316\"></p></p>\n\n<p>You can hide the <strong>More Colors</strong> feature by setting the <a href=\"#!/api/CKEDITOR.config-cfg-colorButton_enableMore\" rel=\"CKEDITOR.config-cfg-colorButton_enableMore\" class=\"docClass\">CKEDITOR.config.colorButton_enableMore</a> configuration option to <code>false</code>.</p>\n\n<h2 id='dev_colorbutton-section-custom-color-list'>Custom Color List</h2>\n\n<p>The list of colors available in the color selectors can be customized, for example to include the colors that are used in your website. You may also want to limit user's choice of colors to just selected few in order to avoid the overuse of colors.</p>\n\n<p>Use the <a href=\"#!/api/CKEDITOR.config-cfg-colorButton_colors\" rel=\"CKEDITOR.config-cfg-colorButton_colors\" class=\"docClass\">CKEDITOR.config.colorButton_colors</a> configuration option to define a custom list available in the <strong>Text Color</strong> and <strong>Background Color</strong> features. For example:</p>\n\n<pre><code>config.colorButton_colors = 'CF5D4E,454545,FFF,CCC,DDD,CCEAEE,66AB16';\n</code></pre>\n\n<p>Additionally, since CKEditor 4.5.8 you can also disable the \"Automatic\" option by setting the <a href=\"#!/api/CKEDITOR.config-cfg-colorButton_enableAutomatic\" rel=\"CKEDITOR.config-cfg-colorButton_enableAutomatic\" class=\"docClass\">CKEDITOR.config.colorButton_enableAutomatic</a> option to <code>false</code>.</p>\n\n<pre><code>config.colorButton_enableAutomatic = false;\n</code></pre>\n\n<p>These settings will cause the color list to only contain the seven colors listed above, with no \"Automatic\" option available:</p>\n\n<p><p><img src=\"guides/dev_colorbutton/colorbutton_04.png\" alt=\"The customized color list\" width=\"937\" height=\"294\"></p></p>\n\n<div class=\"tip\">\n    <p>\n        The <strong>Text and Background Color</strong> feature does not create semantically meaningful content. Even if you adjust the color list to match the style of your website, your users will be able to arbitrarily apply colors to text elements without any consistency.\n    </p>\n    <p>\n        A much better idea for creating semantic content and maintaining consistent styling across your website is to adjust the <strong><a href=\"#!/guide/dev_styles\">Styles</a></strong> drop-down list to include some colors that could be applied to user-created content and would still be consistent with your website design.\n    </p>\n</div>\n\n\n<h2 id='dev_colorbutton-section-custom-color-style-definition'>Custom Color Style Definition</h2>\n\n<p>You can also decide how the color definition is stored by setting the <a href=\"#!/api/CKEDITOR.config-cfg-colorButton_foreStyle\" rel=\"CKEDITOR.config-cfg-colorButton_foreStyle\" class=\"docClass\">CKEDITOR.config.colorButton_foreStyle</a> (for text color) and <a href=\"#!/api/CKEDITOR.config-cfg-colorButton_backStyle\" rel=\"CKEDITOR.config-cfg-colorButton_backStyle\" class=\"docClass\">CKEDITOR.config.colorButton_backStyle</a> (for background color) configuration options. By default, the color is added as a <code>&lt;span&gt;</code> element with the <code>style</code> attribute, but you could also e.g. use the legacy (and not recommended) HTML4 <code>&lt;font&gt;</code> element definition:</p>\n\n<pre><code>config.colorButton_foreStyle = {\n    element: 'font',\n    attributes: { 'color': '#(color)' }\n};\n\nconfig.colorButton_backStyle = {\n    element: 'font',\n    styles: { 'background-color': '#(color)' }\n};\n</code></pre>\n\n<p>CKEditor will then output the color definition as <code>&lt;font&gt;</code> elements with <code>color</code> and <code>style=\"background-color\"</code> attributes for text and background color, respectively:</p>\n\n<pre><code>&lt;p&gt;&lt;font color=\"#800080\"&gt;This is my text color.&lt;/font&gt;&lt;br /&gt;\n&lt;font style=\"background-color:#FFFF00;\"&gt;This is my background color&lt;/font&gt;&lt;/p&gt;\n</code></pre>\n\n<h2 id='dev_colorbutton-section-text-and-background-color-demo'>Text and Background Color Demo</h2>\n\n<p>See the <a href=\"../samples/colorbutton.html\">working \"Setting Text and Background Color\" sample</a> that showcases the usage and customization of the text and background color features.</p>\n\n<h2 id='dev_colorbutton-section-related-features'>Related Features</h2>\n\n<p>Refer to the following resources for more information about text styling and formatting:</p>\n\n<ul>\n<li>The <a href=\"#!/guide/dev_fcopyformatting\">Using the Copy Formatting Feature</a> article explains how to copy text formatting between document fragments.</li>\n<li>The <a href=\"#!/guide/dev_removeformat\">Removing Text Formatting</a> article explains how to quickly remove any text formatting that is applied through inline HTML elements and CSS styles.</li>\n<li>The <a href=\"#!/guide/dev_basicstyles\">Basic Text Styles: Bold, Italic and More</a> article explains how to apply bold, italic, underline, strikethrough, subscript and superscript formatting.</li>\n<li>The <a href=\"#!/guide/dev_styles\">Applying Styles to Editor Content</a> article discusses creating more semantically correct text styles.</li>\n<li>The <a href=\"#!/guide/dev_format\">Applying Block-Level Text Formats</a> article presents how to apply formatting to entire text blocks and not just text selections.</li>\n</ul>\n\n","title":"Text and Background Color","meta_description":"Using and customizing the Text and Background Color features.","meta_keywords":"ckeditor, editor, wysiwyg, text, background, color, configuration, configure, modify, modification, change, customize, customization, customise, customisation"});