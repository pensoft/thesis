DROP FUNCTION pwt.spMarkInstanceAsUnconfirmed(
	pInstanceId bigint,
	pUid int
);

CREATE OR REPLACE FUNCTION pwt.spMarkInstanceAsUnconfirmed(
	pInstanceId bigint,
	pUid int
)
  RETURNS int AS
$BODY$
		DECLARE			
		BEGIN	
			-- Mark the instance and all subinstances down the tree.
			UPDATE pwt.document_object_instances i SET
				is_confirmed = false
			FROM pwt.document_object_instances p
			WHERE p.document_id = i.document_id AND p.id = pInstanceId AND substring(i.pos, 1, char_length(p.pos)) = p.pos;
			
			RETURN 1;
		END
	$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;

GRANT EXECUTE ON FUNCTION pwt.spMarkInstanceAsUnconfirmed(
	pInstanceId bigint,
	pUid int
) TO iusrpmt;