<?php

$docroot = getenv("DOCUMENT_ROOT");
require_once($docroot . '/lib/static.php');
HtmlStart();//Tova e tuk za da mojem da izpratim header-a za file 

$historypagetype = HISTORY_CLEAR;
define('DEBUG_SCRIPT', 0);

$page = (int)$_GET['p'];

$fld = array(
	'title' => array(
		'VType' => 'string',
		'CType' => 'text',
		'DisplayName' => getstr('admin.photos.titleCol'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => true,
	),
	
	'article_id' => array(
		'CType' => 'select',
		'VType' => 'int',
		'SrcValues' => 'SELECT null as id, \'--\' as name UNION SELECT id, id || \'--\' || title as name FROM articles ORDER BY id',
		'DisplayName' => getstr('admin.photos.article_id'),
		'AddTags' => array(
			'class' => 'coolinp',
		),
		'AllowNulls' => false,
	),
	
	'show' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.filterButton'),
		'SQL' => '',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
	
	'download' => array(
		'CType' => 'action',
		'DisplayName' => getstr('admin.downloadButton'),
		'SQL' => '{article_id}',
		'ActionMask' => ACTION_CHECK | ACTION_SHOW,
		'AddTags' => array(
			'class' => 'frmbutton',
		),
	),
);

$frm = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="5" border="0" class="formtable">
				<colgroup>
					<col width="50%"/>
					<col width="50%"/>
				</colgroup>
				<tr>
					<th colspan="2">' . getstr('admin.photos.filter') . '</th>
				</tr>
				<tr>
					<td>{*title}<br/>{title}</td>
					<td>{*article_id}<br/>{article_id}</td>
				</tr>
				<tr>
					<td colspan="2" align="right">{show} {download}</td>
				</tr>
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
';


$kfor = new kfor($fld, $frm, 'GET');
if( $kfor->lCurAction == 'download'){
	$lArticleId = (int) $kfor->lFieldArr['article_id']['CurValue'] ;
	if( $lArticleId ){//Slagame vsichki snimki za statiqta v arhiv i gi podavame za download
		$lCon = Con();
		$lSql = '	
			SELECT p.guid, p.filenameupl
			FROM photos p
			JOIN storyproperties sp ON sp.valint = p.guid AND sp.propid = ' . (int) ARTICLE_PHOTOS_PROPID . '
			JOIN articles s ON s.id = sp.guid
			WHERE s.id = ' . (int) $lArticleId . '
		';
		$lCon->Execute($lSql);
		$lCon->MoveFirst();
		$lHasError = false;
		if( (int)$lCon->mRecordCount ){
			/**
				Pravim temp file vyv temp direktoriqta posle go triem i pravim nova direktoriq sys syshtoto ime.
				Polzvame go na praktika za da napravim direktoriq sys ime, koeto ne e zaeto
			*/
			$lTempDir = tempnam(sys_get_temp_dir(), 'Photos_tmp_dir');
			if( unlink($lTempDir) ){
				if( mkdir($lTempDir) ){
					if((int) DEBUG_SCRIPT )
						echo $lTempDir . '<br/>';
					$lErrorMsg = '';
					while(!$lCon->Eof()){
						$lPhotoGuid = (int) $lCon->mRs['guid'];
						$lPhotoRealName = $lCon->mRs['filenameupl'];
						$lOriginalPhotoPath = PATH_DL . 'oo_' . (int) $lPhotoGuid . '.jpg';
						$lCon->MoveNext();
						
						if((int) DEBUG_SCRIPT )
							echo $lOriginalPhotoPath . ' - ' . $lTempDir . '/' . $lPhotoRealName . '<br/>';
						
						if (!copy($lOriginalPhotoPath, $lTempDir . '/' . $lPhotoRealName)) {
							$lHasError = true;
							$lErrorMsg = getstr('admin.photos.couldNotCopyPhotoToTempDir');
							break;
						}
					}
					if( !$lHasError ){//Pravim arhiv i go davame za dl
						$lArchiveFile = $lTempDir . '/photos.tar';
						exec('cd ' . $lTempDir . ' && tar -cf ' . $lArchiveFile . ' *');
						if((int) DEBUG_SCRIPT )
							echo 'Archive - ' .  $lArchiveFile . '<br/>';
						if( is_file($lArchiveFile) ){
							header('Content-Description: File Transfer');
							header('Content-Type: application/octet-stream');
							header('Content-Disposition: attachment; filename="photos_' . $lArticleId . '.tar"');
							header('Content-Transfer-Encoding: binary');
							header('Expires: 0');
							header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
							header('Pragma: public');
							header('Content-Length: ' . filesize($lArchiveFile));							
							
							ob_clean();
							flush();
							readfile($lArchiveFile);
						}else{
							$lHasError = true;
							$lErrorMsg = getstr('admin.photos.errorDuringGeneratingArchive');	
						}
					}
					
					//Triem temp direktoriqta i vsichki failove v neq
					$lDirHandle = opendir($lTempDir);					
					if( $lDirHandle !== 'false' ){
						while( false !== ($lFileName = readdir( $lDirHandle ) ) ){
							$lAbsFilePath = $lTempDir . '/' . $lFileName;								
							if( is_file($lAbsFilePath) ){
								if((int) DEBUG_SCRIPT )
									echo 'Removing file ' . $lAbsFilePath . '<br/>';								
								if( !unlink( $lAbsFilePath ) ){
									$lHasError = true;
									$lErrorMsg .= getstr('admin.photos.errorDuringDeletingFile');	
								}								
							}
						}
						if((int) DEBUG_SCRIPT )
							echo 'Removing dir ' . $lTempDir . '<br/>';
						if( ! rmdir( $lTempDir ) ){
							$lHasError = true;
							$lErrorMsg .= getstr('admin.photos.errorDuringDeletingTempDir');	
						}
					}else{
						$lHasError = true;
						$lErrorMsg = getstr('admin.photos.errorDuringDeletingFile');	
					}
					
				}else{
					$lHasError = true;
					$lErrorMsg = getstr('admin.photos.couldNotCreateTempDir');					
				}
			}else{
				$lHasError = true;
				$lErrorMsg = getstr('admin.photos.couldNotCreateTempDir');				
			}
		}else{
			$lHasError = true;
			$lErrorMsg = getstr('admin.photos.articleHasNoPhotos');			
		}
		
		//Ako ima greshka q pokazvame - inache exitvame		
		if( $lHasError ){
			$kfor->SetError('download', $lErrorMsg);
		}else{
			exit;
		}
		
	}
}


echo $kfor->Display();

$addWhere = '';
if ($kfor->lCurAction == 'show') {
	if ($kfor->lFieldArr['title']['CurValue']) {
		$addWhere .= ' AND lower(p.filenameupl) ILIKE \'%' . q(mb_strtolower($kfor->lFieldArr['title']['CurValue'], 'UTF-8')) . '%\' ';
	}
	
	if ((int)($kfor->lFieldArr['article_id']['CurValue'])) {
		$addWhere .= ' AND s.id = ' . (int)$kfor->lFieldArr['article_id']['CurValue'];
	}
}

$gFArr = array(
	1001 => array('caption' => '  ', 'deforder' => 'desc'),
	1 => array('caption' => getstr('admin.photos.idCol'), 'deforder' => 'desc'), 
	2 => array('caption' => getstr('admin.photos.titleCol'), 'deforder' => 'asc'), 
	3 => array('caption' => getstr('admin.photos.picCol'), 'deforder' => 'asc'), 
	4 => array('caption' => getstr('admin.photos.uploadedCol'), 'deforder' => 'desc', 'def'), 
	5 => array('caption' => getstr('admin.photos.articleTitleCol'), 'deforder' => 'desc', 'def'), 
	1000 => array('caption' => '  ', 'deforder' => 'desc'),
);

$lTableHeader = '
	<div class="t">
	<div class="b">
	<div class="l">
	<div class="r">
		<div class="bl">
		<div class="br">
		<div class="tl">
		<div class="tr">
			<table cellspacing="0" cellpadding="6" border="0" class="gridtable">
				<tr>
					<th class="gridtools" colspan="7">
						<a href="/resources/articles/upload_article_picture.php">' . getstr('admin.photos.addPhoto') . '</a>
						' . getstr('admin.photos.antetka') . '
					</th>
				</tr>
';

$lTableFooter = '
			</table>
		</div>
		</div>
		</div>
		</div>
	</div>
	</div>
	</div>
	</div>
';

$t = '<tr>
	<td valign="top">
		<a href="/lib/show_pic_by_filename.php?filename={filenameupl}" target="_blank"><img src="/showimg.php?filename=mx50_{guid}.gif" border="0" alt="" /></a>
	</td>
	<td valign="top">{guid}</td>
	<td valign="top">{title}</td>
	<td valign="top">{filenameupl}</td>
	<td valign="top">{createdate}</td>
	<td valign="top"><a href="/resources/articles/edit.php?id={article_id}&tAction=showedit" target="_blank">{article_title}</a></td>
	<td align="right" valign="top">
		<a href="./edit.php?guid={guid}&tAction=show"><img src="/img/edit.gif" border="0" alt="' . getstr('admin.editLabel') . '" title="' . getstr('admin.editLabel') . '" /></a>
		<a href="javascript:if (confirm(\'' . getstr('admin.photos.confirmDel') . '\')) { window.location = \'./edit.php?guid={guid}&tAction=delete\';} else {}" ><img src="/img/trash2.gif" border="0" alt="' . getstr('admin.removeLabel') . '" title="' . getstr('admin.removeLabel') . '" /></a>
	</td>
</tr>';

$l = new DBList($lTableHeader);
$l->SetCloseTag($lTableFooter);
$l->SetTemplate($t);
$l->SetPageSize(30);
$l->SetOrderParams((int)$_GET['ob1'], (int)$_GET['odd1']);
$l->SetOrderParamNames('ob1', 'odd1');
$l->SetAntet($gFArr);
$lSql = '
	SELECT p.guid, p.title, p.filenameupl, p.createdate::date, s.id as article_id, s.title as article_title
	FROM photos p
	JOIN storyproperties sp ON sp.valint = p.guid AND sp.propid = ' . (int) ARTICLE_PHOTOS_PROPID . '
	JOIN articles s ON s.id = sp.guid
	WHERE ftype = 0 ' . $addWhere;
//~ var_dump($lSql);
$l->SetQuery($lSql);

if (!$l->DisplayList($page)) {
	echo $lTableHeader . '<tr><td colspan="6"><p align="center"><b>' . getstr('admin.photos.noData') . '</b></p></td></tr>' . $lTableFooter;
}

HtmlEnd() ;

function pubdate(&$pRs) {
	if (!$pRs['pubdate']) return '&nbsp;';
	else return $pRs['pubdate'];
}

?>