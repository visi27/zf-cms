
  $(function() {
    // there's the unconnected and the connected
    var $unconnected = $( "#unconnected" ),
      $connected = $( "#connected" );
 
    // let the unconnected items be draggable
    $( "li", $unconnected ).draggable({
      cancel: "a.ui-icon", // clicking an icon won't initiate dragging
      revert: "invalid", // when not dropped, the item will revert back to its initial position
      containment: "document",
      helper: "clone",
      cursor: "move"
    });
    
    // let the connected items be draggable
    $( "li", $connected ).draggable({
      cancel: "a.ui-icon", // clicking an icon won't initiate dragging
      revert: "invalid", // when not dropped, the item will revert back to its initial position
      containment: "document",
      helper: "clone",
      cursor: "move"
    });
 
    // let the trash be droppable, accepting the unconnected items
    $connected.droppable({
      accept: "#unconnected > li",
      activeClass: "ui-state-highlight",
      drop: function( event, ui ) {
    	  $.blockUI();
    	  connectImage( ui.draggable );
    	  $.unblockUI();
      }
    });
 
    // let the unconnected be droppable as well, accepting items from the trash
    $unconnected.droppable({
      accept: "#connected li",
      activeClass: "custom-state-active",
      drop: function( event, ui ) {
    	  $.blockUI();
    	  recycleImage( ui.draggable );
    	  $.unblockUI();
      }
    });
 
     //image deletion function
    function connectImage( $item ) {
    	$.ajax({
			url:"index.php?c=ajax",
			type: "GET",
			data: {
				loadClass: "Ajax_Response_Archive",
				method: "assignPageToRecord",
				parameter: "pageId:"+ $item.attr("id") + ";" + "recordId:"+ $.cookie("recordId")
			},
			context: this,
			cache: false,
			dataType: "json",
			success: function(response){
				if (response === true) {
					//alert("Page Updated!");
				}
			}
		});
      $item.fadeOut(function() {
        var $list = $( "ul", $connected ).length ?
        $( "ul", $connected ) :
        $( "<ul class='unconnected ui-helper-reset'/>" ).appendTo( $connected );
        
        
        $item.find('a.ui-icon-triangle-2-e-w').remove();
        $item.appendTo( $list ).fadeIn(function() {
          $item
            .animate({ width: "48px" })
            .find( "img" )
              .animate({ height: "36px" });
        });
      });
    }
 
    // image unconnect function
    	var category_icon = "<a class='ui-icon ui-icon-triangle-2-e-w' title='Zmadho Imazhin'>Ndrysho Kategorine</a>";
       function recycleImage( $item ) {
    	
			$.ajax({
				url:"index.php?c=ajax",
				type: "GET",
				data: {
					loadClass: "Ajax_Response_Archive",
					method: "unAssignPage",
					parameter: "pageId:"+ $item.attr("id")
				},
				context: this,
				cache: false,
				dataType: "json",
				success: function(response){
					if (response === true) {
						//alert("Page Updated!");
					}
				}
			});
	      $item.fadeOut(function() {
	        $item
	          .find( "a.ui-icon-refresh" )
	            .remove()
	          .end()
	          .css( "width", "96px")
	          .append( category_icon )
	          .find( "img" )
	            .css( "height", "72px" )
	          .end()
	          .appendTo( $unconnected )
	          .fadeIn();
	      });
    }
 
    // image preview function, demonstrating the ui.dialog used as a modal window
    function viewLargerImage( $link ) {
      var src = $link.attr( "href" ),
        title = $link.siblings( "img" ).attr( "alt" ),
        $modal = $( "img[src$='" + src + "']" );
 
      if ( $modal.length ) {
    	  console.log($modal);
    	  $modal.dialog({
				autoOpen: false,
				show: "blind",
				hide: "slide",
				modal: false});
        $modal.dialog( "open" );
      } else {
        var img = $( "<img alt='" + title + "' width='384' height='288' style='display: none; padding: 8px;' />" )
          .attr( "src", src ).appendTo( "body" );
        setTimeout(function() {
          img.dialog({
            title: title,
            width: 400,
            modal: true
          });
        }, 1 );
      }
    }
 
     //resolve the icons behavior with event delegation
//    $( "ul.unconnected > li" ).click(function( event ) {
//      var $item = $( this ),
//        $target = $( event.target );
// 
//      if ( $target.is( "a.ui-icon-check" ) ) {
//        connectImage( $item );
//      }else if ( $target.is( "a.ui-icon-refresh" ) ) {
//        recycleImage( $item );
//      }
// 
//      return false;
//    });
  });
