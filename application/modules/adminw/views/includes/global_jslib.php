<script type="text/javascript">
		
	function showalert(message) {
		$("#alertModalMsg").html(message);
		$("#alertModal").modal("show");
	}

	function exportData(methodName,exportType = '') {
		var action = '<?=admin_url('exportdata/')?>' + methodName;
		var formValus = $("#_filter_data").serialize();
		//alert(action + '?exportType=' + exportType + '&' + formValus);
		window.location.href = action + '?exportType=' + exportType + '&' + formValus;
	}

	function bulkAction(actionType, fullmethodurl, table) {
	  var bulkid = new Array();
	  $("#_data_rows input[type=checkbox]:checked").each(function(){
	      bulkid.push($(this).val());
	  });
	  if (bulkid.length != 0 && actionType != '') {
	    if(confirm("Are you sure you want to change status ?")){
	      $.ajax({
	        type: "POST",
	        url: fullmethodurl,
	        data: { 
	        	bulkid : bulkid,
	        	type : actionType,
	        	table : table
	        },
	        success: function(response){
	          if(response){
	            location.reload();
	          }
	        }
	      });
	    }
	  }else{
	    showalert("Please Select Atleast one checkbox");
	  }
	}

	function changeUserStatus(actionType, userid, actionTable) {
		switch (actionType) {
		  case 1:
		    action = "Active";break;
		  case 2:
		    action = "InActive";break;
		  case 3:
		    action = "Deleted";break;
		}
	    if(confirm("Are you sure you want to "+ action +" User ?")){
	      $.ajax({
	        type: "POST",
	        url: "<?=admin_url('users/changeStatus')?>",
	        data: { 
	        	actionType : actionType,
	        	userid : userid,
	        	actionTable : actionTable
	        },
	        success: function(response){
	        	if(response == 1){location.reload();}
	        }
	      });
	    }
  	}

  	function changeManagementStat(actionType, userid, actionTable) {
		switch (actionType) {
		  case 1:
		    action = "Active";break;
		  case 2:
		    action = "InActive";break;
		  case 3:
		    action = "Deleted";break;
		}
	    if(confirm("Are you sure you want to "+ action +" Data ?")){
	      $.ajax({
	        type: "POST",
	        url: "<?=admin_url('management/changeStatus')?>",
	        data: { 
	        	actionType : actionType,
	        	userid : userid,
	        	actionTable : actionTable
	        },
	        success: function(response){
	          if(response == 1){location.reload();}
	        }
	      });
	    }
  	}

  	function masterStatus(actionType, id, actionTable) {
		switch (actionType) {
		  case 1:
		    action = "Active";break;
		  case 2:
		    action = "InActive";break;
		  case 3:
		    action = "Deleted";break;
		}
	    if(confirm("Are you sure you want to "+ action +" Data ?")){
	      $.ajax({
	        type: "POST",
	        url: "<?=admin_url('master/changeStatus')?>",
	        data: { 
	        	actionType : actionType,
	        	id : id,
	        	actionTable : actionTable
	        },
	        success: function(response){
	          if(response == 1){location.reload();}
	        }
	      });
	    }
  	}

  	function setState(countryId,selectedId){
		var request = $.ajax({
			type: "POST",
	        url: "<?=admin_url('ajaxcontrol/getStateData')?>",
            data: { 
            	countryId: countryId,
            	selectedId:selectedId 
            },
            success: function (dataHtml){
            	console.log(dataHtml);
            	$("#vState").html(dataHtml);
			}
		});
	}
	
  	function setCity(stateId, selectedId){
		var request = $.ajax({
			type: "POST",
			url: "<?=admin_url('ajaxcontrol/getCityData')?>",
            data: {
            	stateId: stateId, 
            	selectedId: selectedId
            },
            success: function (dataHtml){
            	$("#vCity").html(dataHtml);
            }
		});
    }
    /*======================WALLET FUNCTIONS======================*/
    function addMoneyModel(userId,usertype) {
    	$("#walletUserId").val(userId);
    	$("#walletusertype").html(usertype);
    	$("#addWalletModel").modal('show');
    }

    function processAddMoney() {
    	$("#walletmdlbtn").prop("disabled",true);
    	/*THIS IS FOR ADDING MONEY ONLY TO AGENT USER*/
    	var amount = document.getElementById('addWalletIp').value;
    	var walletUserId = document.getElementById('walletUserId').value;
    	var userType = $("#walletusertype").html();
    	
    	if(amount != '' && amount != 0 && userType != ''){
    		if(amount <= 100000){
	    		var request = $.ajax({
					type: "POST",
					url: "<?=admin_url('ajaxcontrol/addMoneyToWallet')?>",
	            	data: {
	            		amount: amount, 
	            		iUserId: walletUserId,
	            		eUserType: userType,
	            	},
	            	success: function (response){
		            	if(response > 0){
		            		$("#walletmdlbtn").prop("disabled",false);
		            		location.reload();
		            	}
	            	}
	        	});
	    	}else{
	    		showalert("Sorry.! but You can't add more than 1 Lac");
	       	}
    	}else{
    		$("#addWalletIp").focus();
    		$("#walletmdlbtn").prop("disabled",false);
    		showalert("Please Enter Valid Amount");
    	}
    }

    //wallet report function
   	function getUserSelect(usertype,selected = '') {
   		if(usertype != ''){
	   		$("#hashdiv").show();
	   		
	   		var request = $.ajax({
						type: "POST",
						url: "<?=admin_url('ajaxcontrol/getUserList')?>",
		            	//contentType: "application/json",
		            	dataType: "json",
		            	data: {usertype: usertype,selected:selected},
		            	success: function (response){
		            		//alert(response.data);
			            	$("#hash").html(response.data);
		            	}
		        	});
	   	}
   	}

   	function removeTourImage(tourImgId){
   		if(confirm("Are you sure you want to delete this ?")){
   			$.ajax({
				type: "POST",
				url: "<?=admin_url('management/delete_tours_images')?>",
				data: {tourImgId: tourImgId},
				success: function (response){
			    	if(response){
			    		location.reload();
			    	}
				}
			});
   		}
   	}
   	function makePrimaryImage(imageId){
   		if(confirm("Are you sure you want Make This Primary ?")){
   			$.ajax({
				type: "POST",
				url: "<?=admin_url('management/make_primaryImage')?>",
				data: {imageId: imageId},
				success: function (response){
			    	if(response){
			    		location.reload();
			    	}
				}
			});
   		}
   	}

   	/*=========================================TimingFunction=========================================*/
   	function setToday(dp1, dp2) {

   		var today = '<?=date('d-m-Y')?>';
   		$("#dp1").val(today);
   		$("#dp2").val(today);
   	}
   	function setYesterDay(dp1, dp2) {

   		var date = '<?=date('d-m-Y',strtotime("-1 day"))?>';
   		$("#dp1").val(date);
   		$("#dp2").val(date);
   	}
   	function setCurrentWeek(dp1, dp2) {
   		<?php 
   			$monday = date('d-m-Y', strtotime('monday this week'));
   			$sunday = date('d-m-Y', strtotime('sunday this week'));
   		?>
   		$("#dp1").val('<?=$monday?>');
   		$("#dp2").val('<?=$sunday?>');
   	}
   	function setCurrentMonth(dp1, dp2, prev = 'No') {
   		<?php $date1 = date('01-m-Y');$date2 = date('t-m-Y'); ?>
   		$("#dp1").val('<?=$date1?>');
   		$("#dp2").val('<?=$date2?>');
   	}
   	function setPrevMonth(dp1, dp2) {
   		<?php 
   			$date1 = date('01-m-Y',strtotime("-1 month"));
   			$date2 = date('t-m-Y',strtotime("-1 month"));
   		?>
   		$("#dp1").val('<?=$date1?>');
   		$("#dp2").val('<?=$date2?>');
   	}
   	function setCurrentYear(dp1, dp2) {
   		<?php 
   			$date1 = date('01-01-Y');
   			$date2 = date('31-12-Y');
   		?>
   		$("#dp1").val('<?=$date1?>');
   		$("#dp2").val('<?=$date2?>');
   	}
   	function setPrevYear(dp1, dp2) {
   		<?php 
   			$date1 = date('01-01-Y',strtotime("-1 year"));
   			$date2 = date('31-12-Y',strtotime("-1 year"));
   		?>
   		$("#dp1").val('<?=$date1?>');
   		$("#dp2").val('<?=$date2?>');
   	}

   	$(document).ready(function(){

   		$('#walletmdlbtn').one('click', function(e) {
   		//$(document).on('click', '#walletmdlbtn:not(.clicked)', function() {
   			processAddMoney();
   		});

   	});
</script>