<?php

$gTemplArr = array(
	'pageing.pgstart' => '
		<li class="PrevNext"><a title="{_getstr(pageing.topage)} {lpagegroup}" class="noleftpadding deselected" href="?{_to_xhtml(pageingurl)}p={gotopage}&amp;lang='.getlang(true).'">« </a></li>
		<li ><a title="{_getstr(pageing.gotopage)} {maxpages}" class="deselected" href="?{_to_xhtml(pageingurl)}p=0&amp;lang='.getlang(true).'">1</a><span class="dots">...</span></li>',
	
	'pageing.pgend' => '
		<li><span class="dots">...</span><a title="{_getstr(pageing.gotopage)} {maxpages}"  class="deselected" href="?{_to_xhtml(pageingurl)}p=-1&amp;lang='.getlang(true).'">{maxpages}</a></li> 
		<li class="PrevNext"><a title="{_getstr(pageing.topage)} {lpagegroup}"  class="deselected" href="?{_to_xhtml(pageingurl)}p={gotopage}&amp;lang='.getlang(true).'">»</a></li>',
	
/**/	'pageing.inactivepage' => '
			<li><a href="{href}" class="selected">{lpagenum}</a></li>
		',
	
/**/	'pageing.activepage' => '
			<li><a title="{_getstr(pageing.gotopage)} {lpagenum}" class="deselected" href="?{_to_xhtml(pageingurl)}p={gotopage}&amp;lang='.getlang(true).'">{lpagenum}</a></li>
		',
	
	'pageing.activefirst' => '<li class="FirstLast"><a href="{href}">««</a></li>',
	'pageing.inactivefirst' => '<li class="FirstLast">««</li>',
	
	'pageing.activelast' => '<li class="FirstLast"><a href="{href}">»»</a></li>',
	'pageing.inactivelast' => '<li class="FirstLast">»»</li>',
	
	'pageing.delimeter' => '',
	
	'pageing.prevpage' => '<li class="PrevNext"><a title="{_getstr(pageing.topage)} {gotopage}" href="?{_to_xhtml(pageingurl)}p={gotopage}&amp;lang='.getlang(true).'">«</a></li>',
	
	'pageing.first' => '<a title="{_getstr(pageing.gotopage)} 1" href="?{_to_xhtml(pageingurl)}p=0&amp;lang='.getlang(true).'">1</a>',
	
	'pageing.dots' => '<span class="dots">...</span>',
	
	'pageing.last' => '<a title="{_getstr(pageing.gotopage)} {maxpages}" href="?{_to_xhtml(pageingurl)}p=-1&amp;lang='.getlang(true).'">{maxpages}</a> ',
	
	'pageing.nextpage' => '<a title="{_getstr(pageing.topage)} {gotopage}" href="?{_to_xhtml(pageingurl)}p={gotopage}&amp;lang='.getlang(true).'">{_getstr(pageing.next)}</a>',
);

/*
	'pageing.pgstart' => '<a title="{_getstr(pageing.topage)} {lpagegroup}" class="noleftpadding" href="?{_to_xhtml(pageingurl)}p={gotopage}">{_getstr(pageing.previous)}</a><a title="{_getstr(pageing.gotopage)} {maxpages}" href="?{_to_xhtml(pageingurl)}p=0">1</a><span class="dots">...</span>',
	
	'pageing.pgend' => '<span class="dots">...</span><a title="{_getstr(pageing.gotopage)} {maxpages}" href="?{_to_xhtml(pageingurl)}p=-1">{maxpages}</a> <a title="{_getstr(pageing.topage)} {lpagegroup}" href="?{_to_xhtml(pageingurl)}p={gotopage}">{_getstr(pageing.next)}</a>',
	
	'pageing.inactivepage' => '<span class="activepage">{lpagenum}</span>',
	
	'pageing.activepage' => '<a title="{_getstr(pageing.gotopage)} {lpagenum}" href="?{_to_xhtml(pageingurl)}p={gotopage}">{lpagenum}</a>',
	
	'pageing.activefirst' => 'bla',
	'pageing.inactivefirst' => 'bla',
	
	'pageing.activelast' => 'asdfa',
	'pageing.inactivelast' => 'asdf',
	
	'pageing.delimeter' => 'asdf',
	
	'pageing.prevpage' => '<a title="{_getstr(pageing.topage)} {gotopage}" class="noleftpadding" href="?{_to_xhtml(pageingurl)}p={gotopage}">{_getstr(pageing.previous)}</a>',
	
	'pageing.first' => '<a title="{_getstr(pageing.gotopage)} 1" href="?{_to_xhtml(pageingurl)}p=0">1</a>',
	
	'pageing.dots' => '<span class="dots">...</span>',
	
	'pageing.last' => '<a title="{_getstr(pageing.gotopage)} {maxpages}" href="?{_to_xhtml(pageingurl)}p=-1">{maxpages}</a> ',
	
	'pageing.nextpage' => '<a title="{_getstr(pageing.topage)} {gotopage}" href="?{_to_xhtml(pageingurl)}p={gotopage}">{_getstr(pageing.next)}</a>',
*/

?>