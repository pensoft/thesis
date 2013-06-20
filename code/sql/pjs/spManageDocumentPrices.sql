DROP TYPE IF EXISTS ret_managedocumentprices CASCADE;

CREATE TYPE ret_managedocumentprices AS (
	startpage int,
	endpage varchar,
	colorpage int,
	price numeric,
	issue_id int
);

-- Function: pjs.spUpdateDocumentPrices(integer, integer, integer, integer, numeric)

-- DROP FUNCTION pjs.spUpdateDocumentPrices(integer, integer, integer, integer, numeric);

CREATE OR REPLACE FUNCTION pjs.spManageDocumentPrices(
	pOper integer,
	pDocumentId integer,
	pStartPage integer, 
	pEndPage integer, 
	pColorPages integer,
	pPrice numeric,
	pJournalId int,
	pIssueId int
)
  RETURNS ret_managedocumentprices AS
$BODY$
DECLARE
	lRes ret_managedocumentprices;
	lPagesNum int;
BEGIN
	IF pDocumentId IS NOT NULL THEN
		IF pOper = 1 THEN -- GET
			SELECT INTO lRes.startpage, lRes.endpage, lRes.colorpage, lRes.price, lRes.issue_id start_page, end_page, number_of_color_pages, price, issue_id FROM pjs.documents WHERE id = pDocumentId;
		ELSEIF pOper = 2 THEN -- UPDATE
			UPDATE pjs.documents SET start_page = pStartPage, end_page = pEndPage, number_of_color_pages = pColorPages, price = pPrice, issue_id = pIssueId WHERE id = pDocumentId;
		END IF;
	END IF;
	IF pOper = 3 THEN -- AUTOMATIC PRICE
		lPagesNum = pEndPage - pStartPage;
		SELECT INTO lRes.price price FROM pjs.journal_prices WHERE range_end > lPagesNum AND journal_id = pJournalId LIMIT 1;
	END IF;

	RETURN lRes;
END;
$BODY$
  LANGUAGE plpgsql VOLATILE SECURITY DEFINER
  COST 100;
ALTER FUNCTION pjs.spManageDocumentPrices(integer, integer, integer, integer, integer, numeric, integer, integer) OWNER TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spManageDocumentPrices(integer, integer, integer, integer, integer, numeric, integer, integer) TO postgres;
GRANT EXECUTE ON FUNCTION pjs.spManageDocumentPrices(integer, integer, integer, integer, integer, numeric, integer, integer) TO iusrpmt;
GRANT EXECUTE ON FUNCTION pjs.spManageDocumentPrices(integer, integer, integer, integer, integer, numeric, integer, integer) TO pensoft;
