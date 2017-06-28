<?php
class Ajax_Response_Gallery{
	
	//Get fully functional gallery with category change and record assignment
	public function getGalleryHtml($filter = array()){
		
		$args = Utility_Functions::argsToArray($filter);
		
		$receiptId = $args['receiptId'];
		
		$imagesDir = 'files/receipts/' . $receiptId.'/medium';
		$images = Utility_Functions::getImages($imagesDir);
		
		$html = "<div class='ui-widget ui-helper-clearfix'>
				<ul id='unconnected' class='unconnected ui-helper-reset ui-helper-clearfix'>";
		
		$i = 1;
		foreach ($images as $img) {
		    $date = new DateTime();
		    
			$img_path = $imagesDir."/".$img['file']."?".$date->getTimestamp();
			$imgId = $img['file'];
			
			$html .="<li class='ui-widget-content ui-corner-tr' id='$imgId'>
			<h5 class='ui-widget-header'>$i</h5>
			<img src='$img_path'  width='96' height='72' alt='$i'/>
			<a href='$img_path' title='Zmadho Imazhin' class='ui-icon ui-icon-zoomin'>Zmadho Imazhin</a>
			<a class='assignProfile ui-icon ui-icon-minusthick' title='Profil Horizontal'>Caktoje Si Imazh Profili</a>
			<a class='assignVerticalProfile ui-icon ui-icon-grip-solid-vertical' title='Profil Vertikal'>Caktoje Si Imazh Profili Vertikal</a>
			</li>";
				
			$i++;
		}
				
		$html .='</ul>';
				 	
		$html.='</div>
				 
				</div>
				<div id="dialog-confirm"  style="display:none" title="Konfirmo Vendosjen si Imazh Profili!">
  					<p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>Ky imazh do te jete imazhi i profilit per kete recete. Jeni te sigurte?</p>
				</div>
				';
		
		$html .= '<script type="text/javascript" src="js/archive.js"></script>';
		
		$html .="<script type = 'text/javascript'>
				
				$(document).ready(function() {
					$('a.ui-icon-zoomin').fancybox({
     			    	padding : 0,
     			    	'type'         : 'image',
						helpers		: {
							title	: { type : 'inside' },
						buttons	: {
							tpl        : '<div id=\"fancybox-buttons\"><ul><li><a class=\"btnToggle\" title=\"Toggle size\" href=\"javascript:;\"></a></li><li><a class=\"btnClose\" title=\"Close\" href=\"javascript:jQuery.fancybox.close();\"></a></li></ul></div>'
						}
				}
    				});
				
    				
    				//Button Assign Profile OnClick open dialog
					$( '.assignProfile' ).click(function() {
					
						//get image id from clicked li element
							var image_file = $(this).closest('li').attr('id');
					
						$( '#dialog-confirm' ).dialog({
	      					resizable: false,
	     					height:250,
	     					modal: true,
	      					buttons: {
	        					'Vendose Foto Horizontale Profili': function() {
	        						
	        						$.ajax({
										url:'index.php?c=ajax',
										dataType: 'html',
										type: 'GET',
										data: {
											loadClass: 'Ajax_Response_Gallery',
											method: 'assignProfileImage',
											parameter: 'imgFile:'+image_file+';receiptId:".$receiptId."'
										},
										context: this,
										success: function(response){
											alert('Foto u vendos si imazh profili horizontal');
										}
									});
	        					        						
	          						$( this ).dialog( 'close' );
	        					},
	        					Cancel: function() {
	          						$( this ).dialog( 'close' );
	        					}
	      					}
	    				});
					});
											    
					//Button Assign Vertical Profile OnClick open dialog
					$( '.assignVerticalProfile' ).click(function() {
					
						//get image id from clicked li element
							var image_file = $(this).closest('li').attr('id');
					
						$( '#dialog-confirm' ).dialog({
	      					resizable: false,
	     					height:250,
	     					modal: true,
	      					buttons: {
	        					'Vendose Foto Vertikale Profili': function() {
	        						
	        						$.ajax({
										url:'index.php?c=ajax',
										dataType: 'html',
										type: 'GET',
										data: {
											loadClass: 'Ajax_Response_Gallery',
											method: 'assignVerticalProfileImage',
											parameter: 'imgFile:'+image_file+';receiptId:".$receiptId."'
										},
										context: this,
										success: function(response){
											alert('Foto u vendos si imazh profili vertikal');
										}
									});
	        					        						
	          						$( this ).dialog( 'close' );
	        					},
	        					Cancel: function() {
	          						$( this ).dialog( 'close' );
	        					}
	      					}
	    				});
					});
											    
											    
    				
				})";
		
		
		echo $html;
	}
	
	public function assignProfileImage($filter = array()){
		$args = Utility_Functions::argsToArray($filter);
		
		$fileName = $args['imgFile'];
		$receiptId = $args['receiptId'];
		
		$business = new Table_Receipts();
		
		$output = $business->assignProfileImage($receiptId, $fileName);
		echo json_encode($output);
	}
	
	public function assignVerticalProfileImage($filter = array()){
	    $args = Utility_Functions::argsToArray($filter);
	
	    $fileName = $args['imgFile'];
	    $receiptId = $args['receiptId'];
	
	    $business = new Table_Receipts();
	
	    $output = $business->assignVerticalProfileImage($receiptId, $fileName);
	    echo json_encode($output);
	}

	
	
}