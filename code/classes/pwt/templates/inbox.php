<?php

$gTemplArr = array(
	'inbox.browse_head' => '
			<div class="P-Inbox-Messages-Container">
				<div class="P-Section-Title-Holder">
					<table cellspacing="0" cellpadding="0" class="P-Data-Resources-Head">
						<tbody>
							<tr>
								<td class="P-Data-Resources-Head-Text">Inbox&nbsp;Messages</td>
								<td class="P-Inline-Line"></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="P-Inline-Line"></div>
				<div class="P-Messages-Menu">
					<div class="P-Messages-Menu-Left-Row">{_showMessageLink(\'show\', t1, show, ' . getstr('pwt.messages.allmessages') . ')}</div>
					<div class="P-Messaging-Menu-Sep"></div>
					<div class="P-Messages-Menu-Left-Row">{_showMessageLink(\'show\', t3, show, ' . getstr('pwt.messages.sentmessages') . ')}</div>
					<div class="unfloat"></div>
				</div>
				<div class="P-Inline-Line"></div>
				
	',
	
	'inbox.browse_foot' => '
			</div>
	',
	
	'inbox.browse_start' => '
				<!-- <form name="delForm" id="delForm" method="post" action="/inbox.php" enctype="multipart/form-data">
				<input type="hidden" id="action" name="action" value=""></input> -->
	',
	
	'inbox.browse_end' => '
				<!-- </form> -->
	',
	
	'inbox.browse_row' => '
		<div class="P-Message-Row">
			<div class="P-Message-Row-Info">
				<div class="P-Message-Row-User-Pic"><img border="0" src="/showimg.php?filename=c30x30y_{senderphoto}.jpg" alt="" /></div>
				<div class="P-Message-Row-Info-Main">
					<div class="P-Message-Row-From">
						from <span>{sender}<span>
					</div>
					received <b>{createdate}</b>
					<div class="P-Message-Row-Title">
						Subject: <b>{subject}</b>
					</div>
					<div class="P-Message-Row-Body-Title">Message</div>
					<div class="P-Message-Row-Body">
						{msg}
					</div>
				</div>
				<div class="unfloat"></div>
			</div>
			<div class="unfloat"></div>
		</div>
		<div class="P-Inline-Line"></div>
	',
	
	'inbox.browse_nodata' => '<div class="P-Data-Resources-Head">' . getstr('pwt.messages.nomessages') . '</div>',
);

?>