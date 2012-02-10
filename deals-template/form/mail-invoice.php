<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<title>Invoice</title>
		
	</head>

	<body>
		
		<?php global $checkVerify, $invoice_options, $invoice_data; ?>
		
		<?php 
			// format buy_date
			$buy_date_raw = date_create($checkVerify->buy_date);
			$buy_date = date_format($buy_date_raw, 'M d, y');
			
		?>

		<div id="page-wrap" style="width: 80%;max-width: 700px;margin: 2% 10%;font-family: arial, sans-serif;font-size: 13px;">

			<div id="header" style="font-weight: bold;font-size: 2em;">INVOICE</div>

			<div id="identity" style="display: block;overflow: hidden;margin: 4em 0;vertical-align: middle;">

				<div id="address" style="float: left;width: 30%"><?php echo $invoice_options['info']; ?></div>

				<div style="float: right" id="logo">
					<img id="image" src="<?php echo $invoice_options['logo_url']; ?>" alt="<?php echo $invoice_options['store_name']; ?>" />
				</div>

			</div>

			
			<div style="clear:both"></div>

			
			<div id="customer">

				<div id="customer-title" style="font-size: 1.5em;margin-bottom: 1em;border-bottom: 2px dotted #adadad;padding-bottom: .5em;"><?php echo $invoice_options['store_name']; ?></div>

				<table id="meta" style="width: 100%;text-align: right;border-collapse: collapse;">
					<tbody>
						<tr style="float-left;display: inline-table;width: 33%;padding: .5em 0;background:#ccc">
							<td class="meta-head" style="float:left;text-align:center;width:100%">Invoice</td>
							<td style="float:left;text-align:center;width:100%;font-size:1.1em;font-weight:bold">#<?php echo $checkVerify->transaction_id; ?></td>
						</tr>
						<tr style="float-left;display: inline-table;width: 33%;padding: .5em 0;background: #adadad;color: #fff;">
							<td class="meta-head" style="float:left;text-align:center;width:100%">Date</td>
							<td style="float:left;text-align:center;width:100%;font-size:1.3em;font-weight:bold"><div class="date"><?php echo $buy_date; ?></div></td>
						</tr>
						<tr style="float-left;display: inline-table;width: 33%;padding: .5em 0;background:#ccc">
							<td class="meta-head" style="float:left;text-align:center;width:100%">Amount Due</td>
							<td style="float:left;text-align:center;width:100%;font-size:1.1em;font-weight:bold"><div class="due">$<?php echo $checkVerify->total_price; ?></div></td>
						</tr>
					</tbody>
				</table>

			</div>

			<table id="items" style="width: 100%; margin-top: 4em; border-spacing: 0">
				<thead>
					<tr style="color: #adadad; text-transform: uppercase;">
						<th style="text-align: left; width: 36%; padding: 2%;border-bottom:2px solid #adadad">Item Name</th>
                                                <th style="text-align: left; width: 36%; padding: 2%;border-bottom:2px solid #adadad">User Name</th>
						<th style="text-align: left; width: 16%; padding: 2%;border-bottom:2px solid #adadad">Price</th>
						<th style="text-align: left; width: 16%; padding: 2%;border-bottom:2px solid #adadad">Link</th>
					</tr>					
				</thead>
				
				<tbody style="vertical-align: top;">
					<tr class="item-row">
						<td class="title" style="padding: 3% 2%; font-weight: bold;border-bottom: 1px solid #adadad;"><?php echo $invoice_data['title']; ?></td>
                                                <td class="title" style="padding: 3% 2%; font-weight: bold;border-bottom: 1px solid #adadad;"><?php echo $invoice_data['user_name']; ?></td>
						<td class="price" style="padding: 3% 2%;border-bottom: 1px solid #adadad;">$<?php echo $checkVerify->total_price; ?></td>
						<td class="link" style="padding: 3% 2%;border-bottom: 1px solid #adadad;"><a href="<?php echo $invoice_data['link']; ?>" title="You will be redirected to your Transaction History" style="color: #21759b; text-decoration: none">Click Here</a></td>
					</tr>
				</tbody>
				
				<tfoot>
					<tr>
						<td class="blank"> </td>
						<td class="total-line">Total</td>
						<td class="total-value" style="font-weight:bold"><div id="total">$<?php echo $checkVerify->total_price; ?></div></td>
					</tr>
					<tr>
						<td class="blank"> </td>
						<td class="total-line">Amount Paid</td>
						<td class="total-value" style="font-weight:bold"><div id="paid">$<?php echo $checkVerify->total_price; ?></div></td>
					</tr>
					<tr>
						<td class="blank"> </td>
						<td class="total-line balance">Balance Due</td>
						<td class="total-value balance" style="font-weight:bold"><div class="due">$0.00</div></td>
					</tr>					
				</tfoot>
				
			</table>

			<div id="footer" style="position: relative;margin: 2em auto 0;text-align: right;">
				<?php echo $invoice_options['footer']; ?>
				<img src="<?php echo $invoice_options['barcode']; ?>" />
			</div>

		</div>


	</body>
</html>