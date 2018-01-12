Ext.data.JsonP.dev_ckfinder_integration({"guide":"<h1 id='dev_ckfinder_integration-section-ckfinder-integration'>CKFinder Integration</h1>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/dev_ckfinder_integration-section-ckfinder-3'>CKFinder 3</a><ol>\n<li>\n<a href='#!/guide/dev_ckfinder_integration-section-using-ckfinder.setupckeditor%28%29'>Using CKFinder.setupCKEditor()</a></li>\n<li>\n<a href='#!/guide/dev_ckfinder_integration-section-manual-integration-with-configuration-settings'>Manual Integration with Configuration Settings</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_ckfinder_integration-section-ckfinder-2'>CKFinder 2</a><ol>\n<li>\n<a href='#!/guide/dev_ckfinder_integration-section-using-ckfinder.setupckeditor%28%29'>Using CKFinder.setupCKEditor()</a></li>\n<li>\n<a href='#!/guide/dev_ckfinder_integration-section-manual-integration-with-configuration-settings'>Manual Integration with Configuration Settings</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_ckfinder_integration-section-further-reading'>Further Reading</a></li></ol>\n</div>\n\n<p>The aim of this article is to explain how to integrate CKEditor with <a href=\"http://cksource.com/ckfinder/\">CKFinder</a>, a powerful and easy to use Ajax file manager. See <a href=\"http://cksource.com/ckfinder/demo#ckeditor\">the demo</a> for a live demonstration.</p>\n\n<p class=\"requirements\">\n    CKFinder is a <a href=\"http://cksource.com/ckfinder\">commercial application</a> that was designed with CKEditor compatibility in mind. It is currently available as version 3.x for PHP and version 2.x for Java, ASP.NET, ASP and ColdFusion.\n</p>\n\n\n<h2 id='dev_ckfinder_integration-section-ckfinder-3'>CKFinder 3</h2>\n\n<p><a href=\"https://cksource.com/ckfinder/demo\">CKFinder 3</a> for PHP was released in June 2015. ASP.NET and Java versions are now under development. The ASP and ColdFusion distributions of CKFinder will not be upgraded to version 3, however, they will still receive patches (including security fixes) until 2019.</p>\n\n<p>The integration with CKFinder 3 can be conducted in two ways:</p>\n\n<ul>\n<li>Using the <code>CKFinder.setupCKEditor()</code> method.</li>\n<li>Manually, by passing additional configuration settings to the CKEditor instance.</li>\n</ul>\n\n\n<p>The image below shows CKFinder 3 integrated with CKEditor, with the file manager being opened from the editor Image Properties dialog window.</p>\n\n<p><p><img src=\"guides/dev_ckfinder_integration/ckeditor_with_ckfinder3.png\" alt=\"CKFinder 3 integrated with CKEditor\" width=\"1100\" height=\"648\"></p></p>\n\n<h3 id='dev_ckfinder_integration-section-using-ckfinder.setupckeditor%28%29'>Using CKFinder.setupCKEditor()</h3>\n\n<p>The simplest way to integrate CKFinder 3 with CKEditor is using the <a href=\"http://docs.cksource.com/ckfinder3/#!/api/CKFinder-method-setupCKEditor\">CKFinder.setupCKEditor()</a> method.</p>\n\n<p>This method takes the CKEditor instance which will be set up as a first argument (<code>editor</code>). If no argument is passed or the <code>editor</code> argument is null, CKFinder will integrate with all CKEditor instances.</p>\n\n<pre><code>var editor = <a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a>( 'editor1' );\nCKFinder.setupCKEditor( editor );\n</code></pre>\n\n<p>Please check the <code>samples/ckeditor.html</code> sample distributed with CKFinder 3 to see the full working example of this integration method.</p>\n\n<p>Refer to the <a href=\"http://docs.cksource.com/ckfinder3/#!/guide/dev_ckeditor-section-ckfinder.setupckeditor%28%29\">CKFinder 3 documentation</a> for more details and examples of:</p>\n\n<ul>\n<li>Integrating CKFinder with a selected CKEditor instance.</li>\n<li>Integrating CKFinder with all existing and future CKEditor instances.</li>\n<li>Passing CKFinder configuration options while integrating with CKEditor.</li>\n</ul>\n\n\n<h3 id='dev_ckfinder_integration-section-manual-integration-with-configuration-settings'>Manual Integration with Configuration Settings</h3>\n\n<p>In order to manually configure CKEditor to use CKFinder, you need to pass additional configuration settings to the CKEditor instance.</p>\n\n<p>For example, to enable CKFinder in a CKEditor instance using the same settings for all editor dialog windows:</p>\n\n<pre><code><a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a>( 'editor1', {\n    filebrowserBrowseUrl: '/ckfinder/ckfinder.html',\n    filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',\n    filebrowserWindowWidth: '1000',\n    filebrowserWindowHeight: '700'\n} );\n</code></pre>\n\n<p>See the <a href=\"http://docs.cksource.com/ckfinder3/#!/guide/dev_ckeditor-section-manual-integration\">CKFinder 3 documentation</a> for more details and examples of:</p>\n\n<ul>\n<li>Setting <a href=\"#!/api/CKEDITOR.config-cfg-filebrowserBrowseUrl\" rel=\"CKEDITOR.config-cfg-filebrowserBrowseUrl\" class=\"docClass\">config.filebrowserBrowseUrl</a> and <a href=\"#!/api/CKEDITOR.config-cfg-filebrowserUploadUrl\" rel=\"CKEDITOR.config-cfg-filebrowserUploadUrl\" class=\"docClass\">config.filebrowserUploadUrl</a> options.</li>\n<li>Setting available resource types.</li>\n<li>Changing the file manager window size.</li>\n<li>Setting target resource type and target folder for quick uploads.</li>\n<li>Providing different configuration for selected CKEditor dialog windows.</li>\n</ul>\n\n\n<h2 id='dev_ckfinder_integration-section-ckfinder-2'>CKFinder 2</h2>\n\n<p>The integration with CKFinder 2 may be conducted in two ways:</p>\n\n<ul>\n<li>By using the <a href=\"http://docs.cksource.com/ckfinder_2.x_api/symbols/CKFinder.html#.setupCKEditor\">CKFinder.setupCKEditor()</a> method available in the <a href=\"http://docs.cksource.com/ckfinder_2.x_api/\">CKFinder 2 API</a>.</li>\n<li>Manually, by setting CKEditor configuration options.</li>\n</ul>\n\n\n<p>The image below shows CKFinder 2 integrated with CKEditor, with the file manager being opened from the editor Image Properties dialog window.</p>\n\n<p><p><img src=\"guides/dev_ckfinder_integration/ckeditor_with_ckfinder.png\" alt=\"CKFinder 2 integrated with CKEditor\" width=\"640\" height=\"452\"></p></p>\n\n<h3 id='dev_ckfinder_integration-section-using-ckfinder.setupckeditor%28%29'>Using CKFinder.setupCKEditor()</h3>\n\n<p>The example below shows the use of the <a href=\"http://docs.cksource.com/ckfinder_2.x_api/symbols/CKFinder.html#.setupCKEditor\">CKFinder.setupCKEditor()</a> method to insert a CKEditor instance with CKFinder 2 integrated.</p>\n\n<p>This method takes the CKEditor instance which will be set up as a first argument (<code>editor</code>). If no argument is passed or the <code>editor</code> argument is null, CKFinder will integrate with all CKEditor instances.</p>\n\n<p>The second parameter of the <code>CKFinder.setupCKEditor()</code> method is the file manager configuration which may be just the path to the CKFinder installation.</p>\n\n<pre><code>var editor = <a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a>( 'editor1' );\nCKFinder.setupCKEditor( editor, '/ckfinder/' );\n</code></pre>\n\n<p>Please check the <code>_samples/ckeditor.html</code> sample distributed with CKFinder 2 to see the full working example of this integration method.</p>\n\n<p>Refer to the appropriate \"CKEditor Integration\" article of the <a href=\"http://docs.cksource.com/CKFinder_2.x/Developers_Guide\">CKFinder 2 Developer's Guide</a> for more details and examples of:</p>\n\n<ul>\n<li>Integrating CKFinder with a selected CKEditor instance.</li>\n<li>Integrating CKFinder with all existing and future CKEditor instances.</li>\n<li>Passing CKFinder configuration options while integrating with CKEditor.</li>\n</ul>\n\n\n<h3 id='dev_ckfinder_integration-section-manual-integration-with-configuration-settings'>Manual Integration with Configuration Settings</h3>\n\n<p>In order to manually configure CKEditor to use CKFinder, you need to pass additional configuration settings to the CKEditor instance.</p>\n\n<p>The sample below shows the configuration code that can be used to insert a CKEditor instance with CKFinder integrated. The browse and upload paths for images are configured separately from CKFinder default paths. The file manager window size was also set.</p>\n\n<pre><code><a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a>( 'editor1', {\n    filebrowserBrowseUrl: '/ckfinder/ckfinder.html',\n    filebrowserImageBrowseUrl: '/ckfinder/ckfinder.html?Type=Images',\n    filebrowserUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Files',\n    filebrowserImageUploadUrl: '/ckfinder/core/connector/php/connector.php?command=QuickUpload&amp;type=Images',\n    filebrowserWindowWidth : '1000',\n    filebrowserWindowHeight : '700'\n});\n</code></pre>\n\n<p>The example above is valid for the PHP environment. Note that <code>/ckfinder/</code> is a base path to the CKFinder installation directory.</p>\n\n<p>If you are using CKFinder for ASP, ASP.NET, Java, or ColdFusion, remember to change <code>php</code> above to the correct extension:</p>\n\n<ul>\n<li><code>asp</code> &ndash; <a href=\"http://docs.cksource.com/CKFinder_2.x/Developers_Guide/ASP/CKEditor_Integration\">CKFinder for ASP</a></li>\n<li><code>aspx</code> &ndash; <a href=\"http://docs.cksource.com/CKFinder_2.x/Developers_Guide/ASP.NET/CKEditor_Integration\">CKFinder for ASP.NET</a></li>\n<li><code>cfm</code> &ndash; <a href=\"http://docs.cksource.com/CKFinder_2.x/Developers_Guide/ColdFusion/CKEditor_Integration\">CKFinder for ColdFusion</a></li>\n<li><code>java</code> &ndash; <a href=\"http://docs.cksource.com/CKFinder_2.x/Developers_Guide/Java/CKEditor_Integration\">CKFinder for Java</a></li>\n<li><code>php</code> &ndash; <a href=\"http://docs.cksource.com/CKFinder_2.x/Developers_Guide/PHP/CKEditor_Integration\">CKFinder for PHP</a></li>\n</ul>\n\n\n<h2 id='dev_ckfinder_integration-section-further-reading'>Further Reading</h2>\n\n<p>For more information on integrating CKEditor with a file manager refer to the following articles:</p>\n\n<ul>\n<li><a href=\"#!/guide/dev_file_browse_upload\">File Manager Integration</a></li>\n<li><a href=\"#!/guide/dev_file_manager_configuration\">Advanced File Manager Configuration</a></li>\n<li><a href=\"#!/guide/dev_file_browser_api\">File Browser API - Creating a Custom File Manager</a></li>\n<li><a href=\"#!/guide/dev_dialog_add_file_browser\">Adding the File Manager to Dialog Windows</a></li>\n<li><a href=\"#!/guide/dev_file_upload\">Uploading Pasted and Dropped Files</a></li>\n</ul>\n\n","title":"CKFinder Integration","meta_description":"How to integrate CKEditor with CKFinder.","meta_keywords":"ckeditor, editor, ckfinder, integrate, integration, configure, configuration, file, files, upload, uploading, manage, management, browse, browser, image, images"});