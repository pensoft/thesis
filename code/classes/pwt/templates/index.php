<?php

$gTemplArr = array(
	'index.htmlstartcontent' => '
			{*global.htmlonlyheader}
				<div class="P-Wrapper P-Without-Bread-Crumbs">
					{*global.header}
					<div class="P-Wrapper-Container">
	',
/*
<div class="sidebar">
                        <div class="form_wrap">
                            <div class="center_wrap">
                                <a href="#" class="button_green">Start a manuscript</a>
                            </div>
                            <div class="article_wrap">
                                <h2>My recent manuscripts</h2>
                                <ul>
                                    <li>
                                        <a href="#">Redescription of the rare amphipod crustacean Pseudaeginella montoucheti (Quitete, 1971) from Brazil</a>
                                    </li>
                                    <li>
                                        <a href="#">Redescription of the rare amphipod crustacean Pseudaeginella montoucheti (Quitete, 1971) from Brazil</a>
                                    </li>
                                </ul>
                                <a href="#">See more</a>
                            </div>
                        </div>
                    </div>*/

	'index.content_head' => '
		<div class="sidebar" style="position: relative;">
			<div class="form_wrap">
	            <div class="center_wrap">
	                <a href="/create_document.php" class="button_green">Start a manuscript</a>
	            </div>
	            <div class="article_wrap">
	                <h2>{_getstr(pwt.index_list_head_text)}</h2>
	                
	',
	
	'index.content_startrs' => '<ul>',
	'index.content_endrs' => '</ul><a href="/dashboard.php">See more</a>',
	
	'index.content_row' => '
						<li>
							<a href="/preview.php?document_id={document_id}">
								{_TrimAndCutText(name)}
							</a>
						</li>
	',
	
	'index.content_foot' => '
                </div>
            </div>
        </div>
	',
	
	'index.no_manuscripts' => '
		<p>
			Use the button above to create your first manuscript
		</p>
	',
);
?>