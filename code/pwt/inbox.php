<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once($docroot . '/lib/static.php');

if (!(int)$user->id) {
	header('Location: /');
	exit;
}


$lRootId = (int) $_REQUEST['rootid'];
$lShow = $_REQUEST['show'];

if(!$lRootId){//browse
	$lShowAllowed = array(1, 2, 3);
	if( ! (int) $lShow || !in_array( (int) $lShow, $lShowAllowed ) ) {
		$lShow = 1;
	}
	if((int)$lShow) {
		switch ((int)$lShow) {
			case 1:{//all
				$lCondSended = ' AND coalesce(realmsg.msgs, 0) > 0 ';
				$lCondition = '
					AND i.recipient_id = ' . (int)$user->id . '
					AND i.recipient_state <> 3
				';
				break;
			}
			case 2:{//unread
				$lCondSended = ' AND coalesce(realmsg.msgs, 0) > 0 ';
				$lCondition = ' AND (CASE WHEN i.sender_id = ' . (int)$user->id . ' THEN i.sender_state = 1 ELSE i.recipient_state = 1 END) 
					AND (i.sender_id = ' . (int)$user->id . ' OR i.recipient_id = ' . (int)$user->id . ') 
					AND (CASE WHEN i.sender_id = ' . (int)$user->id . ' THEN i.sender_state <> 3 ELSE i.recipient_state <> 3 END) 
				';
				break;
			}
			case 3:{//sended
				$lCondSended = ' AND coalesce(realsentmsg.msgs, 0) > 0 ';
				$lCondition = ' 
					AND i.sender_id = ' . (int)$user->id . '
					AND i.sender_state <> 3 
				';
				break;
			}
			default: break;
		}
	}
	
	//inbox messages
	$lSql = 'SELECT * FROM (
				SELECT DISTINCT ON (i.rootid)
					i.id as id, 
					i.msg as msg, 
					date_trunc(\'seconds\', i.createdate) as createdate, 
					coalesce(u1.first_name, \'\') || \' \' || coalesce(u1.last_name, \'\') as sender,
					coalesce(u2.first_name, \'\') || \' \' || coalesce(u2.last_name, \'\') as receiver,
					i.sender_state as sender_state,
					i.recipient_state as recipient_state,
					i.subject as subject, 
					i.sender_id as sender_id, 
					i.recipient_id as recipient_id,
					i.rootid as rootid,
					unr.id as unread,
					coalesce(u1.photo_id, ' . (int) DEFAULT_USER_PIC_ID . ') as senderphoto
					
				FROM inbox i 
				JOIN usr u1 ON i.sender_id = u1.id
				JOIN usr u2 ON i.recipient_id = u2.id
				LEFT JOIN (
					SELECT DISTINCT ON (rootid) rootid, id FROM inbox 
					WHERE recipient_id = ' . (int)$user->id . ' AND recipient_state = 1
					ORDER BY rootid
				) as unr ON unr.rootid = i.rootid
				LEFT JOIN (
					SELECT DISTINCT ON (rootid) rootid, coalesce(count(id), 0) as msgs FROM inbox 
					WHERE sender_id <> ' . (int)$user->id . ' AND recipient_state <> 3
					GROUP BY rootid
				) as realmsg ON realmsg.rootid = i.rootid
				LEFT JOIN (
					SELECT DISTINCT ON (rootid) rootid, coalesce(count(id), 0) as msgs FROM inbox 
					WHERE sender_id = ' . (int)$user->id . ' AND sender_state <> 3
					GROUP BY rootid
				) as realsentmsg ON realsentmsg.rootid = i.rootid
				WHERE i.type = 1
				' . $lCondSended . '
				' . $lCondition . '
				ORDER BY i.rootid, i.createdate DESC) as a
			ORDER BY a.createdate DESC';
			//~ echo $lSql;
	$lMsg = new crs_custom_pageing(
		array(
			'ctype' => 'crs_custom_pageing',
			'sqlstr' => $lSql,
			'sent' => $lSent,
			'count' => $lAllMessages,
			'unread' => $lUnreadMessages,
			'show' => $lShow,
			//~ 'ordby' => $lOrdBy,
			'name' => $user->fullname,
			//~ 'dir' => $lDir,
			'pagesize' => 20,
			'usecustompn' => 1,
			'templs' => array(
				G_HEADER => 'inbox.browse_head',
				G_STARTRS => 'inbox.browse_start',
				G_FOOTER => 'inbox.browse_foot',
				G_ENDRS => 'inbox.browse_end',
				G_ROWTEMPL => 'inbox.browse_row',
				G_NODATA => 'inbox.browse_nodata',
			),
		)
	);
	
} else {//show
	
	$lStatusUpdate = new crs_custom_pageing(
		array(
			'ctype' => 'crs_custom_pageing',
			'sqlstr' => 'SELECT * FROM sp_inboxmsgstatus(' . $lRootId . ', ' . $user->id . ')',
		)
	);
	$lStatusUpdate->GetData();
	
	$lRecipients = GetRecipients($lRootId);
	
	$lSql = '
		SELECT * FROM (
			SELECT DISTINCT ON (msg, createdate)
				i.id, 
				i.msg, 
				date_trunc(\'seconds\', i.createdate) as createdate, 
				u1.name as sender, 
				coalesce(u1.usrphoto, ' . (int) DEFAULT_USER_PIC_ID . ') as sender_pic,
				u2.name as receiver, 
				i.recipient_state, 
				i.subject, 
				i.sender_id, 
				i.recipient_id,
				i.rootid
			FROM inbox i 
			JOIN usr u1 ON i.sender_id = u1.id
			JOIN usr u2 ON i.recipient_id = u2.id
			WHERE i.rootid = ' . $lRootId . ' 
				AND (CASE WHEN i.sender_id = ' . $user->id . ' THEN i.sender_state<>3 WHEN i.recipient_id = ' . $user->id . ' THEN i.recipient_state<>3 ELSE false END)
				AND i.type = 1
			ORDER BY msg, createdate DESC) as a 
		ORDER BY a.createdate DESC';
	//~ echo $lSql;
	$lMsg = new crs_custompageing(
		array(
			'ctype' => 'crs_custompageing',
			'sqlstr' => $lSql,
			'sent' => $lSent,
			'count' => $lAllMessages,
			'unread' => $lUnreadMessages,
			'current_user' => $user->id,
			//~ 'dir' => $lDir,
			'recipients' => $lRecipients,
			'name' => $user->fullname,
			'templs' => array(
				G_HEADER => 'inbox.show_head',
				G_FOOTER => 'inbox.show_foot',
				G_STARTRS => 'inbox.show_start',
				G_ENDRS => 'inbox.show_end',
				G_ROWTEMPL => 'inbox.show_row',
				G_NODATA => 'inbox.show_nodata',
			),
		)
	);
}
//~ $lMsg->GetData();

$t = array(
	'content' => $lMsg->Display(),
);

$inst = new cpage(array_merge($t, DefObjTempl()), array(G_MAINBODY => 'global.inbox'));
$inst->Display();
?>