<?php

class cforum extends crs {
	var $form;
	
	function __construct($pFieldTempl) {
		global $user;
		parent::__construct($pFieldTempl);
		//~ $this->con->debug = true;
		$this->LoadPubdataDefs();
		$this->m_pubdata['postform'] = '';
		$this->PostForm();
		if ($this->m_pubdata['showtype'] == 1) { //temi po diskusia
			
			$this->m_pubdata['sqlstr'] = 'SELECT rootid, subject, author, mdate, replies, views, 
				dscname as dsc_name, lastmoddate, dscid as dsc_id, uid, uname, usertype, flags as mflags 
			FROM ForumGetTopics(' . (int)$this->m_pubdata['dscid'] . ', ' . (int)$this->m_pubdata['dsggroup'] . ') 
			WHERE flags & 8 = 0 AND itemid IS NULL
			ORDER BY ' . $this->SetOrderParam($this->m_pubdata['ordby'], $this->m_pubdata['ordtype'], 'lastmoddate', array(2,3,4,5,6,7,8));
			
		} elseif ($this->m_pubdata['showtype'] == 2) { //temi po tema
			
			$this->m_pubdata['sqlstr'] = 'SELECT id, subject, author, msg, mdate, rootid, dsc_name, 
				dsc_id, topic_name, topic_id, topicflags, mflags, itemid, uid, uname, ord, replies, dsg_name 
			FROM ForumGetMsgFlatHtml(' . (int)$this->m_pubdata['topicid'] . ', '. (int)CMS_SITEID .', ' . (int)$this->m_pubdata['dsggroup'] . ', NULL, NULL) 
			WHERE mflags & 4 <> 4 AND topicflags & 4 <> 4
			ORDER BY ' . 
			$this->SetOrderParam(null, $this->m_pubdata['ordtype'], 'mdate') . ', ' . 
			$this->SetOrderParam(null, $this->m_pubdata['ordtype'], 'id');
			
		} elseif ($this->m_pubdata['showtype'] == 3) { //temi po tema pod obekt
			
			$this->m_pubdata['sqlstr'] = 'SELECT * FROM UnderForumGetMsgFlatHtml('. (int)$this->m_pubdata['dscid'] .', '. (int)$this->m_pubdata['storyid'] . ') WHERE topicflags & 4 <> 4  AND mflags & 4 <> 4 ORDER BY mdate '. $this->m_pubdata['ordtype'] . ';';
			
		} elseif ($this->m_pubdata['showtype'] == 4) { //popup za skriti suobshtenia
			
			$this->m_pubdata['sqlstr'] = 'SELECT id, subject, author, msg, mdate, dsc_name as dsc_name, dsc_id, 
				topic_name, topic_id, topicflags, mflags, uname, dsg_name 
			FROM ForumGetSingleMsg(' . (int)$this->m_pubdata['msgid'] . ', ' . (int)$this->m_pubdata['dsggroup'] . ')';
			
		} elseif ($this->m_pubdata['showtype'] == 5) { //list na diskusiite
			$this->m_pubdata['sqlstr'] = '
				SELECT d.id AS id,d.name AS name,count(DISTINCT m.rootid) AS topicnum, 
					count(m.id) AS msgnum, max(m.mdate) AS lastdate 
				FROM dsc d 
				LEFT JOIN msg m ON (d.id = m.dscid AND m.flags & 4 <> 4) 
				WHERE d.dsgid  = ' . (int)$this->m_pubdata['dsggroup'] . '
				GROUP BY name, d.id
			';
		}
		
	}
	
	function LoadPubdataDefs() {
		
		if (!(int)$this->m_pubdata['storyid']) {
			$this->m_pubdata['storyid'] = (int)$_REQUEST['storyid'];
		}	
		if (!(int)$this->m_pubdata['storyid']) {
			$this->m_pubdata['storyid'] = (int)$_REQUEST['itemid']; 
		}
		if (!(int)$this->m_pubdata['dscid']) {
			$this->m_pubdata['dscid'] = (int)$_REQUEST['dscid'];
		}
		if (!(int)$this->m_pubdata['topicid']) {
			$this->m_pubdata['topicid'] = (int)$_REQUEST['topicid'];
		}
		if (!(int)$this->m_pubdata['msgid']) {
			$this->m_pubdata['msgid'] = (int)$_REQUEST['msgid'];
		}
		if (!(int)$this->m_pubdata['dsggroup']) {
			$this->m_pubdata['dsggroup'] = (int)$_REQUEST['dsgid'];
		}
		$this->m_pubdata['ordtype'] = 'ASC';
		if ($this->m_pubdata['sorttypedef']) {
			$this->m_pubdata['ordtype'] = strtoupper($this->m_pubdata['sorttypedef']);
		}
		if ($this->m_pubdata['sort']) {
			if ($this->m_pubdata['ordtype'] == 'ASC') {
				$this->m_pubdata['ordtype'] = 'DESC';
			} else {
				$this->m_pubdata['ordtype'] = 'ASC';
			}
		}
		if (!(int)$this->m_pubdata['ordby']) {
			$this->m_pubdata['ordby'] = (int)$_REQUEST['ordby'];
		}
		if (!(int)$this->m_pubdata['showtype']) {
			$this->m_pubdata['showtype'] = 1;
		}
		if (isset($this->m_pubdata['showforum'])) {
			global $COOKIE_DOMAIN;
			$this->m_pubdata['showforum'] = (!array_key_exists('showforum', $_COOKIE) ? 1 : $_COOKIE['showforum']);
			$expire = mktime() + (24 * 3600 * 7);

			if ($_REQUEST['show'] === '0') {
				setcookie('showforum' , 0, $expire, '/', $COOKIE_DOMAIN);
				$this->m_pubdata['showforum'] = 0;
			} elseif ($_REQUEST['show'] === '1') {
				setcookie('showforum', $_REQUEST['show'], $expire, '/', $COOKIE_DOMAIN);
				$this->m_pubdata['showforum'] = 1;
			}
		}
	}
	
	function PostForm() {
		global $user;
		
		if( (int) $this->m_pubdata['requirelogin'] && ! (int) $user->id ){
			return;
		}
		$frmTempl = 'forum.normalform';
		if ($this->m_pubdata['showtype'] == 1) {
			$frmTempl = 'forum.advancedform';
		} elseif (!$this->m_pubdata['openforum']) {
			$frmTempl = 'forum.closed';
		}
		
		$loggedArr = array();
		if ((int)$user->id) 
			$loggedArr = array(
				'class' => 'input',
				'onclick' => 'return false;',
				'onchange' => 'return false;',
			);
		
		$this->form = new ctplkfor(
			array(
				'ctype' => 'ctplkfor',
				'method' => 'POST',
				'setformname' => 'forumpost' . (int)$this->m_pubdata['formname'] ? $this->m_pubdata['formname'] : '',
				'flds' => array(
					'hascaptcha' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['captcha'] ? 1 : 0),
						'AllowNulls' => true,
					),
					'storyid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['storyid'] ? (int)$this->m_pubdata['storyid'] : ''),
						'AllowNulls' => true,
					),
					'dscid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['dscid'] ? (int)$this->m_pubdata['dscid'] : ''),
						'AllowNulls' => true,
					),
					
					'topicid' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'DefValue' => ((int)$this->m_pubdata['topicid'] ? (int)$this->m_pubdata['topicid'] : ''),
						'AllowNulls' => true,
					),
					
					'sort' => array(
						'VType' => 'int',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
					),
					
					'dsc_name' => array(
						'VType' => 'string',
						'CType' => 'hidden',
						'DisplayName' => '',
						'AllowNulls' => true,
					),
					
					'author' => array(
						'VType' => 'string',
						'CType' => 'text',
						'DisplayName' => getstr('forum.author'),
						'DefValue' => ((int)$user->id ? $user->fullname : ''),
						'AddTags' => array_merge(array('class' => 'nameinput'), $loggedArr),
					),
					
					
					'msg' => array(
						'VType' => 'string',
						'CType' => 'textarea',
						'Checks' => array(
							CKMAXSTRLEN('{msg}', 4096),
						),
						'AddTags' => array(
							'class' => 'commentarea',
							'rows' => '6',
						),
						'DisplayName' => getstr('forum.msg'),
					),
					
					'subject' => array(
						'VType' => 'string',
						'CType' => 'text',
						'AddTags' => array(
							'class' => 'input',
						),
						'AllowNulls' => true,
						'DisplayName' => getstr('forum.subject'),
					),
					
					'save' => array(
						'CType' => 'action',
						'DisplayName' => 'save',
						'ButtonHtml' => '
							<div class="commentssubmitholder">
								<button class="commentsbutton" type="submit" name="tAction" value="{value}"><!-- tAction=###save### -->
									<div class="tabmore">
										<div class="left"></div>
										<div class="center">' . getstr('forum.save') . '</div>
										<div class="right"></div>
									</div>
									
								</button>
								<div class="unfloat"></div>
							</div>
						',
						'SQL' => 'SELECT * FROM ForumAddMsg({topicid}, {dscid}, {storyid}, ' . ((int)$user->id ? '\'' . q($user->fullname) . '\'' : '{author}') . ', {subject}, {msg}, {msg}, \'' . $_SERVER['REMOTE_ADDR'] . '\',' . ((int) $user->id ? (int) $user->id : 'null') . ',' . ((int) $user->id ? ('\'' .q($user->uname) . '\'') : 'null') . ',null)',
						'RedirUrl' => $this->m_pubdata['returl'],
						'ActionMask' => ACTION_CHECK | ACTION_CCHECK | ACTION_EXEC | ACTION_FETCH | ACTION_REDIRECT,
					),
				),
				'templs' => array(
					G_DEFAULT => $frmTempl,
				),
			)
		);
		$this->form->SetKforVal('hascaptcha', (int) $this->m_pubdata['captcha']);
			
		$lCon = Con();
		$lSql = 'SELECT flags FROM msg WHERE id = rootid AND (id = ' . $this->m_pubdata['topicid'] . ' OR itemid = ' . $this->m_pubdata['storyid'] . ') AND dscid = ' . $this->m_pubdata['dscid'];
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lFlag = $lCon->mRs['flags'];
		$lClosed = (int)  $lFlag & 1;
		if($this->m_pubdata['showtype'] == 1){
			$this->form->setProp('subject', 'AllowNulls', false);
		}
		
		if($this->form->KforAction() == 'save'){
			if( !$this->form->getKforVal('msg') )
				$this->form->KforSetErr('{*msg}', getstr('global.emptyfield'));
			$this->form->setProp('save', 'SQL', 'SELECT * FROM ForumAddMsg({topicid}, {dscid}, {storyid}, ' . ((int)$user->id ? '\'' . q($user->fullname) . '\'' : '{author}') . ', {subject}, \'' . q(h(s($this->form->getKforVal('msg')))) . '\', \'' . linebr(q(s($this->form->getKforVal('msg')))) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\',' . ((int) $user->id ? (int) $user->id : 'null') . ',' . ((int) $user->id ? ('\'' .q($user->uname) . '\'') : 'null') . ',null)');
			if($this->m_pubdata['showtype'] == 8){
				$this->form->setProp('save', 'SQL', 'SELECT * FROM ForumAddMsgAll({topicid}, {dscid}, {storyid}, ' . ((int)$user->id ? '\'' . q($user->fullname) . '\'' : '{author}') . ', {subject}, \'' . q(h(s($this->form->getKforVal('msg')))) . '\', \'' . linebr(q(s($this->form->getKforVal('msg')))) . '\', \'' . $_SERVER['REMOTE_ADDR'] . '\',' . ((int) $user->id ? (int) $user->id : 'null') . ',' . ((int) $user->id ? ('\'' .q($user->uname) . '\'') : 'null') . ',null)');
			}
			if( (int) $this->m_pubdata['captcha'] ){
				//~ var_dump($_SESSION['frmcapt']);
				//~ echo '<br/>';
				//~ var_dump($_POST['captcha']);
				if (in_array(strtolower($_POST['captcha']), $_SESSION['frmcapt'])) {
					foreach ($_SESSION['frmcapt'] as $captkey => $captval) {
						if ($captval == strtolower($_POST['captcha'])) unset($_SESSION['frmcapt'][$captkey]);
					}
				} else {
					$this->form->KforSetErr(getstr('global.captchacode'), getstr('global.captchaerr'));
				}
			}
		}
		
		$this->form->GetData();
		if(!(int) $this->m_pubdata['openforum'] || $lClosed)
			$this->m_pubdata['postform'] = $this->DisplayTemplate('forum.closed');
		else
			$this->m_pubdata['postform'] = $this->form->Display();
		return;
	}
	
	function SetOrderParam($ordby, $pos, $def, $allowed = array()) {
		if (!(int)$ordby || !in_array((int)$ordby, $allowed)) return ' ' . $def . ' ' . $pos;
		return ' ' . (int)$ordby . ' ' . $pos;
	}
	
	function GetRows() {
		$i = 0;
		while (!$this->con->Eof()) {
			$this->GetNextRow();
			
			if ($this->m_pubdata['id'] == $this->m_pubdata['rootid'] && $this->m_pubdata['itemid']) {
				$this->m_pubdata['topicsubject'] = $this->m_pubdata['subject'];
			} else {
				if ($this->m_pubdata['showtype'] == 1 && $this->m_pubdata['mflags'] & 8) {
					// iztrita forumna tema
					$lRet .= '';
				} elseif ($this->m_pubdata['showtype'] == 1 || $this->m_pubdata['showtype'] == 2 || $this->m_pubdata['showtype'] == 3) {
					if ($this->m_pubdata['mflags'] & 4) {
						$lRet .= ''; // $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL_DEL));
						$this->m_pubdata['deleted']++;
					} elseif ($this->m_pubdata['mflags'] & 2 && $this->m_pubdata['showtype'] != 4) {
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL_HIDDEN));
						$this->m_pubdata['rownum']++;
					} else {
						$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
						$this->m_pubdata['rownum']++;
					}
				} else {
					$lRet .= $this->ReplaceHtmlFields($this->getObjTemplate(G_ROWTEMPL));
					$this->m_pubdata['rownum']++;
				}
			}
				
			$i++;
		}
		return $lRet;
	}
}

?>