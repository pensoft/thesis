CREATE OR REPLACE FUNCTION pjs_id(pwt_doc_id bigint)
  RETURNS bigint AS
'select document_id from pjs.pwt_documents where pwt_id = pwt_doc_id'
  LANGUAGE sql VOLATILE
  COST 100;

  CREATE OR REPLACE FUNCTION pwt_id(pjs_id bigint)
  RETURNS bigint AS
'select pwt_id from pjs.pwt_documents where document_id = pjs_id'
  LANGUAGE sql VOLATILE
  COST 100;