<?php
$docroot = getenv('DOCUMENT_ROOT');
require_once ($docroot . '/lib/static.php');

$gDocumentId = (int) $_REQUEST['document_id'];

$lResult = array(
	'err_msg' => '',
	'html' => '',
);

if($gDocumentId){
	$lFigures = new crs(
		array(
			'ctype'=>'crs',
			'document_id' => $gDocumentId,
			'templs'=>array(
				G_HEADER=>'global.empty', 
				G_ROWTEMPL=>'figures.document_figures_row_baloon', 
				G_FOOTER =>'global.empty', 
				G_NODATA =>'figures.empty_row',
			),
			'sqlstr'=>'
				(SELECT 	
					m.id as photo_id, 
					m.document_id, 
					m.plate_id,
					m.link,
					null as format_type,
					null as photo_ids_arr,
					null as photo_positions_arr,
					m.title as photo_title, 
					m.description as photo_desc, 
					m.position,
					m.move_position,
					null as plate_desc,
					null as plate_title,
					m.lastmod,
					m.ftype
				FROM pwt.media m
				WHERE m.plate_id IS NULL AND m.document_id = ' . (int)$gDocumentId . ' AND m.ftype <> 1
				UNION
				SELECT
						null as photo_id,
						max(m.document_id) as document_id,
						m.plate_id,
						null as link,
						max(p.format_type) as format_type,
						array_agg(m.id) as photo_ids_arr,
						array_agg(m.position) as photo_positions_arr,
						null as photo_title,
						null as photo_desc,
						null as position,
						max(m.move_position),
						max(p.description) as plate_desc,
						max(p.title) as plate_title,
						max(p.lastmod) as lastmod,
						null as ftype
					FROM pwt.media m
					JOIN pwt.plates p ON p.id = m.plate_id
					WHERE m.document_id = ' . (int)$gDocumentId . ' AND m.ftype <> 1
					GROUP BY m.plate_id
					)
				ORDER BY move_position ASC
			',
		)
	);
	$lFigures->GetData();
	$lResult['html'] = $lFigures->Display();
	displayAjaxResponse($lResult);
}
?>