<?php

$gTemplArr = array(
	// PJS Main Menu
	'menu.main-head' => '
			<div id="menu">
				<a class="menu_btn" style="background: none;" href="/">
					<img style="margin-top: -3px;" src="i/homeIcon.png" alt="home" />
				</a>
	',
	
	'menu.main-foot' => '
			</div>
	',
	
	'menu.main-row0' => '<a class="menu_btn" href="{href}">{name}</a>',
	
	// PJS Journal Menu
	
	'menu.journal-head' => '
							<div id="subMenuWrapper">
								<!--<img src="i/subMenuRight.png" alt="RIghe Line" />-->
								<div id="subMenu">
	',
	
	'menu.journal-foot' => '
								</div>
								<img src="i/subMenuLeft.png" alt="Left Line" />
							</div>
	',
	
	'menu.journal-row0' => '<a class="menu_btn" href="{href}">{name}</a>',
);

?>