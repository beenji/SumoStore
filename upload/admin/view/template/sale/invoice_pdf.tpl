<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15">
		<style>
		body { 
			font-family: Helvetica, Arial;
			font-size: 13px;
		}

		#content {
			margin: 0 20px 30px 20px;
		}

		#header {
			margin: 0 20px;
		}

		#footer {
			margin: 0;
			text-align: center;
			position: absolute;
			bottom: 0;
			width: 100%;
		}

		h1 {
			margin: 0;
		}
		
		table {
			margin-bottom: 20px;
		}

		table.lines {
			border-collapse: collapse;
			width: 100%;
		}

		table.lines thead th {
			border-bottom: 2px solid #ddd;
			text-align: left;
			padding: 7px;
		}

		table.lines tbody td {
			border-bottom: 1px solid #ddd;
			padding: 7px;
		}

		table.lines tbody tr:nth-child(2n) {
			background-color: #f8f8f8;
		}

		.center {
			text-align: center !important;
		}

		.right {
			text-align: right !important;
		}

		.left {
			text-align: left !important;
		}

		table.summary {
			border-collapse: collapse;
			width: 100%;
		}

		table.summary tbody th {
			text-align: right;
			padding: 7px;
		}

		table.summary tbody td {
			padding: 7px;
		}

		table.info {
			width: 100%;
			margin-bottom: 10px;
		}

		table.info table td, table.info table th {
			padding: 3px 7px;
			text-align: left;
		}

		</style>
	</head>
	<body>
		<div id="header">
			<table class="info">
				<tr>
					<td>
						<?php if ($logo) { ?>
						<img src="<?php echo $logo; ?>" alt="<?php echo $store; ?>" />
						<?php } ?>
					</td>
					<td style="width: 210px;">
						<table>
							<tr>
								<td><strong><?php echo $store; ?></strong></td>
							</tr>
							<tr>
								<td><?php echo $store_address; ?></td>
							</tr>
						</table>

						<table>
							<tr>
								<th style="width: 100px;">E-mailadres:</th>
								<td><?php echo $store_email; ?></td>
							</tr>
							<tr>
								<th>Website:</th>
								<td><?php echo $store_website; ?></td>
							</tr>
						</table>

						<table>
							<tr>
								<th style="width: 100px;">KVK-nr.:</th>
								<td><?php echo $store_company_number; ?></td>
							</tr>
							<tr>
								<th>BTW-nr.:</th>
								<td><?php echo $store_tax_number; ?></td>
							</tr>
							<tr>
								<th>IBAN:</th>
								<td><?php echo $store_iban; ?></td>
							</tr>
							<tr>
								<th>BIC:</th>
								<td><?php echo $store_bic; ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
		<div id="content">
			<h1>Factuur</h1>
			<table class="info">
				<tbody>
					<tr>
						<td valign="top">
							<table>
								<tr>
									<td style="padding-left: 0;"><?php echo $customer_name; ?></td>
								</tr>
								<tr>
									<td style="padding-left: 0;"><?php echo $customer_address; ?></td>
								</tr>
								<tr>
									<td style="padding-left: 0;"><?php echo $customer_postcode . ' ' . $customer_city; ?></td>
								</tr>
								<tr>
									<td style="padding-left: 0;"><?php echo $customer_country; ?></td>
								</tr>
							</table>
						</td>
						<td style="width: 208px;">
							<table>
								<tr>
									<th>Debiteurnr.:</th>
									<td><?php echo $customer_no; ?></td>
								</tr>
								<tr>
									<th>Factuurnr.:</th>
									<td><?php echo $invoice_no; ?></td>
								</tr>
								<tr>
									<th>Datum:</th>
									<td><?php echo $invoice_date; ?></td>
								</tr>
								<tr>
									<th>Referentienr.:</th>
									<td><?php echo $reference; ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</tbody>
			</table>

			<table class="lines">
				<thead>
					<tr>
						<th style="width: 55px;" class="center">Aantal</th>
						<th style="width: 75px;">Productnr.</th>
						<th>Omschrijving</th>
						<th style="width: 55px;" class="right">BTW</th>
						<th style="width: 130px;" class="right">Prijs incl. BTW</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($amount as $k => $am) { ?>
					<tr>
						<td class="center"><?php echo $quantity[$k]; ?></td>
						<td><?php echo $product[$k]; ?></td>
						<td><?php echo $description[$k]; ?></td>
						<td class="right"><?php echo $tax_percentage[$k]; ?>%</td>
						<td style="width: 130px;" class="right"><?php echo $am; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>

			<table class="summary">
				<tbody>
					<?php foreach ($totals as $t) { ?>
					<tr>
						<th class="right"><?php echo $t['label']; ?>:</th>
						<td style="width: 60px;" class="right"><?php echo $t['value_hr']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>

		<div id="footer">
			<?php echo $invoice_footer; ?>
		</div>
	</body>
</html>