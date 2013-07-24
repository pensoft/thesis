DROP TYPE ret_spImportOldFigures CASCADE;
CREATE TYPE ret_spImportOldFigures AS (
	result int
);

CREATE OR REPLACE FUNCTION spImportOldFigures(
	pDocumentId int
)
  RETURNS ret_spImportOldFigures AS
$BODY$
DECLARE
	lRes ret_spImportOldFigures;
	
	lFigureObjectId bigint = 221;
	lFigureWrapperObjectId bigint = 236;
	lPlatePartObjectIds bigint[] = ARRAY[225, 226, 227, 228, 229, 230];
	lWrapperInstanceId bigint;
	lPlatePartInstanceId bigint;
	
	lFigureId bigint;
	lFigureRealObjectInstanceId bigint;-- The instance id of the real subobject(plate/image/video)
	lFigureRecord record;
	lPlateDetailsRecord record;
	lCitationsRecord record;
	lRecord record;
	lUid int;
		
	lCaptionFieldId bigint = 482;
	lPlateTypeFieldId bigint = 485;
	lPlatePartDescriptionFieldId bigint = 487;
	lPlatePicIdFieldId bigint= 484;
	
	lVideoLinkFieldId bigint = 486;
	lImagePicIdFieldId bigint= 483;
	
	lFigImageType int = 1;
	lFigPlateType int = 2;
	lFigVideoType int = 3;
	
	lFigCitationType int = 1;
	lCitationOldObjectIds bigint[];
	lCitationNewObjectIds bigint[];
	
	lFigType int;
	lFigIsPlate boolean;
	lFigCaption text;
BEGIN
	SELECT INTO lWrapperInstanceId 
		id
	FROM pwt.document_object_instances 
	WHERE document_id = pDocumentId AND object_id = lFigureWrapperObjectId;
	
	IF lWrapperInstanceId IS NULL THEN
		RAISE EXCEPTION  'pwt.noFigureWrapperForTheSelectedDocument';
	END IF;
	
	CREATE TEMP TABLE figures_import (
		old_id bigint,
		new_id bigint,
		fig_is_plate boolean DEFAULT false,
		object_is_plate_part boolean DEFAULT false,
		plate_new_id bigint
	);
	
	CREATE TEMP TABLE citations_import(
		citation_id bigint,
		old_object_ids bigint[],
		new_object_ids bigint[]
	);
	
	INSERT INTO citations_import(citation_id, old_object_ids)
		SELECT id, object_ids
	FROM pwt.citations
	WHERE document_id = pDocumentId AND citation_type = lFigCitationType;
	
	<<lFigureLoop>>
	FOR lFigureRecord IN
		(SELECT
					m.id as photo_id,
					m.document_id,
					m.plate_id,
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
					m.ftype as ftype,
					m.link as link,
					m.usr_id
				FROM pwt.media m
				WHERE m.plate_id IS NULL AND m.document_id = pDocumentId AND m.ftype IN (0,2)
			UNION
				SELECT
					null as photo_id,
					max(m.document_id) as document_id,
					m.plate_id,
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
					null as ftype,
					null as link,
					max(p.usr_id) as usr_id
				FROM pwt.media m
				JOIN pwt.plates p ON p.id = m.plate_id
				WHERE m.document_id = pDocumentId AND m.ftype IN (0,2)
				GROUP BY m.plate_id
		)
		ORDER BY move_position
	LOOP 
		lUid = lFigureRecord.usr_id;
		SELECT INTO lFigureId 
			new_instance_id
		FROM spCreateNewInstance(lWrapperInstanceId, lFigureObjectId, lUid);
				
		PERFORM pwt.spMarkInstanceAsUnconfirmed(lFigureId, lUid);
		PERFORM pwt.spMarkInstanceAsConfirmed(lFigureId, lUid);
		
		lFigType = lFigPlateType;
		lFigCaption = lFigureRecord.plate_desc;
		lFigIsPlate = true;
		
		IF lFigureRecord.plate_id IS NULL THEN
			lFigIsPlate = false;
			IF lFigureRecord.ftype = 0 THEN
				lFigType = lFigImageType;
			ELSE 
				lFigType = lFigVideoType;
			END IF;
			lFigCaption = lFigureRecord.photo_desc;
		END IF;
		
		INSERT INTO figures_import(old_id, new_id, fig_is_plate) 
			VALUES (lFigureRecord.photo_id, lFigureId, lFigIsPlate);
			
		
		PERFORM spChangeFigureType(lFigureId, lFigType, lUid);		
		
		SELECT INTO lFigureRealObjectInstanceId 
			id 
		FROM pwt.document_object_instances
		WHERE parent_id = lFigureId;
		
		--Update the fig caption
		UPDATE pwt.instance_field_values SET
			value_str = lFigCaption
		WHERE instance_id = lFigureRealObjectInstanceId AND field_id = lCaptionFieldId;
				
		IF lFigType = lFigImageType THEN -- Image
			UPDATE pwt.instance_field_values SET
				value_int = lFigureRecord.photo_id
			WHERE instance_id = lFigureRealObjectInstanceId AND field_id = lImagePicIdFieldId;
			
		ELSEIF lFigType = lFigVideoType THEN -- Video
			UPDATE pwt.instance_field_values SET
				value_str = lFigureRecord.link
			WHERE instance_id = lFigureRealObjectInstanceId AND field_id = lVideoLinkFieldId;
		
		ELSE -- Plate 			
			--RAISE NOTICE 'Plate str %, %', lFigureRealObjectInstanceId, lFigureRecord.format_type;
			PERFORM spCreatePlateDetails(lFigureRealObjectInstanceId, lFigureRecord.format_type, lUid);	
						
			-- Update the plate details
			<<lPlateDetailsLoop>>
			FOR lPlateDetailsRecord IN
				SELECT
					m.id as photo_id,
					m.description as photo_desc,
					m.position,
					m.ftype as ftype,
					m.link as link
				FROM pwt.media m
				WHERE m.plate_id = lFigureRecord.plate_id AND m.document_id = pDocumentId AND m.ftype IN (0,2)
				ORDER BY m.position ASC
			LOOP 
				SELECT INTO lPlatePartInstanceId 
					i.id
				FROM pwt.document_object_instances i
				JOIN pwt.document_object_instances p ON p.id = lFigureRealObjectInstanceId AND p.pos = substring(i.pos, 1, char_length(p.pos))
				WHERE i.document_id = pDocumentId AND i.object_id = ANY (lPlatePartObjectIds) AND spGetPlatePartNumber(i.id) = lPlateDetailsRecord.position;
				
				IF lPlatePartInstanceId IS NOT NULL THEN
					INSERT INTO figures_import(old_id, new_id, object_is_plate_part, plate_new_id) 
						VALUES (lPlateDetailsRecord.photo_id, lPlatePartInstanceId, true, lFigureId);
					
					UPDATE pwt.instance_field_values SET
						value_str = lPlateDetailsRecord.photo_desc
					WHERE instance_id = lPlatePartInstanceId AND field_id = lPlatePartDescriptionFieldId;
					
					UPDATE pwt.instance_field_values SET
						value_int = lPlateDetailsRecord.photo_id
					WHERE instance_id = lPlatePartInstanceId AND field_id = lPlatePicIdFieldId;
				END IF;
				
			END LOOP lPlateDetailsLoop;
		END IF;
	
	END LOOP lFigureLoop;
		
	FOR lCitationsRecord IN
		SELECT *
		FROM citations_import
	LOOP
		SELECT INTO lCitationNewObjectIds
			array_agg(new_id)
		FROM figures_import 
		WHERE old_id = ANY(lCitationsRecord.old_object_ids) AND fig_is_plate = false;
		
		<<lCitatedPlatesLoop>>
		FOR lRecord IN 
			SELECT DISTINCT plate_new_id
			FROM figures_import 
			WHERE old_id = ANY(lCitationsRecord.old_object_ids) AND fig_is_plate = false AND object_is_plate_part = true 
		LOOP
			lCitationNewObjectIds = array_append(lCitationNewObjectIds, lRecord.plate_new_id);
		END LOOP lCitatedPlatesLoop;
		
		UPDATE citations_import SET
			new_object_ids = lCitationNewObjectIds
		WHERE citation_id = lCitationsRecord.citation_id;		
	END LOOP;
	
	UPDATE pwt.citations c SET
		object_ids = t.new_object_ids,
		is_dirty = true
	FROM citations_import t
	WHERE t.citation_id = c.id;
	
	UPDATE pwt.document_object_instances i SET
		is_new = false
	FROM pwt.document_object_instances p
	WHERE p.id = lWrapperInstanceId AND p.document_id = i.document_id AND p.pos = substring(i.pos, 1, char_length(p.pos));  
	
	DROP TABLE figures_import;
	DROP TABLE citations_import;
	
	lRes.result = 1;
	RETURN lRes;
END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spImportOldFigures(
	pDocumentId int
) TO iusrpmt;
