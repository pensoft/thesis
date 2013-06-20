DROP FUNCTION pwt.spUpdateDocumentSubmittingAuthor(
	pDocumentId bigint
);

CREATE OR REPLACE FUNCTION pwt.spUpdateDocumentSubmittingAuthor(
	pDocumentId bigint
)
  RETURNS int AS
$BODY$
		DECLARE
			lRes ret_spCreateDocument;		
			lAuthorObjectId bigint;
			lSubmittingAuthorUid int;
			lAuthorIdFieldId bigint;
			lSubmittingAuthorFieldId bigint;
		BEGIN	
			lAuthorObjectId = 8;
			lAuthorIdFieldId = 13;
			lSubmittingAuthorFieldId = 248;
			
			SELECT INTO lSubmittingAuthorUid iv.value_int
			FROM pwt.instance_field_values iv
			JOIN pwt.document_object_instances i ON i.id = iv.instance_id
			JOIN pwt.instance_field_values iv1 ON iv1.instance_id = i.id AND iv1.field_id = lSubmittingAuthorFieldId AND iv1.value_int = 1
			WHERE i.document_id = pDocumentId AND i.object_id = lAuthorObjectId AND iv.field_id = lAuthorIdFieldId;
			
			
			UPDATE pwt.documents SET
				createuid = lSubmittingAuthorUid
			WHERE id = pDocumentId;
			RETURN 1;
		END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
