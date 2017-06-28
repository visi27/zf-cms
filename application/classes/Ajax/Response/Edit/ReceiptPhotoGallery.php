<?php
class Ajax_Response_Edit_ReceiptPhotoGallery extends Ajax_Response_Abstract{

	public function layoutCenter_Action(){		
		
		// the module id of this report
		$module = $this->_params["moduleId"];
		
		if(isset($_SESSION['receipt']['selected'])){
		    
		    $receipt_obj = new Table_Receipts();
		    $receipt = $receipt_obj->getDataById($_SESSION['receipt']['selected']);
		    
		$header = $this->renderBreadCrumb($receipt->title);
		    
		$html= '<!-- BEGIN PAGE HEADER-->'.$header.'
		    <!-- END PAGE HEADER-->
		    <!-- BEGIN PAGE CONTENT-->
		    <div class="row">
		      <div class="col-md-12">
				<br>
		    	<form id="fileupload" action="index.php?c=upload" method="POST" enctype="multipart/form-data">
    				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    		    	<div class="row fileupload-buttonbar">
    				    <div class="col-lg-7">
    		    		<!-- The fileinput-button span is used to style the file input field as button -->
    		    		<span class="btn green fileinput-button">
    						<i class="fa fa-plus"></i>
    		    		    <span>Shto Imazhe... </span>
    		    			<input type="file" name="files[]" multiple="">
    		    		</span>
    		    		<button type="submit" class="btn blue start">
    		    	        <i class="fa fa-upload"></i>
    		    		    <span>Fillo Ngarkimin </span>
    		    		</button>
    		    		<button type="reset" class="btn warning cancel">
    						<i class="fa fa-ban-circle"></i>
    		    			<span>Anullo Ngarkimin </span>
    		    		</button>
    		    		<button type="button" class="btn red delete">
    						<i class="fa fa-trash"></i>
    		    			<span>Fshi </span>
    		    		</button>
    		    		<input type="checkbox" class="toggle">
    		    		<!-- The global file processing state -->
    		    		<span class="fileupload-process"></span>
    		    	</div>
    		    	<!-- The global progress information -->
    				<div class="col-lg-5 fileupload-progress fade">
    		    		<!-- The global progress bar -->
    		    		<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
    		    		    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
    		    		</div>
    		    		<!-- The extended global progress information -->
    		    		<div class="progress-extended">
    		    			&nbsp;
    		    		</div>
    		    	</div>
    		    </div>
    		    <!-- The table listing the files available for upload/download -->
    		    <table role="presentation" class="table table-striped clearfix">
    		      <tbody class="files"></tbody>
    		    </table>
		    </form>
		    
		  </div>
        </div>
				
		<!-- The blueimp Gallery widget -->
		<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
		    <div class="slides"></div>
		    <h3 class="title"></h3>
		    <a class="prev">&lsaquo;</a>
		    <a class="next">&rsaquo;</a>
		    <a class="close">&times;</a>
		    <a class="play-pause"></a>
		    <ol class="indicator"></ol>
		</div>
		<!-- END PAGE CONTENT-->';
		
		$html .= '<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
                    <script id="template-upload" type="text/x-tmpl">
                    {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-upload fade">
                            <td>
                                <span class="preview"></span>
                            </td>
                            <td>
                                <p class="name">{%=file.name%}</p>
                                <strong class="error text-danger label label-danger"></strong>
                            </td>
                            <td>
                                <p class="size">Processing...</p>
                                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                                </div>
                            </td>
                            <td>
                                {% if (!i && !o.options.autoUpload) { %}
                                    <button class="btn blue start" disabled>
                                        <i class="fa fa-upload"></i>
                                        <span>Start</span>
                                    </button>
                                {% } %}
                                {% if (!i) { %}
                                    <button class="btn red cancel">
                                        <i class="fa fa-ban"></i>
                                        <span>Cancel</span>
                                    </button>
                                {% } %}
                            </td>
                        </tr>
                    {% } %}
                    </script>
                    <!-- The template to display files available for download -->
                    <script id="template-download" type="text/x-tmpl">
                            {% for (var i=0, file; file=o.files[i]; i++) { %}
                                <tr class="template-download fade">
                                    <td>
                                        <span class="preview">
                                            {% if (file.thumbnailUrl) { %}
                                                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                                            {% } %}
                                        </span>
                                    </td>
                                    <td>
                                        <p class="name">
                                            {% if (file.url) { %}
                                                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?\'data-gallery\':\'\'%}>{%=file.name%}</a>
                                            {% } else { %}
                                                <span>{%=file.name%}</span>
                                            {% } %}
                                        </p>
                                        {% if (file.error) { %}
                                            <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                                        {% } %}
                                    </td>
                                    <td>
                                        <span class="size">{%=o.formatFileSize(file.size)%}</span>
                                    </td>
                                    <td>
                                        {% if (file.deleteUrl) { %}
                                            <button class="btn red delete btn-sm" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
                                                <i class="fa fa-trash-o"></i>
                                                <span>Delete</span>
                                            </button>
                                            <input type="checkbox" name="delete" value="1" class="toggle">
                                        {% } else { %}
                                            <button class="btn yellow cancel btn-sm">
                                                <i class="fa fa-ban"></i>
                                                <span>Cancel</span>
                                            </button>
                                        {% } %}
                                    </td>
                                </tr>
                            {% } %}
                        </script>
                    
                    <!-- BEGIN PAGE LEVEL PLUGINS -->
                    <script src="template/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
                    <!-- END PAGE LEVEL PLUGINS-->
                    <!-- BEGIN:File Upload Plugin JS files-->
                    <!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
                    <script src="template/global/plugins/jquery-file-upload/js/vendor/jquery.ui.widget.js"></script>
                    <!-- The Templates plugin is included to render the upload/download listings -->
                    <script src="template/global/plugins/jquery-file-upload/js/vendor/tmpl.min.js"></script>
                    <!-- The Load Image plugin is included for the preview images and image resizing functionality -->
                    <script src="template/global/plugins/jquery-file-upload/js/vendor/load-image.min.js"></script>
                    <!-- The Canvas to Blob plugin is included for image resizing functionality -->
                    <script src="template/global/plugins/jquery-file-upload/js/vendor/canvas-to-blob.min.js"></script>
                    <!-- blueimp Gallery script -->
                    <script src="template/global/plugins/jquery-file-upload/blueimp-gallery/jquery.blueimp-gallery.min.js"></script>
                    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.iframe-transport.js"></script>
                    <!-- The basic File Upload plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload.js"></script>
                    <!-- The File Upload processing plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload-process.js"></script>
                    <!-- The File Upload image preview & resize plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload-image.js"></script>
                    <!-- The File Upload audio preview plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload-audio.js"></script>
                    <!-- The File Upload video preview plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload-video.js"></script>
                    <!-- The File Upload validation plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload-validate.js"></script>
                    <!-- The File Upload user interface plugin -->
                    <script src="template/global/plugins/jquery-file-upload/js/jquery.fileupload-ui.js"></script>
                    <!-- The main application script -->
                    <!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
                    <!--[if (gte IE 8)&(lt IE 10)]>
                        <script src="template/global/plugins/jquery-file-upload/js/cors/jquery.xdr-transport.js"></script>
                        <![endif]-->
                    <!-- END:File Upload Plugin JS files-->';

		    $html.='<script>
                    jQuery(document).ready(function() {    
                    	FormFileUpload.init();
                    });';
		    
		    echo $html;
		    
		}else{
		    echo "Zgjidhni nje recete fillimisht!";
		}
				
	}
}