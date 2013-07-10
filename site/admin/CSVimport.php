<?php

/*
 * CSV import form + guide
 *************************************************/

/* required functions */
require_once('../../functions/functions.php'); 

/* verify that user is logged in */
isUserAuthenticated(false);

# permissions
$permission = checkSubnetPermission ($_POST['subnetId']);

# die if write not permitted
if($permission < 2) { die("<div class='alert alert-error'>"._('You cannot write to this subnet')."!</div>"); }

/* verify post */
CheckReferrer();
?>

<!-- header -->
<div class="pHeader"><?php print _('XLS / CSV subnet import'); ?></div>


<!-- content -->
<div class="pContent">
	
	<?php  
	# get custom fields
	#get all custom fields!
	$myFields = getCustomIPaddrFields();
	if(sizeof($myFields) > 0) {
		$custFields = " | ";
		foreach($myFields as $myField) {
			$custFields .= "$myField[name] | ";
		}
		# remove last |
		$custFields = substr($custFields, 0,-2);
	}
	?>

	<!-- notes -->
	<?php print _('To successfully import data please use the following XLS/CSV structure:<br>( ip | State | Description | hostname | MAC | Owner | Device | Port | Note '); ?> <?php print $custFields; ?> )
	<br>
	<img src="css/images/csvuploadexample.jpg" style="border:1px solid #999999">
	<br><br>

	<!-- Upload file form -->
	<h4>1.) <?php print _('Upload file'); ?>:</h4>
	<hr>
	
	<form id="csvimport" method="post" action="site/admin/CSVimportVerify.php" enctype="multipart/form-data">
	<div id="drop">
		<input type="file" name="file" id="csvfile" style="display:none;">

		<?php print _('Select CSV file'); ?>: <a class="btn btn-small">Browse</a>
	</div>
	<span class="fname" style="display:none"></span>
	
	<ul class="progressUl">
	<!-- The file uploads will be shown here -->
	</ul>
	
	</form>
	

    <!-- jQuery File Upload Dependencies -->
    <script src="js/uploader/jquery.ui.widget.js"></script>
    <script src="js/uploader/jquery.iframe-transport.js"></script>
    <script src="js/uploader/jquery.fileupload.js"></script>
    
    
    <script type="text/javascript">
	$(function(){
	
	    var ul = $('#csvimport ul');
	    	
	    $('#drop a').click(function(){
	        // Simulate a click on the file input button to show the file browser dialog
	        $(this).parent().find('input').click();
	    });
	
	    // Initialize the jQuery File Upload plugin
	    $('#csvimport').fileupload({
	
	        // This element will accept file drag/drop uploading
	        dropZone: $('#drop'),
	
	        // This function is called when a file is added to the queue;
	        // either via the browse button, or via drag/drop:
	        add: function (e, data) {
	        
	        	//remove all old references
	        	$('ul.progressUl li').remove();
	        	
	        	//add name to hidden class for magic.js
	        	$('.fname').html(data.files[0].name);
	
	            var tpl = $('<li class="alert"><p></p><span></span></li>');
	
	            // Append the file name and file size
	            tpl.find('p').text(data.files[0].name).append(' (<i>' + formatFileSize(data.files[0].size) + '</i>)');
	
	            // Add the HTML to the UL element
	            data.context = tpl.appendTo(ul);
	
	            // Listen for clicks on the cancel icon
	            tpl.find('span').click(function(){
	                if(tpl.hasClass('working')){
	                    jqXHR.abort();
	                }
	                tpl.fadeOut(function(){
	                    tpl.remove();
	                });
	
	            });
	
	            // Automatically upload the file once it is added to the queue
	            var jqXHR = data.submit();
	        },
	
	        fail:function(e, data){
	            // Something has gone wrong!
	            $('ul.progressUl li.alert').addClass('alert-error');
	        },
	        success:function(e, data){
	            // All good, check for response!
				var resp = jQuery.parseJSON(e);
				//get status
				var respStat = resp['status'];
	            //success
	            if(respStat == "success") {	            
	            	$('ul.progressUl li.alert').addClass('alert-success');		//add success class
	            	$('ul.progressUl li.alert p').append('<br><strong>Upload successfull</strong>');	//add ok sign
	            }
	            //error
	            else {
	            	//get error message
					var respErr = resp['error'];	            	
	            	$('ul.progressUl li.alert').addClass('alert-error');		//add error class	
	            	$('li.alert p').append("<br><strong>Error: "+respErr+"</strong>");	            
	            }

	        }
	    });
	
	    // Prevent the default action when a file is dropped on the window
	    $(document).on('drop dragover', function (e) {
	        e.preventDefault();
	    });
	
	    // Helper function that formats the file sizes
	    function formatFileSize(bytes) {
	        if (typeof bytes !== 'number') 	{ return ''; }
	        if (bytes >= 1000000000) 		{  return (bytes / 1000000000).toFixed(2) + ' GB'; }
	        if (bytes >= 1000000) 			{ return (bytes / 1000000).toFixed(2) + ' MB'; }
	        //return result
	        return (bytes / 1000).toFixed(2) + ' KB';
	    }
	
	});
    </script>
    

	<!-- Import file -->
	<h4>2.) <?php print _('Import file'); ?>:</h4>
	<hr>

	<!-- import button -->
	<input type="button" class="btn btn-small" value="<?php print _('Show uploaded subnets'); ?>" id="csvimportcheck">

	<!-- verification holder -->
	<div class="csvimportverify"></div>

</div>

<!-- footer -->
<div class="pFooter">
	<button class="btn btn-small hidePopups"><?php print _('Close window'); ?></button>
	<!-- result -->
	<div class="csvImportResult"></div>
</div>