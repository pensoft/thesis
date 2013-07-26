<?php

/**
 * Този клас ще реализира копирането на документ.
*/
class cdocument_copy{
	var $m_errorCount;
	var $m_errorMsg;
	
	var $m_oldDocumentId;	
	var $m_newDocumentId;

	/**
	 *
	 * Конекция към базата
	 * @var DBCn
	 */
	var $m_con;



	function __construct($pFieldArr){

		$this->m_errorCount = 0;
		$this->m_errorMsg = '';		
		$this->m_oldDocumentId = $pFieldArr['document_id'];
		
		if(!(int)$this->m_oldDocumentId){
			$this->SetError(getstr('pwt.nodDocumentSpecified'));
			return ;
		}
		
		$this->m_con = new DBCn();
		$this->m_con->Open();
	
	}

	function HasErrors(){
		return $this->m_errorCount;
	}

	function GetErrorMsg(){
		return $this->m_errorMsg;
	}

	function GetNewDocumentId(){
		return $this->m_newDocumentId;
	}
	

	function GetData(){
		if($this->m_errorCount){
			return;
		}
// 		return;
		try {
			//Стартираме транзакцията
			$this->ExecuteSqlQuery('BEGIN TRANSACTION;');
			
			$this->ExecuteSqlQuery('SELECT * FROM spCopyDocument(' . (int)$this->m_oldDocumentId . ')');
			$this->m_newDocumentId = (int)$this->m_con->mRs['document_id'];
			if(!$this->m_newDocumentId){
				$this->SetSqlError(getstr('pwt.couldNotGetNewDocumentId'));
			}
			$this->CopyMediaFiles();
			$this->DropTempTables();

			//Ако всичко е ОК - къмитваме
			$this->ExecuteSqlQuery('COMMIT TRANSACTION;');			
		} catch (Exception $e) {
			/*
			 * При грешка - спираме всичко надолу. Класа автоматично ще ролбекне
			 * Затова няма нужда да зачистваме нещо.
			 */
			return;
		}
	}
	
	protected function CopyMediaFiles(){
		//Copy all the files first
		$lSql = '
			SELECT * 
			FROM media_temp 				
		';
		$this->ExecuteSqlQuery($lSql);
		
		$lPngExt = '.png';
		$lPrefixes = array('oo_', 'big_');
		$lOriginalNamesToBeUpdated = array();
		while(!$this->m_con->Eof()){
			$lOriginalFileName = $this->m_con->mRs['original_name'];
			$lPreviousId = $this->m_con->mRs['id'];
			$lNewId = $this->m_con->mRs['new_id'];
			$lFileExt = substr($lOriginalFileName, strrpos($lOriginalFileName, '.'));
			if(!$lNewId || !$lPreviousId){
				continue;
			}
			$lExtensions = array($lFileExt);			
			if($lFileExt != $lPngExt){
				$lExtensions[] = $lPngExt;
			} 
			
			foreach ($lPrefixes as $lCurrentPrefix){
				foreach ($lExtensions as $lCurrentExt){
					$lOriginalFilePath = PATH_DL . $lCurrentPrefix . $lPreviousId . $lCurrentExt;
					$lNewFilePath = PATH_DL . $lCurrentPrefix . $lNewId . $lCurrentExt;
					if(file_exists($lOriginalFilePath) && !copy($lOriginalFilePath, $lNewFilePath)){
						$this->SetSqlError(getstr('pwt.couldNotCopyFile') . $lOriginalFilePath );
					}
				}
			}
		
			$this->m_con->MoveNext();
		}
		//Update all the id-s in fields of file_upload_type
		$lSql = '
				UPDATE pwt.instance_field_values iv SET
					value_int = t.new_id
				FROM pwt.object_fields f 
				JOIN media_temp t ON true
				JOIN pwt.document_object_instances i ON true
				WHERE f.field_id = iv.field_id AND f.object_id = i.object_id AND t.id = iv.value_int 
					AND i.id = iv.instance_id AND i.document_id = ' . (int) $this->m_newDocumentId . '
					AND f.control_type IN (' . (int) FIELD_HTML_FILE_UPLOAD_TYPE . ', ' . (int)FIELD_HTML_FILE_UPLOAD_FIGURE_IMAGE . ', ' . (int)FIELD_HTML_FILE_UPLOAD_FIGURE_PLATE_IMAGE . ') 
		';
		$this->ExecuteSqlQuery($lSql);
		//Update the original name in the media table
		$lSql = '
				UPDATE pwt.media m SET
					original_name = replace(m.original_name, \'_\' || t.id || \'.\', \'_\' || t.new_id || \'.\')
				FROM media_temp t
				WHERE m.id = t.new_id AND m.document_id = ' . (int) $this->m_newDocumentId . '
		';
		$this->ExecuteSqlQuery($lSql);
	}
	
	protected function DropTempTables(){		
		$lTablesList = array('document_template_objects_temp', 'document_object_instances_temp', 
			'instance_field_values_temp', 'document_revisions_temp', 'msg_temp', 
			'document_users_temp', 'plates_temp', 'media_temp', 'tables_temp', 'citations_temp'
		);
		foreach ($lTablesList as $lTableName){
			$this->ExecuteSqlQuery('DROP TABLE ' . q($lTableName));
		}
	}
	
	/**
	 * Изпълняваме sql заявка. Ще използваме член променливата за конекция към базата.
	 * Ако гръмне - ролбек-ваме.
	 * При грешка ще хвърляме exception, за да може да не правим след всяка команда проверка дали всичко е минало успешно,
	 * а наведнъж да обработваме грешка при коя да е заявка.
	 * @param string $lQuery - заявката, която ще се опитаме да изпълним.
	 */
	protected function ExecuteSqlQuery($lQuery){
		if(!$this->m_con->Execute($lQuery)){
			$this->setSqlError($this->m_con->GetLastError());
		}
	}

	/**
	 * Сигнализираме за sql грешка.
	 *
	 * За целта първо сетваме грешка. След това ролбекваме и хвърляме exception,
	 * за да може да го обработим на 1 място
	 * @param string $lErrorMsg - съобщението за грешката
	 */
	protected function SetSqlError($lErrorMsg){
		$this->SetError($lErrorMsg);
		$this->m_con->Execute('ROLLBACK TRANSACTION;');
		throw new Exception(getstr('pwt.sqlError'));
	}


	function SetError($pErrorMsg, $pErrorDelimiter = "\n"){
		if($this->m_errorCount){
			$this->m_errorMsg .= $pErrorDelimiter;
		}
		$this->m_errorCount++;
		$this->m_errorMsg .= $pErrorMsg;
	}
}