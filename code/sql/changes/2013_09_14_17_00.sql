ALTER TABLE pjs.taxon_sites ADD COLUMN add_link_prefix boolean DEFAULT true;
ALTER TABLE pjs.taxon_sites ADD COLUMN add_link_prefix_no_result boolean DEFAULT true;
ALTER TABLE pjs.taxon_sites ADD COLUMN link_to_search_for_results varchar;

UPDATE pjs.taxon_sites SET
	link_to_search_for_results = 'http://www.morphbank.net/MyManager/image.php?id=imageTab&keywords={encoded_taxon_name}'
WHERE id = 26;

UPDATE pjs.taxon_sites SET
	link_to_search_for_results = 'http://www.europe-aliens.org/autoComplete?action=complete&id={encoded_taxon_name}'
WHERE id = 36;

UPDATE pjs.taxon_sites SET
	link_to_search_for_results = 'http://www.google.com/cse?cx=004971884014326696348:lwck86z8tsg&ie=UTF-8&cof=FORID:10&q={encoded_taxon_name}&sa=GO&nojs=1'
WHERE id = 37;


UPDATE pjs.taxon_sites SET
	link_to_search_for_results = 'http://www.google.com/custom?ie=UTF-8&oe=UTF-8&cof=S%3Ahttp%3A%2F%2Fwww.conifers.org%2F%3BAH%3Acenter%3BL%3Ahttp%3A%2F%2Fwww.conifers.org%2Fzz%2Fgymn2.gif%3B&domains=conifers.org&sitesearch=conifers.org&&sitesearch=http%3A%2F%2Fwww.conifers.org&sa=Search+this+site&hl=en&q={encoded_taxon_name}'
WHERE id = 16;


UPDATE pjs.taxon_sites SET
	taxon_link = 'http://www.catalogueoflife.org/col/search/all/key/{encoded_taxon_name}/match/1',
	taxon_link_no_results = 'http://www.catalogueoflife.org/col/search/all/key/{encoded_taxon_name}/match/1'
WHERE id = 3;

UPDATE pjs.taxon_sites SET
	add_link_prefix = false,
	add_link_prefix_no_result = false
WHERE id IN (42, 30);

DELETE FROM pjs.taxon_sites_results WHERE site_id IN (17, 30, 31, 41, 26, 7, 37, 16, 3);


CREATE TABLE pjs.taxon_catalogue_of_life_data
(
  id bigserial PRIMARY KEY,
  taxon_id bigint REFERENCES pjs.taxons (id) NOT NULL,
  col_taxon_id varchar,
  url varchar,
  lastmoddate timestamp without time zone DEFAULT now()
)
WITH (
  OIDS=FALSE
);
GRANT ALL ON TABLE pjs.taxon_catalogue_of_life_data TO iusrpmt;

/*
	Modified SPs
	pjs.spSaveTaxonCatalogueOfLifeBaseData
*/