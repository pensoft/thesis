<?php

$gTemplArr = array(

	// <a href="{purl}?{}&{pageingurl}p={gotopage}"></a>
	'pageing.prev' => '
					<div class="more">
						<a href="?{_to_xhtml(pageingurl)}p={gotopage}"><img src="/i/leftArrow.png" alt="Left Arrow" /></a>
					</div>
			',
	'pageing.startrs' => '<div class="selectPage noMarginTop">',
	'pageing.startrs_lines' => '<div class="selectPage withLines">',
	'pageing.startrs_nomargin' => '<div class="selectPage noMargin">',

	'pageing.inactivefirst' => '',

	'pageing.activefirst' => '',

	'pageing.inactivepage' => '
					<a class="page active"><span>{lpagenum}</span></a>
						',

	'pageing.activepage' => '
					<a href="?{_to_xhtml(pageingurl)}p={gotopage}" class="page">
						<span>{lpagenum}</span>
					</a>
	',
	// 'pageing.activepage' => '<div class="page"><a href="{purl}?{}&{pageingurl}p={gotopage}">{lpagenum}</a></div>',

	'pageing.inactivelast' => '',

	'pageing.activelast' => '',

	'pageing.delimeter' => '',

	'pageing.next' => '
					<div class="more">
						<a href="?{_to_xhtml(pageingurl)}p={gotopage}"><img src="/i/rightArrow.png" alt="Right Arrow" /></a>
					</div>
			',
			// <a href="{purl}?{}&{pageingurl}p={gotopage}"></a>
	'pageing.endrs' => '<div class="P-Clear"></div></div>',
);

?>

			