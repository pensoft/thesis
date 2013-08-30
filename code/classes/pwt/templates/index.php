<?php

$gTemplArr = array(
	'index.htmlstartcontent' => '
			{*global.htmlonlyheader}
				<div class="P-Wrapper P-Without-Bread-Crumbs">
					{*global.header}
					<div class="P-Wrapper-Container">
	',

	'index.content_head' => '
		<div class="index_list_start">
			{_getstr(pwt.index_list_head_text)}
		</div>
	',
	
	'index.content_row' => '
						<div class="P-Content-Dashboard-Row">
							<div class="P-Content-Dashboard-Row-Left">
								<div class="P-Content-Dashboard-Row-Title">
									<a href="/preview.php?document_id={document_id}" target="_blank">{_strim(name)}</a>
									<div class="P-Clear"></div>
								</div>
								<div class="P-Clear"></div>
							</div>
							<div class="P-Clear"></div>
						</div>
						<div class="P-Clear"></div>
	',
	
	'index.content_foot' => '
		<div class="index_foot_sect">
			<a href="/dashboard.php">See more</a>
		</div>
	',
	
	'index.no_manuscripts' => '
		<div class="index_list_start">
			{_getstr(pwt.index_list_head_text)}
		</div>
		<div class="index_no_data_in_list">
			Use the button above to create your first manuscript
		</div>
	',
);
?>