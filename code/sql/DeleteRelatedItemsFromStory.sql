
CREATE FUNCTION DeleteRelatedItemsFromStory(pguid int)
  RETURNS int AS
$BODY$
BEGIN
	DELETE FROM storyproperties WHERE guid = pGuid AND propid IN (12,13);
	
	RETURN 1;
END;
$BODY$
    LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION DeleteRelatedItemsFromStory(pguid int) TO iusrpmt;

REVOKE ALL ON FUNCTION DeleteRelatedItemsFromStory(pguid int) FROM public;
