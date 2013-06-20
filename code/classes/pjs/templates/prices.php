<?php

$gTemplArr = array(
	'prices.head' => '
		<h1 class="dashboard-title">' . getstr('pjs.manage_journal_prices') . '</h1>
	',
	'prices.list_start' => '
		<form name="prices_form" action="" method="post" enctype="multipart/form-data">
			<table class="dashboard">
				<tr>
					<th>' . getstr('pjs.frompage') . '</th>
					<th>' . getstr('pjs.topage') . '</th>
					<th>' . getstr('pjs.price') . '</th>
				</tr>
	',
	'prices.list_row' => '
				<tr>
					<td>
						<div class="P-Input-Full-Width P-W300 fieldHolder">
							<input type="text" name="range_start[]" value="{range_start}"></input>
						</div>
					</td>
					<td>
						<div class="P-Input-Full-Width P-W300 fieldHolder">
							<input type="text" name="range_end[]" value="{range_end}"></input>
						</div>
					</td>
					<td>
						<div class="P-Input-Full-Width P-W300 fieldHolder">
							<input type="text" name="price[]" value="{price}"></input>
						</div>
					</td>
				</tr>
	',
	'prices.list_end' => '
				<tr>
					<td>
						<div class="P-Input-Full-Width P-W300 fieldHolder">
							<input type="text" name="range_start[]" value=""></input>
						</div>
					</td>
					<td>
						<div class="P-Input-Full-Width P-W300 fieldHolder">
							<input type="text" name="range_end[]" value=""></input>
						</div>
					</td>
					<td>
						<div class="P-Input-Full-Width P-W300 fieldHolder">
							<input type="text" name="price[]" value=""></input>
						</div>
					</td>
				</tr>
			</table>
			<div class="leftPad10">
				<div class="P-Grey-Btn-Holder P-Reg-Btn">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><input type="submit" name="tAction" value="save"></input></div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
			</div>
		</form>
		<div class="clear"></div>
	',
	'prices.foot' => '',
);
?>