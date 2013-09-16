<?php

$gTemplArr = array(
	'generate_pdf.pdf_first_page_header' => '
	<div class="first_page_header_left">
		<img style="float: left" src="http://biodiversitydatajournal.com/i/bdj-eye.png" />
		<div style="float: left; 	padding: 2px 4px;">
		{idtext}<br>
		doi: <a href="http://dx.doi.org/{doi}" style="text-decoration: none">{doi}</a>
		</div>

		<div style="float: right"><img src="/i/open_access_pdf.svg" style="width: 49.67px !important; height: 37px !important" /></div>
		<div style="clear: both"></div>

	</div>
	<div class="PaperType">
		{document_type_name}
	</div>
	',

	'generate_pdf.main' => '
	{*global.pdf_htmlonlyheader}
	<link href="/lib/pdf.css" rel="stylesheet" media="screen" type="text/css" />

		<style>.first_page_header_left {
			margin-bottom: 10px;
			/*border-bottom: 1px solid black;*/
		}
			@page :first {
				@bottom-center {
					content: "© {author_list_short}. This is an open access article distributed under the terms of the Creative Commons Attribution License 3.0 (CC-BY), which permits unrestricted use, distribution, and reproduction in any medium, provided the original author and source are credited.";
					font-size: 6.3pt;
					font-family: GillSans;
					text-align: left;


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
					content: "{author_list_short}";
					font-size: 8pt;
					color: #0a542c;
					font-family: "Helvetica";
				}
				@top-left {
					content: counter(page);
					font-size: 8pt;
					font-family: "Helvetica";
				}
			}

			@page :right {
				@top-center {
					content: "{_shortTitle(document_title)}";
					font-size: 8pt;
					color: #0a542c;
					font-family: "Helvetica";
				}
				@top-right {
					content: counter(page);
					font-size: 8pt;
					font-family: "Helvetica";
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