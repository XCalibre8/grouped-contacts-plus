Ext.data.JsonP.dev_file_browser_api({"guide":"<!--\nCopyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.\nFor licensing, see LICENSE.md.\n-->\n\n\n<h1 id='dev_file_browser_api-section-file-browser-api---creating-a-custom-file-manager'>File Browser API - Creating a Custom File Manager</h1>\n<div class='toc'>\n<p><strong>Contents</strong></p>\n<ol>\n<li><a href='#!/guide/dev_file_browser_api-section-interaction-between-ckeditor-and-file-manager'>Interaction Between CKEditor and File Manager</a><ol>\n<li>\n<a href='#!/guide/dev_file_browser_api-section-example-1'>Example 1</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_file_browser_api-section-passing-the-url-of-the-selected-file'>Passing the URL of the Selected File</a><ol>\n<li>\n<a href='#!/guide/dev_file_browser_api-section-example-2'>Example 2</a></li>\n<li>\n<a href='#!/guide/dev_file_browser_api-section-example-3'>Example 3</a></li>\n<li>\n<a href='#!/guide/dev_file_browser_api-section-example-4'>Example 4</a></li>\n</ol>\n<li>\n<a href='#!/guide/dev_file_browser_api-section-further-reading'>Further Reading</a></li></ol>\n</div>\n\n<p class=\"requirements\">\n    CKEditor can be easily integrated with an external file manager (file browser/uploader) thanks to the <a href=\"http://ckeditor.com/addon/filebrowser\">File Browser</a> plugin which by default is included in every preset.\n</p>\n\n\n<p>To connect a file browser/uploader that is already compatible with CKEditor, refer to the <a href=\"#!/guide/dev_file_browse_upload\">File Manager Integration</a> article. If you want to integrate with <a href=\"http://cksource.com/ckfinder/\">CKFinder</a>,\ncheck the <a href=\"#!/guide/dev_ckfinder_integration\">CKFinder Integration</a> article.</p>\n\n<h2 id='dev_file_browser_api-section-interaction-between-ckeditor-and-file-manager'>Interaction Between CKEditor and File Manager</h2>\n\n<p>CKEditor automatically sends some additional arguments to the file manager:</p>\n\n<ul>\n<li><a href=\"#!/api/CKEDITOR.editor-property-name\" rel=\"CKEDITOR.editor-property-name\" class=\"docClass\">CKEditor</a> &ndash; the name of the CKEditor instance.</li>\n<li><a href=\"#!/api/CKEDITOR.editor-property-langCode\" rel=\"CKEDITOR.editor-property-langCode\" class=\"docClass\">langCode</a> &ndash; CKEditor language (<code>en</code> for English).</li>\n<li><code>CKEditorFuncNum</code> &ndash; anonymous function reference number used to pass the URL of a file to CKEditor (a random number).</li>\n</ul>\n\n\n<p>For example:</p>\n\n<pre><code>CKEditor=editor1&amp;CKEditorFuncNum=1&amp;langCode=en\n</code></pre>\n\n<h3 id='dev_file_browser_api-section-example-1'>Example 1</h3>\n\n<p>Suppose that CKEditor was created using the following JavaScript call:</p>\n\n<pre><code><a href=\"#!/api/CKEDITOR-method-replace\" rel=\"CKEDITOR-method-replace\" class=\"docClass\">CKEDITOR.replace</a>( 'editor2', {\n    filebrowserBrowseUrl: '/browser/browse.php?type=Files',\n    filebrowserUploadUrl: '/uploader/upload.php?type=Files'\n});\n</code></pre>\n\n<p>In order to browse files, CKEditor will call:</p>\n\n<pre><code>/browser/browse.php?type=Files&amp;CKEditor=editor2&amp;CKEditorFuncNum=2&amp;langCode=de\n</code></pre>\n\n<p>The call includes the following elements:</p>\n\n<ul>\n<li><code>/browser/browse.php?type=Files</code> &ndash; the value of the <code>filebrowserBrowseUrl</code> parameter.</li>\n<li><code>&amp;CKEditor=editor2&amp;CKEditorFuncNum=2&amp;langCode=de</code> &ndash; the information added by CKEditor:\n\n<ul>\n<li><code>CKEditor=editor2</code> &ndash; the name of the CKEditor instance (<code>editor2</code>).</li>\n<li><code>CKEditorFuncNum=2</code> &ndash; the reference number of an anonymous\n  function that should be used in the <a href=\"#!/api/CKEDITOR.tools-method-callFunction\" rel=\"CKEDITOR.tools-method-callFunction\" class=\"docClass\">callFunction</a> method.</li>\n<li><code>langCode=de</code> &ndash; the language code (in this case: <code>de</code> for German). This\n  parameter can be used to send localized error messages.</li>\n</ul>\n</li>\n</ul>\n\n\n<h2 id='dev_file_browser_api-section-passing-the-url-of-the-selected-file'>Passing the URL of the Selected File</h2>\n\n<p>To send back the file URL from an external file manager, call\n<a href=\"#!/api/CKEDITOR.tools-method-callFunction\" rel=\"CKEDITOR.tools-method-callFunction\" class=\"docClass\">CKEDITOR.tools.callFunction</a> and pass <code>CKEditorFuncNum</code> as the first\nargument:</p>\n\n<pre><code>window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl [, data] );\n</code></pre>\n\n<p>If <code>data</code> (the third argument) is a string, it will be displayed by CKEditor. This parameter is usually used to display an error message if a problem occurs during the file upload.</p>\n\n<h3 id='dev_file_browser_api-section-example-2'>Example 2</h3>\n\n<p>The following example shows how to send the URL from a file manager using JavaScript code (save it as <code>browse.php</code>):</p>\n\n<pre><code>&lt;!DOCTYPE html&gt;\n&lt;html lang=\"en\"&gt;\n&lt;head&gt;\n    &lt;meta charset=\"UTF-8\"&gt;\n    &lt;title&gt;Example: Browsing Files&lt;/title&gt;\n    &lt;script&gt;\n        // Helper function to get parameters from the query string.\n        function getUrlParam( paramName ) {\n            var reParam = new RegExp( '(?:[\\?&amp;]|&amp;)' + paramName + '=([^&amp;]+)', 'i' );\n            var match = window.location.search.match( reParam );\n\n            return ( match &amp;&amp; match.length &gt; 1 ) ? match[1] : null;\n        }\n        // Simulate user action of selecting a file to be returned to CKEditor.\n        function returnFileUrl() {\n\n            var funcNum = getUrlParam( 'CKEditorFuncNum' );\n            var fileUrl = '/path/to/file.txt';\n            window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl );\n            window.close();\n        }\n    &lt;/script&gt;\n&lt;/head&gt;\n&lt;body&gt;\n    &lt;button onclick=\"returnFileUrl()\"&gt;Select File&lt;/button&gt;\n&lt;/body&gt;\n&lt;/html&gt;\n</code></pre>\n\n<h3 id='dev_file_browser_api-section-example-3'>Example 3</h3>\n\n<p>If <code>data</code> is a function, it will be executed in the scope of the button that called the file manager. It means that the server connector can have direct access to CKEditor and the dialog window to which the button belongs.</p>\n\n<p>Suppose that apart from passing the <code>fileUrl</code> value that is assigned to an appropriate field automatically based on the dialog window definition you also want to set the <code>alt</code> attribute, if the file manager was opened in the <strong>Image Properties</strong> dialog window. In order to do this, pass an anonymous function as a third argument:</p>\n\n<pre><code>&lt;!DOCTYPE html&gt;\n&lt;html lang=\"en\"&gt;\n&lt;head&gt;\n    &lt;meta charset=\"UTF-8\"&gt;\n    &lt;title&gt;Example: Browsing Files&lt;/title&gt;\n    &lt;script&gt;\n        // Helper function to get parameters from the query string.\n        function getUrlParam( paramName ) {\n            var reParam = new RegExp( '(?:[\\?&amp;]|&amp;)' + paramName + '=([^&amp;]+)', 'i' );\n            var match = window.location.search.match( reParam );\n\n            return ( match &amp;&amp; match.length &gt; 1 ) ? match[1] : null;\n        }\n        // Simulate user action of selecting a file to be returned to CKEditor.\n        function returnFileUrl() {\n\n            var funcNum = getUrlParam( 'CKEditorFuncNum' );\n            var fileUrl = 'http://c.cksource.com/a/1/img/sample.jpg';\n            window.opener.CKEDITOR.tools.callFunction( funcNum, fileUrl, function() {\n                // Get the reference to a dialog window.\n                var dialog = this.getDialog();\n                // Check if this is the Image Properties dialog window.\n                if ( dialog.getName() == 'image' ) {\n                    // Get the reference to a text field that stores the \"alt\" attribute.\n                    var element = dialog.getContentElement( 'info', 'txtAlt' );\n                    // Assign the new value.\n                    if ( element )\n                        element.setValue( 'alt text' );\n                }\n                // Return \"false\" to stop further execution. In such case CKEditor will ignore the second argument (\"fileUrl\")\n                // and the \"onSelect\" function assigned to the button that called the file manager (if defined).\n                // return false;\n            } );\n            window.close();\n        }\n    &lt;/script&gt;\n&lt;/head&gt;\n&lt;body&gt;\n    &lt;button onclick=\"returnFileUrl()\"&gt;Select File&lt;/button&gt;\n&lt;/body&gt;\n&lt;/html&gt;\n</code></pre>\n\n<h3 id='dev_file_browser_api-section-example-4'>Example 4</h3>\n\n<p>The following code shows how to send back the URL of an uploaded file from the PHP connector (save it as <code>upload.php</code>):</p>\n\n<pre><code>&lt;!DOCTYPE html&gt;\n&lt;html lang=\"en\"&gt;\n&lt;head&gt;\n    &lt;meta charset=\"UTF-8\"&gt;\n    &lt;title&gt;Example: File Upload&lt;/title&gt;\n&lt;/head&gt;\n&lt;body&gt;\n&lt;?php\n// Required: anonymous function reference number as explained above.\n$funcNum = $_GET['CKEditorFuncNum'] ;\n// Optional: instance name (might be used to load a specific configuration file or anything else).\n$CKEditor = $_GET['CKEditor'] ;\n// Optional: might be used to provide localized messages.\n$langCode = $_GET['langCode'] ;\n// Optional: compare it with the value of `ckCsrfToken` sent in a cookie to protect your server side uploader against CSRF.\n// Available since CKEditor 4.5.6.\n$token = $_POST['ckCsrfToken'] ;\n\n// Check the $_FILES array and save the file. Assign the correct path to a variable ($url).\n$url = '/path/to/uploaded/file.ext';\n// Usually you will only assign something here if the file could not be uploaded.\n$message = 'The uploaded file has been renamed';\n\necho \"&lt;script type='text/javascript'&gt;window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');&lt;/script&gt;\";\n?&gt;\n&lt;/body&gt;\n&lt;/html&gt;\n</code></pre>\n\n<h2 id='dev_file_browser_api-section-further-reading'>Further Reading</h2>\n\n<p>For more information on integrating CKEditor with a file manager refer to the following articles:</p>\n\n<ul>\n<li><a href=\"#!/guide/dev_file_browse_upload\">File Manager Integration</a></li>\n<li><a href=\"#!/guide/dev_file_manager_configuration\">Advanced File Manager Configuration</a></li>\n<li><a href=\"#!/guide/dev_ckfinder_integration\">CKFinder Integration</a></li>\n<li><a href=\"#!/guide/dev_dialog_add_file_browser\">Adding the File Manager to Dialog Windows</a></li>\n<li><a href=\"#!/guide/dev_file_upload\">Uploading Pasted and Dropped Images</a></li>\n</ul>\n\n","title":"File Browser API","meta_description":"How to use the CKEditor File Browser API and integrate a custom file manager.","meta_keywords":"ckeditor, editor, integrate, integration, API, file, files, upload, uploader, manager, manage, management, browse, browser, image, images"});