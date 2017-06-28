<?php

class TreeDisplay {
	
	
	/**
	 * Stores all the javascript code to be
	 * returned in the browser.
	 * @var String
	 */
	protected $jscript;
	
	/**
	 * Stores the Id of the div container of the tree.
	 * @var String
	 */
	protected $treeId;
	
	
	protected $nodeIdSelected;
	

	/**
	 * 
	 * @param String $id The Id of the div container of the tree.
	 */
	public function __construct($id){
		
		// set the tree id div container.
		$this->treeId = $id;
		
		$output ='
		$(document).ajaxStart($.blockUI({
			css: {
        		opacity: .7
    		},
			message: "<h1><img src=\"images/ajax-loader.gif\" /> Modulet po ngarkohen...</h1>",
        	timeout:500
		})).ajaxStop($.unblockUI);
		
		$("#'.$this->getTreeId().'").dynatree({
	   		minExpandLevel:3,
	    	// Call a URL on the server like this:
	    	initAjax: {
	      		url: "index.php?c=ajax",
		  		data: {
					loadClass: "Ajax_Response_Utility",
					method: "loadTree",
					parameter: 48
				}
	      	},
		
	      	onActivate: function(node) {
		        $.blockUI({
					message: "<h1><img src=\"images/ajax-loader.gif\" /> Te dhenat po ngarkohen...</h1>",
		        });
	        	$.ajax({
		        	type: "GET",
	        	  	url: "index.php?c=ajax",
	        	  	data: {
	        		  	loadClass: "Ajax_Response_Report",
	        		  	method: node.data.form,
	        		  	parameter: "moduleId:" +  node.data.key
	        	  	},
	        	  	context: document.body,
	        	  	success: function(response){
	        	    	$(".ui-layout-center .ui-layout-content").html(response);
	        	  	}
	        	});
	      	},
		
	      	onDeactivate: function(node) {
	        	$("#echoActive").text("");
	      	},
		
	      	onPostInit: function(request) {';
		
		if (isset ( $_POST ['treeNodeId'] )) { 
			$this->setNodeIdSelected($_POST ['treeNodeId']);
			
			$output .= '
		    	var node = $("#'.$this->getTreeId().'").dynatree("getTree").getNodeByKey("'.$this->getNodeIdSelected().'");
		    	node.activateSilently();  ';
		}else {
			$output .= '
	    		var node = $("#'.$this->getTreeId().'").dynatree("getTree").getNodeByKey("'.
			    		Zend_Registry::get ( 'config' )->default_reports_tree_id.'");
	    		if(node){
	        		node.activate();
	        	}else{
            		if( $("#'.$this->getTreeId().'").dynatree("getTree").count()<=1 ){
	        	    	$("#leftcolumn").hide();
	        			$(".ui-layout-center .ui-layout-content").html(" Ju nuk keni te drejta per te aksesuar kete modul.");
            		}else{
            			//activate the first node
            			$(".ui-layout-center .ui-layout-content").html(" Ju lutem zgjidhni nje nga elementet e pemes ne te majte.");
            		}
	        	}// end else';
		}
		
		$output .= '
			}// end function
		}); // end dynatree';
			
		$this->setScript($output);
	}
	
	/**
	 * Return the Id of the div element identifying the tree.
	 * @return string
	 */
	public function getTreeId(){
		
		return $this->treeId;
	}
	
	/**
	 * Render the Tree on the screen.
	 * @param $domElement The div element Id where the tree should be rendered.
	 * @return string
	 */
	public function render($domElement){
    	 $test = $this->getScript();
    	 return $test;
	}
	
	
	/**
	 * @return the $nodeIdSelected
	 */
	public function getNodeIdSelected() {
		return $this->nodeIdSelected;
	}
	
	/**
	 * @param field_type $nodeIdSelected
	 */
	private function setNodeIdSelected($nodeIdSelected) {
		$this->nodeIdSelected = $nodeIdSelected;
	}
	
	/**
	 * Sets a javascript or jquery code.
	 * @param String $script
	 */
	public function setScript($script){
		$this->jscript.= $script."\r\n";
	}
	
	/**
	 * Get the javascript jquery code.
	 * @param String $script
	 * @return string
	 */
	public function getScript(){
		return "\r\n<script type=\"text/javascript\">\r\n $(function() {\r\n" . $this->jscript . "\r\n}); \r\n</script>\r\n";
	}
}

?>
