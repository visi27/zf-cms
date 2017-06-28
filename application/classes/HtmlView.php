<?php

class HtmlView
{

    private static $rowsPerPage = 14;

    public static function getRowsPerPage()
    {
        return self::$rowsPerPage;
    }

    public static function displayTable(Zend_Db_Table_Rowset $rowSet, $divId, $columnAlias = array(), $rowSelection = false, $identityField = null, $headerText = "", $paginator = true, $toolbarElements = array(), $controller, $openPage = 1, $allowSort = true, $itemsPerPage = null)
    {
        if (count($rowSet) > 0) {
            
            $itemsPerPage = ($itemsPerPage) ? $itemsPerPage : self::getRowsPerPage();
            
            // convert the rowset into an array
            $dataArray = $rowSet->toArray();
            
            // get column names from the result set
            $rowSetKeys = array_keys($dataArray[0]);
            
            // create the column display names array
            $columnNames = (count($columnAlias) == count($rowSetKeys)) ? $columnAlias : $rowSetKeys;
            
            // create the identity field (parent key)
            $identityField = ($identityField == null) ? $rowSetKeys[0] : $identityField;
            
            try { // PAGINATOR
                 // set sort parameters from request
                $sortField = isset($_GET['s']) ? htmlentities($_GET['s']) : $identityField;
                $sortDir = isset($_GET['d']) ? htmlentities($_GET['d']) : 'asc';
                
                // $page=$this->_getParam('page',1);
                $pager = Zend_Paginator::factory($rowSet);
                
                if ($paginator) {
                    // set page number from request
                    $openPage = isset($_GET['p']) ? (int) htmlentities($_GET['p']) : $openPage;
                    
                    // set number of items per page from request
                    $itemsPerPage = isset($_GET['i']) ? (int) htmlentities($_GET['i']) : $itemsPerPage;
                }
                // set the page
                $pager->setCurrentPageNumber($openPage);
                
                // set the items per page
                $pager->setItemCountPerPage($itemsPerPage);
                
                // set number of pages in page range
                $pager->setPageRange(5);
                
                // get page data
                $pages = $pager->getPages();
                
                // build first page link
                $pageLinks = array();
                $separator = '';
                $pageLinks[] = self::getLink($pages->first, $itemsPerPage, $sortField, $sortDir, '<<', $toolbarElements, $controller);
                
                // build previous page link
                if (! empty($pages->previous)) {
                    $pageLinks[] = self::getLink($pages->previous, $itemsPerPage, $sortField, $sortDir, '<li class="previous-off">< Para</li>', $toolbarElements, $controller);
                }
                
                // build page number links
                foreach ($pages->pagesInRange as $x) {
                    if ($x == $pages->current) {
                        $pageLinks[] = '<li class="active">' . $x . '</li>';
                    } else {
                        $pageLinks[] = self::getLink($x, $itemsPerPage, $sortField, $sortDir, '<li>' . $x . '</li>', $toolbarElements, $controller);
                    }
                }
                
                // build next page link
                if (! empty($pages->next)) {
                    $pageLinks[] = self::getLink($pages->next, $itemsPerPage, $sortField, $sortDir, '<li class="next">Pas ></li>', $toolbarElements, $controller);
                }
                
                // build last page link
                $pageLinks[] = self::getLink($pages->last, $itemsPerPage, $sortField, $sortDir, '>>', $toolbarElements, $controller);
            } catch (Exception $e) {
                die('ERROR: ' . $e->getMessage());
            }
            
            if ($rowSelection == true)
                $script = '<script>			
			jQuery(function() {
	  			jQuery("tr:odd").addClass("odd");
	  			
		        jQuery("tr").hover(	  			
		  			function() {	  			
			  			jQuery(this).contents("td").addClass("my-hoveredTd");				
			        },
		  			
	        		function() {
			  			var thisId = jQuery(this).attr("id");
			  			
			  			if( jQuery( "#' . $divId . ' table[id=\'data-table\'] tr[id=\'"+thisId+"\'] input[name=\'rowSelectionRadio\']").attr(\'checked\') != "checked"){
			    			jQuery(this).contents("td").removeClass("my-hoveredTd");
			    	}	   				
        		}); // end hover
	    			
		    	jQuery(".tdContent").click(function(){
	      			var parentId = jQuery(this).parent().attr("id");
	    			jQuery("#' . $divId . ' tr[id=\'"+ parentId +"\'] input[name=\'rowSelectionRadio\']").attr(\'checked\', \'checked\');
	  				jQuery("#' . $divId . ' tr[id=\'"+ parentId +"\'] input[name=\'rowSelectionRadio\']").trigger("change");
				});		
    		});	  			
		</script>';
            
            $output = "
		<div id=\"$divId\" class=\"gridPannel\" title=\"Existing Data\">";
            
            if (! empty($headerText))
                $output .= "<div class=\"headerWrap\">
					<label id=\"totalRows\" style=\"float:left;\">$headerText, Total:" . count($rowSet) . "</label>
				</div>";
            
            $output .= "<table id=\"data-table\" class=\"\">
				<thead>
					<tr class=\"\">";
            if ($rowSelection) {
                $output .= "<th></th>";
            }
            $k = 0;
            foreach ($columnNames as $column) {
                
                $output .= "<th nowrap='nowrap' id='$column'><div style='float:left'>" . $column . "</div>";
                if ($allowSort == true) {
                    $sort = $rowSetKeys[$k];
                    $sort_asc_link = self::getLink($openPage, $itemsPerPage, $sort, "asc", '<img src="images/icons/sort_asc.png" alt="Sort Asc" border="0"/>', $toolbarElements, $controller);
                    $sort_desc_link = self::getLink($openPage, $itemsPerPage, $sort, "desc", '<img  src="images/icons/sort_desc.png" alt="Sort Desc" border="0"/>', $toolbarElements, $controller);
                    $output .= "<div class= 'sortIcons'>" . $sort_asc_link . $sort_desc_link . "</div>";
                }
                $output .= "</th>";
                $k ++;
            }
            $output .= "</tr>
				</thead>
				<tbody>";
            foreach ($pager->getCurrentItems() as $item) {
                $output .= "<tr id=\"$item[$identityField]\">";
                if ($rowSelection) {
                    $output .= "<td><input type=\"radio\" class='dataContent' name=\"rowSelectionRadio\" value=\"$item[$identityField]\"></td>";
                }
                foreach ($item as $column => $value) {
                    $output .= "<td class='tdContent' id=\"" . $column . "_td\"> 	 
									<label class='dataContent' title='value'>$value</label>
								   </td>";
                }
                $output .= "</tr>";
            }
            $output .= "</tbody>
			</table>";
            
            if ($paginator) {
                $output .= "
				<div id=\"links\" align=\"center\" class=\"center\" >	
					<ul id=\"pagination\">
				    " . implode($pageLinks, $separator) . " 
				    </ul>
				</div>";
            }
            
            $output .= "</div>";
        } else {
            $output = "</br>Aktualisht,nuk ka te dhena per tu shfaqur !</br></br>";
        }
        return $output . $script;
    }

    /**
     * VERSION draft 2 of HtmlTable
     * 
     * @todo still working on it
     * @param Zend_Db_Table_Rowset $rowSet            
     * @param unknown_type $divId            
     * @param unknown_type $columnAlias            
     * @param unknown_type $rowSelection            
     * @param unknown_type $identityField            
     * @param unknown_type $headerText            
     * @param unknown_type $paginator            
     * @param unknown_type $toolbarElements            
     * @param unknown_type $controller            
     * @param unknown_type $openPage            
     * @param unknown_type $allowSort            
     * @param unknown_type $itemsPerPage            
     * @param unknown_type $sortField            
     * @return string
     */
    public static function rowsetToHtml(Zend_Db_Table_Rowset $source, $divId, $columnAlias = array(), $rowSelection = false, $identityField = null, $headerText = "", $paginator = true, $toolbarElements = array(), $controller, $openPage = 1, $allowSort = true, $itemsPerPage = null, $sortField = null, $sortDir = null, Array $tooltipCols = null)
    {
        if (count($source) > 0) {
            
            $itemsPerPage = ($itemsPerPage) ? $itemsPerPage : self::getRowsPerPage();
            
            // convert the rowset into an array
            $dataArray = $source->toArray();
            
            // get column names from the result set
            $rowSetKeys = array_keys($dataArray[0]);
            
            // create the column display names array
            $columnNames = (count($columnAlias) == count($rowSetKeys)) ? $columnAlias : $rowSetKeys;
            
            // create the identity field (parent key)
            $identityField = ($identityField == null) ? $rowSetKeys[0] : $identityField;
            
            try { // PAGINATOR
                if (is_array($sortField)) {
                    $sortField = implode(";", $sortField);
                }
                // set sort parameters from request
                $sortField = isset($_GET['s']) ? htmlentities($_GET['s']) : $sortField;
                $sortDir = isset($_GET['d']) ? htmlentities($_GET['d']) : $sortDir;
                
                // $page=$this->_getParam('page',1);
                $pager = Zend_Paginator::factory($source);
                
                if ($paginator) {
                    // set page number from request
                    $openPage = isset($_GET['p']) ? (int) htmlentities($_GET['p']) : $openPage;
                    
                    // set number of items per page from request
                    $itemsPerPage = isset($_GET['i']) ? (int) htmlentities($_GET['i']) : $itemsPerPage;
                }
                // set the page
                $pager->setCurrentPageNumber($openPage);
                
                // set the items per page
                $pager->setItemCountPerPage($itemsPerPage);
                
                // set number of pages in page range
                $pager->setPageRange(5);
                
                // get page data
                $pages = $pager->getPages();
                
                // build first page link
                $pageLinks = array();
                $separator = '';
                $pageLinks[] = self::getLink($pages->first, $itemsPerPage, $sortField, $sortDir, '<<', $toolbarElements, $controller);
                
                // build previous page link
                if (! empty($pages->previous)) {
                    $pageLinks[] = self::getLink($pages->previous, $itemsPerPage, $sortField, $sortDir, '<li class="previous-off">< Para</li>', $toolbarElements, $controller);
                }
                
                // build page number links
                foreach ($pages->pagesInRange as $x) {
                    if ($x == $pages->current) {
                        $pageLinks[] = '<li class="active">' . $x . '</li>';
                    } else {
                        $pageLinks[] = self::getLink($x, $itemsPerPage, $sortField, $sortDir, '<li>' . $x . '</li>', $toolbarElements, $controller);
                    }
                }
                
                // build next page link
                if (! empty($pages->next)) {
                    $pageLinks[] = self::getLink($pages->next, $itemsPerPage, $sortField, $sortDir, '<li class="next">Pas ></li>', $toolbarElements, $controller);
                }
                
                // build last page link
                $pageLinks[] = self::getLink($pages->last, $itemsPerPage, $sortField, $sortDir, '>>', $toolbarElements, $controller);
            } catch (Exception $e) {
                die('ERROR: ' . $e->getMessage());
            }
            
            if ($rowSelection == true)
                $script = '<script>
			jQuery(function() {
	  			jQuery("tr:odd").addClass("odd");
	
		        jQuery("tr").hover(
		  			function() {
			  			jQuery(this).contents("td").addClass("my-hoveredTd");
			        },
		  
	        		function() {
			  			var thisId = jQuery(this).attr("id");
	
			  			if( jQuery( "#' . $divId . ' table[id=\'data-table\'] tr[id=\'"+thisId+"\'] input[name=\'rowSelectionRadio\']").attr(\'checked\') != "checked"){
			    			jQuery(this).contents("td").removeClass("my-hoveredTd");
			    	}
        		}); // end hover
	
		    	jQuery(".tdContent").click(function(){
	      			var parentId = jQuery(this).parent().attr("id");
	    			jQuery("#' . $divId . ' tr[id=\'"+ parentId +"\'] input[name=\'rowSelectionRadio\']").attr(\'checked\', \'checked\');
	  				jQuery("#' . $divId . ' tr[id=\'"+ parentId +"\'] input[name=\'rowSelectionRadio\']").trigger("change");
				});
    		});
		</script>';
            
            $output = "
			<div id=\"$divId\" class=\"gridPannel\">";
            
            if (! empty($headerText))
                $output .= "<div class=\"headerWrap\">
				<div id=\"totalRows\">$headerText, Total: " . count($source) . "</div>
				</div>";
            
            $output .= "<table id=\"data-table\" class=\"\">
				<thead>
					<tr class=\"\">";
            if ($rowSelection) {
                $output .= "<th></th>";
            }
            $k = 0;
            foreach ($columnNames as $column) {
                
                $output .= "<th nowrap='nowrap' id='$column'><div style='float:left;padding-right:5px;'>" . $column . "</div>";
                if ($allowSort == true) {
                    $sort = $rowSetKeys[$k];
                    $sort_asc_link = self::getLink($openPage, $itemsPerPage, $sort, "asc", '<img src="images/icons/sort_asc.png" alt="Sort Asc" border="0"/>', $toolbarElements, $controller);
                    $sort_desc_link = self::getLink($openPage, $itemsPerPage, $sort, "desc", '<img  src="images/icons/sort_desc.png" alt="Sort Desc" border="0"/>', $toolbarElements, $controller);
                    $output .= "<div class= 'sortIcons'>" . $sort_asc_link . $sort_desc_link . "</div>";
                }
                $output .= "</th>";
                $k ++;
            }
            $output .= "</tr>
						</thead>
						<tbody>";
            
            $withTooltip = (! empty($tooltipCols) && ($tooltipCols != null));
            foreach ($pager->getCurrentItems() as $item) {
                
                // define tooltip value for row if there is one
                $tooltip = "";
                if ($withTooltip) {
                    foreach ($rowSetKeys as $i => $column) {
                        $label = $columnNames[$i];
                        $value = $item["$column"];
                        
                        $isEmpty = (is_null($value) || $value == "");
                        
                        if (! $isEmpty && in_array($column, $tooltipCols)) {
                            if ($tooltip != "")
                                $tooltip .= "\r\n";
                            $tooltip .= "$label  :  $value";
                        }
                    }
                }
                $output .= "<tr id=\"$item[$identityField]\" title=\"$tooltip\">";
                if ($rowSelection) {
                    $output .= "<td><input type=\"radio\" class='dataContent' name=\"rowSelectionRadio\" value=\"$item[$identityField]\"></td>";
                }
                foreach ($item as $column => $value) {
                    $output .= "<td class='tdContent' id=\"" . $column . "_td\">
									<label class='dataContent'>$value</label>
										</td>";
                }
                $output .= "</tr>";
            }
            $output .= "</tbody>
							</table>";
            
            if ($paginator) {
                $output .= "
					<div id=\"links\" align=\"center\" class=\"center\" >
					<ul id=\"pagination\">
				    " . implode($pageLinks, $separator) . "
				    </ul>
					    </div>";
            }
            
            $output .= "</div>";
        } else {
            $output = "</br>Aktualisht,nuk ka te dhena per tu shfaqur !</br></br>";
        }
        return $output . $script;
    }

    private static function getLink($page, $itemsPerPage, $sortField, $sortDir, $label, $filterElements = array(), $controller)
    {
        
        // set some parameters
        $ajaxController = Zend_Registry::get('config')->ajax_controller; // controller to be called
        $classToCall = "Ajax_Response_" . ucfirst($controller);
        $dynaTreeId = ".ui-layout-west .ui-layout-content"; // used to get the current method to be called and its Id
        $respElemId = ".page-content-wrapper .page-content";
        
        $node_id = $_SESSION['system']['moduleId'];
        $modules = new Table_Modules();
        $module = $modules->getModuleById($node_id);
        $node_form = $module->form_name;
        
        $link = '<a href="#" onclick=\'
			
    		var node_id = ' . $_SESSION['system']['moduleId'] . ';
 	  		var toolbarParam = ""; ';
        
        if (count($filterElements)) {
            foreach ($filterElements as $filterElement) {
                
                $link .= 'toolbarParam += "' . $filterElement . ':" + jQuery("#' . $filterElement . '").val()+ ";";';
            }
        }
        
        $link .= '
	    		jQuery.ajax({
		          type: "GET",
	        	  url: "index.php?c=' . $ajaxController . '",
	        	  data: {
	        		  loadClass: "' . $classToCall . '",
	        		  method: "' . $node_form . '",
		        	  parameter:  "moduleId:" + node_id + ";" + toolbarParam + jQuery("#toolbarFilter").val(),
		        	  p: "' . $page . '",
		        	  i: "' . $itemsPerPage . '",
		        	  s: "' . $sortField . '",
		        	  d: "' . $sortDir . '"
	        	  },
	        	  context: document.body,
	        	  success: function(response){
	        	    jQuery("' . $respElemId . '").html(response);
	        	  }
	        	});  \' title=\'Page:' . $page . '\'>' . $label . '</a>';
        
        return $link;
    }

    public static function displayButton($id, $text, $jqueryButton = array())
    {
        if (count($jqueryButton) > 0)
            $script = '<script>
		jQuery( "#' . $id . '" ).button({
			text: ' . $jqueryButton["showtext"] . ',
			icons: {
				primary: "' . $jqueryButton["primIcon"] . '",
	        	secondary: "' . $jqueryButton["icon"] . '"
			}
		}).click(function() {
			' . $jqueryButton["click"] . '
		});
		' . $jqueryButton["script"] . ';
		</script>';
        
        return "<button id=\"$id\" class=\"btn btn-primary\">$text</button>\n$script";
    }
}
?>