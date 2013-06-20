CREATE OR REPLACE FUNCTION spStripTags(
	pText text
)
  RETURNS TEXT AS
$BODY$
	
	BEGIN
		RETURN regexp_replace(regexp_replace(pText, E'(?x)<[^>]*?(\s alt \s* = \s* ([\'"]) ([^>]*?) \2) [^>]*? >', E'\3'), E'(?x)(< [^>]*? >)', '', 'g');
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spStripTags(	
	pText text
) TO iusrpmt;
