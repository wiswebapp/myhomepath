<?php

$PAYMENT_ENABLE_SANDBOX = $data['getwayData']['PAYMENT_ENABLE_SANDBOX'];
$PAYUMONEY_MERCHANT_KEY = $data['getwayData']['PAYUMONEY_MERCHANT_KEY'];
$PAYUMONEY_MERCHANT_MID = $data['getwayData']['PAYUMONEY_MERCHANT_MID'];
$orderData = $data['orderData']['OrderData'];
$orderUser = $data['orderData']['OrderUser'];
$transId = "Trax-".time();
$uuSerID = $orderUser['iUserId'];
$hashKey = generateHash($PAYUMONEY_MERCHANT_MID,$transId,$orderData['iFare'],$orderData['vOrderId'],$orderUser['vName'],$orderUser['vEmail'],$udf5 = 'BOLT_KIT_PHP7',$PAYUMONEY_MERCHANT_KEY);

function getCallbackUrl($userId)
{
	return base_url('payment/paymentResponse?iUserId='.$userId);
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Make Payment</title>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>

<!-- this meta viewport is required for BOLT //-->
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" >
<!-- BOLT Sandbox/test //-->
<script id="bolt" src="https://sboxcheckout-static.citruspay.com/bolt/run/bolt.min.js" bolt-
color="e34524" bolt-logo="http://boltiswatching.com/wp-content/uploads/2015/09/Bolt-Logo-e14421724859591.png"></script>
<!-- BOLT Production/Live //-->
<!--// script id="bolt" src="https://checkout-static.citruspay.com/bolt/run/bolt.min.js" bolt-color="e34524" bolt-logo="http://boltiswatching.com/wp-content/uploads/2015/09/Bolt-Logo-e14421724859591.png"></script //-->

</head>
<style type="text/css">
	.main {
		margin-left:30px;
		font-family:Verdana, Geneva, sans-serif, serif;
	}
	.text {
		float:left;
		width:180px;
	}
	.dv {
		margin-bottom:5px;
	}
</style>
<body>
<div class="main">

    <!-- <div>
    	<h3>Make Payment</h3>
    </div> -->
	<form action="#" id="payment_form">

    <input type="hidden" id="udf5" name="udf5" value="BOLT_KIT_PHP7" />
    <input type="hidden" id="surl" name="surl" value="<?php echo getCallbackUrl($uuSerID); ?>" />
    <?php $ipType="hidden"; ?>
    <div class="dv">
    <!-- <span class="text"><label>Merchant Key:</label></span> -->
    <span><input type="<?=$ipType?>" id="key" name="key" placeholder="Merchant Key" value="<?=$PAYUMONEY_MERCHANT_MID?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Merchant Salt:</label></span> -->
    <span><input type="<?=$ipType?>" id="salt" name="salt" placeholder="Merchant Salt" value="<?=$PAYUMONEY_MERCHANT_KEY?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Transaction/Order ID:</label></span> -->
    <span><input type="<?=$ipType?>" id="txnid" name="txnid" placeholder="Transaction ID" value="<?=$transId?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Amount:</label></span> -->
    <span><input type="<?=$ipType?>" id="amount" name="amount" placeholder="Amount" value="<?=$orderData['iFare']?>" /></span>    
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Product Info:</label></span> -->
    <span><input type="<?=$ipType?>" id="pinfo" name="pinfo" placeholder="Product Info" value="<?=$orderData['vOrderId']?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>First Name:</label></span> -->
    <span><input type="<?=$ipType?>" id="fname" name="fname" placeholder="First Name" value="<?=$orderUser['vName']?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Email ID:</label></span> -->
    <span><input type="<?=$ipType?>" id="email" name="email" placeholder="Email ID" value="<?=$orderUser['vEmail']?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Mobile/Cell Number:</label></span> -->
    <span><input type="<?=$ipType?>" id="mobile" name="mobile" placeholder="Mobile/Cell Number" value="<?=$orderUser['vPhone']?>" /></span>
    </div>
    
    <div class="dv">
    <!-- <span class="text"><label>Hash:</label></span> -->
    <span><input type="<?=$ipType?>" id="hash" name="hash" placeholder="Hash" value="<?=$hashKey?>" /></span>
    </div>
    
    <div style="display: none;">
    	<input type="submit" class="button2" value="Pay" onclick="launchBOLT(); return false;" />
    </div>
	</form>
</div>

<script type="text/javascript"><!--
function launchBOLT()
{
	bolt.launch({
	key: $('#key').val(),
	txnid: $('#txnid').val(), 
	hash: $('#hash').val(),
	amount: $('#amount').val(),
	firstname: $('#fname').val(),
	email: $('#email').val(),
	phone: $('#mobile').val(),
	productinfo: $('#pinfo').val(),
	udf5: $('#udf5').val(),
	surl : $('#surl').val(),
	furl: $('#surl').val(),
	mode: 'dropout'	
},{ responseHandler: function(BOLT){
	console.log( BOLT.response.txnStatus );		
	if(BOLT.response.txnStatus != 'CANCEL')
	{
		//Salt is passd here for demo purpose only. For practical use keep salt at server side only.
		var fr = '<form action=\"'+$('#surl').val()+'\" method=\"post\">' +
		'<input type=\"hidden\" name=\"key\" value=\"'+BOLT.response.key+'\" />' +
		'<input type=\"hidden\" name=\"salt\" value=\"'+$('#salt').val()+'\" />' +
		'<input type=\"hidden\" name=\"txnid\" value=\"'+BOLT.response.txnid+'\" />' +
		'<input type=\"hidden\" name=\"amount\" value=\"'+BOLT.response.amount+'\" />' +
		'<input type=\"hidden\" name=\"productinfo\" value=\"'+BOLT.response.productinfo+'\" />' +
		'<input type=\"hidden\" name=\"firstname\" value=\"'+BOLT.response.firstname+'\" />' +
		'<input type=\"hidden\" name=\"email\" value=\"'+BOLT.response.email+'\" />' +
		'<input type=\"hidden\" name=\"udf5\" value=\"'+BOLT.response.udf5+'\" />' +
		'<input type=\"hidden\" name=\"mihpayid\" value=\"'+BOLT.response.mihpayid+'\" />' +
		'<input type=\"hidden\" name=\"status\" value=\"'+BOLT.response.status+'\" />' +
		'<input type=\"hidden\" name=\"hash\" value=\"'+BOLT.response.hash+'\" />' +
		'</form>';
		var form = jQuery(fr);
		jQuery('body').append(form);								
		form.submit();
	}
},
	catchException: function(BOLT){
 		alert( BOLT.message );
	}
});
}

document.getElementsByClassName("button2")[0].click();;

</script>	

</body>
</html>
	
