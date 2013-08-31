<?php

$gTemplArr = array(
	'generate_pdf.pdf_first_page_header' => '
		<table class="first_page_header_container">
			<tr>
				<td>
					<div class="first_page_header_left">
						{idtext}<br>
						doi: {doi} <br>
						www.pensoft.net/journals/bdj/
					</div>
				</td>
				<td>
					<div class="first_page_header_center">
						{document_type_name}
					</div>
				</td>
				<td align="right">
					<div class="first_page_header_right">
						<img class="logo_img" src="/i/BDJLogo.jpg" alt="BDJ"></img>
					</div>
				</td>
			</tr>
		</table>
	',

	'generate_pdf.main' => '
	{*global.pdf_htmlonlyheader}
		<style>
			@page :first {
				@bottom-center {
					content: "© {author_list_short}. This is an open access article distributed under the terms of the Creative Commons Attribution License 3.0 (CC-BY), which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.";
					font-size: 5pt;
					
				}
				@top-left {
					content:none;
				}
				@top-center {
					content:none;
				}
				@top-right {
					content:none;
				}
			}
			
			@page {
				@bottom-center {
					content:none;	
				}
			}
			
			@page :left {
				@top-center {
					content: "{author_list_short} / {idtext}";
					font-size: 6pt;
					font-style: italic;
				}
				@top-left {
					content: counter(page);
					font-size: 6pt;
				}	
			}
			
			@page :right {
				@top-center {
					content: "{_shortTitle(document_title)}";
					font-size: 6pt;
					font-style: italic;
				}
				@top-right {
					content: counter(page);
					font-size: 6pt;
				}	
			}
			
		</style>
		{*generate_pdf.pdf_first_page_header}
		<div id="docbody">
			{content}
		</div>
	{*global.pdf_htmlonlyfooter}
	',
);
?>