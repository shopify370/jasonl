<!DOCTYPE html>
<html>
	<head>
	  <meta charset="utf-8" />
	  <title>Error integrating with Pronto</title>
	  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>
		<div>
			<p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px">Dear Admin,</p>
			<p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px">
			We have encountered an error parsing SKUs for the order with ID <?php echo $order_id; ?>. For more details please follow the link  <a href="<?php echo base_url('/orders/orderdetail/').'/'.$insert_id; ?>" target="_blank"><?php echo base_url('/orders/orderdetail/').'/'.$insert_id; ?></a> in pronto.</p>
			<p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px">We request you to recheck the order details and manually push the order to Pronto.
			</p>
			<p style="Margin-top: 0;color: #565656;font-family: Georgia,serif;font-size: 16px;line-height: 25px;Margin-bottom: 25px">Thank you!</p>
		</div>
	</body>
</html>