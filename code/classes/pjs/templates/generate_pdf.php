<?php

$gTemplArr = array(
	'generate_pdf.pdf_first_page_header' => '
		<table class="first_page_header_container">
			<colgroup>
				<col width="25%"></col>
				<col width="50%"></col>
				<col width="25%"></col>
			<colgroup>
			<tr>
				<td>
					<div class="first_page_header_left">
						BDJ / text <br>
						doi: text <br>
						url
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
					content: "{_GenerateAuthorListForPDF(author_list)} + neshto si";	
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
					content: "{_GenerateAuthorListForPDF(author_list)} / BDJ alabala";
				}
				@top-left {
					content: counter(page);
				}	
			}
			
			@page :right {
				@top-center {
					content: "{_stripHTML(document_title)}";
				}
				@top-right {
					content: counter(page);
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