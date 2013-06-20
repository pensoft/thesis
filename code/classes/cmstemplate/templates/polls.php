<?php

$gTemplArr = array(

	'polls.browseallhead' => '
		<div class="blacktitle">
			<span class="navlinkholder homelink"><a class="navlink" href="/index.php">' . getstr('global.home') . '</a></span>
			<span class="navlinkholder"><a class="navlink" href="/polls.php">' . getstr('global.polls') . '</a></span>
		</div>
		<div class="newsbrowse">
			<div class="content">	
	',

	'polls.browseallfoot' => '
			</div>
			<div class="bottom"></div>
		</div>
		<div class="navigation noborder">
			<div class="pageing">{nav}</div>
			<div class="unfloat"></div>
		</div>
	',
	
	'polls.browsenodata' => '
		<div class="comments_row">
			' . getstr('polls.nopolls') . '
		</div>
	',

	'polls.browseallrow' => '
		<div class="news_rowbrowse">
			<div class="linkholder"><a href="/polls.php?id={pollid}">{polltxt}</a></div>
		</div>
	',
	
	'polls.browseallstart' => '',
	
	'polls.browseallsplithead' => '',
	
	'polls.browseallend' => '',


	'polls.shownodata' => '
		' . getstr('polls.nopoll') . '
	',
	
	'polls.leftcol_head' => '
		<div class="poll">
			<div class="greytitle">
				<div class="left"></div>
				<div class="center">
					' . getstr('global.poll') . '
				</div>
				<div class="right"></div>
				
			</div>
			<div class="content">
				<div class="unfloat"></div>
				<div id="anketaleft{posid}" class="pollcontent">
	',
	
	'polls.leftcol_startrs' => '
				
					{question}
					<form name="anketa" method="{formmethod}" action="' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '" onsubmit="return pollsubmit(this,2,\'anketaleft{posid}\');">
						<input type="hidden" name="formname" value="anketa" />
						<input type="hidden" name="posid" value="{posid}" />
						<input type="hidden" name="pollid" value="{pollid}" />
						<input type="hidden" name="pollpage" value="{pollpage}" />
						<table border="0" cellspacing="2" cellpadding="2" class="polltable">		
	',
	
	'polls.leftcol_endrs' => '
						
						
						</table>
						<div class="unfloat"></div><br/>
						
						<button class="pollbutton" type="submit" onclick="return poll_btnclick(this);" name="votenow" value="Vote" >
							<div class="tabmore " >
								<div class="left"></div>
								<div class="center">' . getstr('polls.vote') . '</div>
								<div class="right"></div>
							</div>		
						</button>
						<button class="pollbutton pollresultbutton" type="submit" name="viewtype" value="View results" onclick="return poll_btnclick(this);" >
							<div class="tabmore">
								<div class="left"></div>
								<div class="center"><a title="">' . getstr('polls.statistics') . '</a></div>
								<div class="right"></div>
							</div>
						</button>
						<div class="unfloat"></div>
					</form>
	',
	
	'polls.leftcol_endrsnobut' => '
						
						
						</table>
						<div class="unfloat"></div><br/>

						{display_back}
					</form>
	',
	
	'polls.leftcol_foot' => '
				</div>
				<div class="pollcontent"><a href="/polls.php">'.getstr('polls.archive').'</a></div>
			</div>
			<div class="unfloat"></div>
			<div class="bottom">
				<div class="left"></div>
				<div class="center"></div>
				<div class="right"></div>
				<div class="unfloat"></div>
			</div>
		</div>
	',
	
	
	'polls.startrs' => '			
						<div class="story">
							<div class="blacktitle">
								<span class="navlinkholder homelink"><a class="navlink" href="/index.php">' . getstr('global.home') . '</a></span>
								<span class="navlinkholder"><a class="navlink" href="/polls.php">' . getstr('global.polls') . '</a></span>
								<!--<span class="navlinkholder"><a class="navlink" href="/polls.php?id={pollid}">{polltxt}</a></span>-->
							</div>
							<div id="anketa{posid}">
								{*polls.startrs_ajax}
	',
	
	'polls.startrs_ajax' => '
								<div class="lhfix content">	
									<div class="title">{question}</div>
									<div class="hometext">
										{description}
										<form name="anketa" method="{formmethod}" action="' . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'] . '" onsubmit="return pollsubmit(this,1,\'anketa{posid}\');">
											<input type="hidden" name="formname" value="anketa" />
											<input type="hidden" name="posid" value="{posid}" />
											<input type="hidden" name="id" value="{pollid}" />
											<input type="hidden" name="pollpage" value="{pollpage}" />
											<table border="0" cellspacing="2" cellpadding="2" class="polltable">		

	',
	
	'polls.ansinput' => '
												<tr>
													<td valign="middle"><input type="{inptype}" id="ans_{ansid}" name="anketaans[]" value="{ansid}" valign="middle"></td>
													<td valign="middle">{anstxt}</input></td>
												</tr>
	',
	
	'polls.ansresult' => '
												<tr>
													<td valign="middle">{anstxt}</td>
												</tr>
												<tr>
													<td valign="middle"><img class="resimg" src="/i/bar.jpg" border="0" width="{answidth}" height="8" />&nbsp;{ansprocent}%</td>
												</tr>
		
	',
	
	'polls.rowtempl' => '
		
			{string}
		
	',
	
	'polls.endrs_ajax' => '
											
											</table>
											<div class="unfloat"></div>
											
											<button class="pollbutton pollresultbutton gray" type="submit" onclick="return poll_btnclick(this);" name="votenow" value="Vote" >
												<div class="tabmore " >
													<div class="left"></div>
													<div class="center">' . getstr('polls.vote') . '</div>
													<div class="right"></div>
												</div>		
											</button>
											<button class="pollbutton pollresultbutton gray" type="submit" name="viewtype" value="View results" onclick="return poll_btnclick(this);" >
												<div class="tabmore">
													<div class="left"></div>
													<div class="center"><a title="">' . getstr('polls.statistics') . '</a></div>
													<div class="right"></div>
												</div>
											</button>
											<div class="unfloat"></div><br/>
										</form>
	',	
	
	'polls.endrs' => '
									{*polls.endrs_ajax}
									</div>
								</div>
							</div>
							<div class="unfloat"></div>
							<br/>
						</div>
					
	',	
	
	
	'polls.endrsnobut' => '
									{*polls.endrsnobut_ajax}
									</div>
								</div>
							</div>
							<div class="unfloat"></div>
							<br/>
						</div>
	',
	
	'polls.endrsnobut_ajax' => '
										</table>
										<div class="unfloat"></div><br/>
										
										{display_back}
									</form>
	',
	
	'polls.leftcol_nodata' => '
		' . getstr('polls.noactivepoll') . '
	',
	
	
	'polls.backbutton' => '
		<button class="pollbutton pollresultbutton {btnclass}" type="submit" onclick="return poll_btnclick(this);" value="Back" >
			<div class="tabmore">
				<div class="left"></div>
				<div class="center"><a title="">' . getstr('global.back') . '</a></div>
				<div class="right"></div>
			</div>
		</button>		
		<div class="unfloat"></div>
	',


);
?>	