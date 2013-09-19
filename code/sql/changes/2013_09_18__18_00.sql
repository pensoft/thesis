ALTER TABLE journals ADD COLUMN url_name varchar;
UPDATE journals SET
	url_name =  replace(lower(name), ' ', '_'); 