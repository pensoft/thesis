--
-- Table: store_orders_states
--

CREATE TABLE store_orders_states
(
  id serial NOT NULL,
  "name" character varying NOT NULL,
  CONSTRAINT store_orders_states_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
ALTER TABLE store_orders_states OWNER TO postgres84;
GRANT ALL ON TABLE store_orders_states TO postgres84;
GRANT ALL ON TABLE store_orders_states TO iusrpmt;

INSERT INTO store_orders_states(id, "name") VALUES (3, 'Платена');
INSERT INTO store_orders_states(id, "name") VALUES (4, 'Отказана');
INSERT INTO store_orders_states(id, "name") VALUES (10, 'Нова');
ALTER SEQUENCE store_orders_states_id_seq START 11;

--
-- Table: pays
--

CREATE TABLE pays
(
  payid serial NOT NULL,
  state integer,
  paytype integer,
  createdate timestamp without time zone,
  paydate timestamp without time zone,
  price numeric(10,2),
  descr character varying,
  CONSTRAINT pays_pkey PRIMARY KEY (payid),
  CONSTRAINT pays_state_fkey FOREIGN KEY (state)
      REFERENCES store_orders_states (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=TRUE);
ALTER TABLE pays OWNER TO postgres84;
GRANT ALL ON TABLE pays TO postgres84;
GRANT ALL ON TABLE pays TO iusrpmt;

--
-- Table: store_orders
--

CREATE TABLE store_orders
(
  id serial NOT NULL,
  createdate timestamp(0) without time zone NOT NULL,
  description character varying,
  confirmedbymail integer,
  confirmedbyphone integer,
  employee character varying,
  dateofcall timestamp without time zone,
  uid integer NOT NULL,
  recipient_name character varying,
  recipient_postcode character varying(10),
  delivery_city integer,
  recipient_address character varying,
  recipient_email character varying,
  recipient_phone character varying,
  invoice_firmname character varying,
  invoice_mol character varying,
  invoice_bulstat character varying(13),
  invoice_address character varying,
  invoice_num character varying(20),
  invoice_delivery integer,
  invoice_recipient_city integer,
  invoice_recipient_address character varying,
  payid integer,
  paid integer,
  paymenttodealers numeric(10,2),
  carrier integer,
  giventocarrier timestamp without time zone,
  shipmentnum integer,
  dateaccepted timestamp without time zone,
  paymenttocarrier numeric(10,2),
  expenses numeric(10,2),
  additionalexpenses numeric(10,2),
  profit numeric(10,2),
  total numeric(10,2),
  ip_addr inet,
  delivery integer,
  delivery_recipient_name character varying,
  delivery_recipient_postcode character varying(10),
  delivery_recipient_city integer,
  delivery_recipient_address character varying,
  delivery_recipient_email character varying,
  delivery_recipient_phone character varying,
  delivery_specific_date date,
  delivery_card_text character varying,
  remarks character varying,
  confirmtext character varying,
  invoice integer,
  recipient_city_name character varying,
  delivery_price numeric(10,2),
  CONSTRAINT store_orders_pkey PRIMARY KEY (id),
  CONSTRAINT store_orders_payid_fkey FOREIGN KEY (payid)
      REFERENCES pays (payid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);

ALTER TABLE store_orders OWNER TO postgres84;
GRANT ALL ON TABLE store_orders TO postgres84;
GRANT ALL ON TABLE store_orders TO iusrpmt;

--
-- Table: store_products_color
--

CREATE TABLE store_products_color
(
  id serial NOT NULL,
  "name" character varying NOT NULL,
  picid integer,
  CONSTRAINT store_products_color_pkey PRIMARY KEY (id),
  CONSTRAINT store_products_color_picid_fkey FOREIGN KEY (picid)
      REFERENCES photos (guid) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE store_products_color OWNER TO postgres84;
GRANT ALL ON TABLE store_products_color TO postgres84;
GRANT ALL ON TABLE store_products_color TO iusrpmt;

--
-- Table: store_products_manufacturer
--

CREATE TABLE store_products_manufacturer
(
  id serial NOT NULL,
  "name" character varying NOT NULL,
  state integer,
  pos integer,
  CONSTRAINT store_products_manufacturer_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
ALTER TABLE store_products_manufacturer OWNER TO postgres84;
GRANT ALL ON TABLE store_products_manufacturer TO postgres84;
GRANT ALL ON TABLE store_products_manufacturer TO iusrpmt;

--
-- Table: store_products_measure_unit
--

CREATE TABLE store_products_measure_unit
(
  id serial NOT NULL,
  "name" character varying NOT NULL,
  CONSTRAINT store_products_measure_unit_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
ALTER TABLE store_products_measure_unit OWNER TO postgres84;
GRANT ALL ON TABLE store_products_measure_unit TO postgres84;
GRANT ALL ON TABLE store_products_measure_unit TO iusrpmt;

--
-- Table: store_products_supplier
--

CREATE TABLE store_products_supplier
(
  id serial NOT NULL,
  "name" character varying NOT NULL,
  CONSTRAINT store_products_supplier_pkey PRIMARY KEY (id)
)
WITH (OIDS=FALSE);
ALTER TABLE store_products_supplier OWNER TO postgres84;
GRANT ALL ON TABLE store_products_supplier TO postgres84;
GRANT ALL ON TABLE store_products_supplier TO iusrpmt;

--
-- Table: store_products
--

CREATE TABLE store_products
(
  id integer NOT NULL DEFAULT nextval('stories_guid_seq'::regclass),
  store_products_cat_id integer,
  "name" character varying NOT NULL,
  small_description character varying,
  manufacturer integer,
  measureunit integer,
  supplier integer,
  promo integer,
  propertytype integer,
  createdate timestamp(0) without time zone NOT NULL,
  ord integer,
  previewpicid integer,
  newproduct integer,
  color integer,
  pubdate timestamp without time zone,
  big_description character varying,
  state integer,
  CONSTRAINT store_products_pkey PRIMARY KEY (id),
  CONSTRAINT store_products_manufacturer_fkey FOREIGN KEY (manufacturer)
      REFERENCES store_products_manufacturer (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT store_products_measureunit_fkey FOREIGN KEY (measureunit)
      REFERENCES store_products_measure_unit (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT store_products_supplier_fkey FOREIGN KEY (supplier)
      REFERENCES store_products_supplier (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
WITH (OIDS=FALSE);
ALTER TABLE store_products OWNER TO postgres84;
GRANT ALL ON TABLE store_products TO postgres84;
GRANT ALL ON TABLE store_products TO iusrpmt;

--
-- Table: store_products_det
--

CREATE TABLE store_products_det
(
  id serial NOT NULL,
  productid integer NOT NULL,
  price numeric(10,2) NOT NULL,
  promoprice numeric(10,2),
  deliveryprice numeric(10,2),
  state integer NOT NULL,
  available integer,
  code character varying,
  colorid integer,
  description character varying,
  CONSTRAINT store_products_det_pkey PRIMARY KEY (id),
  CONSTRAINT store_products_det_colorid_fkey FOREIGN KEY (colorid)
      REFERENCES store_products_color (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT store_products_det_productid_fkey FOREIGN KEY (productid)
      REFERENCES store_products (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (OIDS=FALSE);
ALTER TABLE store_products_det OWNER TO postgres84;
GRANT ALL ON TABLE store_products_det TO postgres84;
GRANT ALL ON TABLE store_products_det TO iusrpmt;

-- Index: fki_1

CREATE INDEX fki_1
  ON store_products_det
  USING btree
  (colorid);

-- Table: store_orders_det

CREATE TABLE store_orders_det
(
  id serial NOT NULL,
  store_orders_id integer,
  store_products_id integer,
  qty integer NOT NULL,
  price numeric(10,2),
  createdate timestamp without time zone,
  paymenttodealer numeric(10,2),
  paidtodealer integer,
  paymenttodealerdate timestamp without time zone,
  paymenttodealerdocument integer,
  deliveredtooffice integer,
  officedeliverydate timestamp without time zone,
  store_products_det_id integer,
  CONSTRAINT store_orders_det_pkey PRIMARY KEY (id),
  CONSTRAINT store_orders_det_store_orders_id_fkey FOREIGN KEY (store_orders_id)
      REFERENCES store_orders (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT store_orders_det_store_products_det_id_fkey FOREIGN KEY (store_products_det_id)
      REFERENCES store_products_det (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT store_orders_det_store_products_id_fkey FOREIGN KEY (store_products_id)
      REFERENCES store_products (id) MATCH SIMPLE
      ON UPDATE CASCADE ON DELETE CASCADE
)
WITH (OIDS=FALSE);
ALTER TABLE store_orders_det OWNER TO postgres84;
GRANT ALL ON TABLE store_orders_det TO postgres84;
GRANT ALL ON TABLE store_orders_det TO iusrpmt;

--
-- storeorderscalculatesums(pId int4)
--

CREATE OR REPLACE FUNCTION storeorderscalculatesums(pId int4)
  RETURNS int4 AS
$$
DECLARE
	lSum NUMERIC(10,2);--Sumata nad koqto dostavkata e bezplatna
BEGIN
	SELECT INTO lSum so.delivery_price 
		FROM store_orders so
	WHERE so.id = pId;
	
	UPDATE store_orders sor
		SET 
			total = coalesce(t1.price,0) + coalesce(lSum, 0)
	FROM store_orders so
	LEFT JOIN (
		SELECT store_orders_id, sum((price)) AS price
		FROM store_orders_det 
		WHERE store_orders_id = pId GROUP BY store_orders_id
	) t1 ON so.id = t1.store_orders_id
	WHERE so.id = pId AND sor.id = pId;
	
	UPDATE pays p SET
		price = so.total
	FROM 
		store_orders so
	WHERE 
		so.id = pId AND p.payid = so.payid;
 RETURN 1;
 END;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
  
ALTER FUNCTION storeorderscalculatesums(pId int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION storeorderscalculatesums(pId int4) TO postgres84;
GRANT EXECUTE ON FUNCTION storeorderscalculatesums(pId int4) TO iusrpmt;

--
-- Function: store_orders_det_upd()
--

CREATE OR REPLACE FUNCTION store_orders_det_upd()
  RETURNS "trigger" AS
$$
DECLARE
	lstore_orders_id int;
BEGIN

	IF (TG_OP = 'INSERT') THEN
		UPDATE store_orders_det sod
			SET 
			price = (case when sp.promo=1 then spd.promoprice else spd.price end) * sod.qty,
			paymenttodealer = spd.deliveryprice * sod.qty
			FROM store_products sp
			JOIN store_products_det spd ON spd.id = NEW.store_products_det_id
			WHERE sp.id = sod.store_products_id
			AND sod.id = NEW.id;
			
			lstore_orders_id = new.store_orders_id;
	END IF;

	IF (TG_OP = 'DELETE') THEN
		lstore_orders_id = old.store_orders_id;
	END IF;
	PERFORM storeorderscalculatesums(lstore_orders_id);
	RETURN NULL;

END;
$$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION store_orders_det_upd() OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION store_orders_det_upd() TO postgres84;
GRANT EXECUTE ON FUNCTION store_orders_det_upd() TO public;
GRANT EXECUTE ON FUNCTION store_orders_det_upd() TO iusrpmt;

-- Index: fki_

CREATE INDEX fki_
  ON store_orders_det
  USING btree
  (store_products_det_id);

-- Index: fki_11

CREATE INDEX fki_11
  ON store_orders_det
  USING btree
  (store_orders_id);

-- Index: fki_2

CREATE INDEX fki_2
  ON store_orders_det
  USING btree
  (store_products_id);


-- Trigger: store_orders_det_upd on store_orders_det

CREATE TRIGGER store_orders_det_upd
  AFTER INSERT OR DELETE
  ON store_orders_det
  FOR EACH ROW
  EXECUTE PROCEDURE store_orders_det_upd();

--
-- MakeOrder
--

CREATE TYPE retMakeOrder AS (
	id int4,
	recipient_name varchar,
	recipient_address varchar,
	recipient_phone varchar
);

CREATE OR REPLACE FUNCTION MakeOrder(
	pOp int4, 
	pID int4, 
	pName varchar, 
	pAddress varchar, 
	pPhone varchar, 
	pIPaddr inet, 
	pProdID int[], 
	pProdDetID int[], 
	pQty int[],
	pDelivery_price numeric(10,2),
	pCity_Name varchar
)
  RETURNS retMakeOrder AS
$$

DECLARE
	lRes retMakeOrder;
	lDelPrice NUMERIC(10,2);
BEGIN
	 IF (pOp = 0) THEN --GET
		 SELECT INTO lRes id, recipient_name, recipient_address, recipient_phone
			 FROM store_orders
			 WHERE id = pID;
	ELSEIF (pOp = 1) THEN --INSER UPDATE
		IF (NOT EXISTS(SELECT * FROM store_orders WHERE id = pID) AND NOT EXISTS(SELECT * FROM store_orders_det WHERE store_orders_id = pID)) THEN --INSERT
			INSERT INTO pays
				(
					state,
					createdate,
					descr
				)
				VALUES(
					10,
					CURRENT_TIMESTAMP,
					'Поръчка на rumpel от Rumpel.bg'
			);
			
			INSERT INTO store_orders
			(
				createdate, 
				uid, 
				payid, 
				recipient_name, 
				delivery_price, 
				recipient_address, 
				recipient_phone, 
				invoice,  
				ip_addr,
				recipient_city_name
			)
			VALUES 
			(
				CURRENT_TIMESTAMP, 
				1, 
				currval('pays_payid_seq'),  
				pName,
				pDelivery_price, 
				pAddress,
				pPhone, 
				0,
				pIPaddr,
				pCity_Name
			);
			
			lRes.id = currval('store_orders_id_seq');
			
			FOR i IN array_lower(pProdID, 1) .. array_upper(pQty, 1) LOOP
				INSERT INTO store_orders_det 
					(
						store_orders_id, 
						store_products_id, 
						store_products_det_id, 
						qty
					) VALUES 
					(
						lRes.id, 
						pProdID[i], 
						pProdDetID[i], 
						pQty[i]
					);
			END LOOP;
			
			PERFORM storeorderscalculatesums(lRes.id);
		ELSE --UPDATE
			DELETE FROM store_orders_det WHERE store_orders_id = pID;
			
			FOR i IN array_lower(pProdID, 1) .. array_upper(pQty, 1)  LOOP
				INSERT INTO store_orders_det (store_orders_id, store_products_id, store_products_det_id, qty) VALUES (pID, pProdID[i], pProdDetID[i], pQty[i]);
			END LOOP;
			
			UPDATE store_orders
			SET createdate = CURRENT_TIMESTAMP,
				recipient_name = pName,
				delivery_price = pDelivery_price,
				recipient_address = pAddress,
				recipient_phone = pPhone,
				ip_addr = pIPaddr,
				recipient_city_name = pCity_Name
			WHERE id = pID;
			PERFORM storeorderscalculatesums(pID);
		END IF;
	END IF;
	RETURN lRes;
END
$$
  LANGUAGE 'plpgsql' SECURITY DEFINER;
ALTER FUNCTION MakeOrder(
	pOp int4, 
	pID int4, 
	pName varchar, 
	pAddress varchar, 
	pPhone varchar, 
	pIPaddr inet, 
	pProdID int[], 
	pProdDetID int[], 
	pQty int[], 
	pDelivery_price numeric(10,2), 
	pCity_Name varchar
) OWNER TO postgres84;

REVOKE ALL ON FUNCTION MakeOrder(
	pOp int4, 
	pID int4, 
	pName varchar, 
	pAddress varchar, 
	pPhone varchar, 
	pIPaddr inet, 
	pProdID int[], 
	pProdDetID int[], 
	pQty int[], 
	pDelivery_price numeric(10,2), 
	pCity_Name varchar
) FROM public;

GRANT EXECUTE ON FUNCTION MakeOrder(
	pOp int4, 
	pID int4, 
	pName varchar, 
	pAddress varchar, 
	pPhone varchar, 
	pIPaddr inet, 
	pProdID int[], 
	pProdDetID int[], 
	pQty int[], 
	pDelivery_price numeric(10,2), 
	pCity_Name varchar
) TO iusrpmt;

--
-- Administration Functions (adm)
--

--
-- addphototostory
--

DROP FUNCTION addphototostory(
	pphotoid int, 
	pstoryid int, 
	pplace int, 
	ptxt varchar, 
	pfirst int, 
	psid int, 
	ppos int
);

CREATE FUNCTION addphototostory(
	pphotoid int, 
	pstoryid int, 
	pplace int, 
	ptxt varchar, 
	pfirst int, 
	psid int, 
	ppos int
) RETURNS int AS 
$$
DECLARE
	lPos int;
	rr RECORD;
BEGIN
	
	IF pplace = 0 AND pfirst IS NULL THEN -- iztrivane na snimka
		SELECT INTO lPos coalesce(valint3, 1) FROM storyproperties WHERE guid = pstoryid 
		AND propid = 2 AND valint = pphotoid;
		
		DELETE FROM storyproperties WHERE guid = pstoryid 
		AND propid = 2 AND valint = pphotoid;
		
		FOR rr IN SELECT valint FROM storyproperties 
			WHERE guid = pstoryid AND propid = 2 AND (valint3 > lPos OR valint3 IS NULL)
			ORDER BY valint3
		LOOP
			UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
			lPos := lPos + 1;
		END LOOP;
		
		IF EXISTS(SELECT guid FROM stories WHERE guid = pstoryid AND previewpicid = pphotoid) THEN
			UPDATE stories SET previewpicid = NULL WHERE guid = pstoryid; 
		END IF;
		
		IF EXISTS(SELECT id FROM store_products WHERE id = pstoryid AND previewpicid = pphotoid) THEN
			UPDATE store_products SET previewpicid = NULL WHERE id = pstoryid; 
		END IF;
		
	ELSE
		
		IF ppos IS NULL THEN
			SELECT INTO lPos coalesce((max(valint3) + 1), 1) FROM storyproperties WHERE guid = pstoryid AND propid = 2;
		ELSE 
			lPos := ppos + 1;
			FOR rr IN SELECT valint FROM storyproperties 
				WHERE guid = pstoryid AND propid = 2 AND (valint3 >= ppos OR valint3 IS NULL) AND valint <> pphotoid
				ORDER BY valint3
			LOOP
				UPDATE storyproperties SET valint3 = lPos WHERE valint = rr.valint;
				lPos := lPos + 1;
			END LOOP;
			
			lPos := ppos;
		END IF;
		
		DELETE FROM storyproperties WHERE guid = pstoryid 
		AND propid = 2 AND valint = pphotoid;
		
		INSERT INTO storyproperties (guid, propid, valint, valint2, valstr, valint3) 
		VALUES (pstoryid, 2, pphotoid, pplace, ptxt, lPos);
		
		IF pfirst IS NOT NULL THEN
			IF EXISTS (SELECT * FROM store_products where id = pStoryID) THEN
				UPDATE store_products SET previewpicid =  pphotoid WHERE id = pStoryID;
			ELSE 
				UPDATE stories SET previewpicid =  pphotoid WHERE guid = pStoryID;
			END IF;
		ELSE
			IF EXISTS(SELECT guid FROM stories WHERE guid = pstoryid AND previewpicid = pphotoid) THEN
				UPDATE stories SET previewpicid = NULL WHERE guid = pstoryid;
			END IF;
			
			IF EXISTS(SELECT id FROM store_products WHERE id = pstoryid AND previewpicid = pphotoid) THEN
				UPDATE store_products SET previewpicid = NULL WHERE id = pstoryid;
			END IF;
			
		END IF;
		
	END IF;
	
	RETURN 0;
END;
$$ LANGUAGE 'plpgsql' SECURITY DEFINER;

--
-- GetPhotosByProduct
--

CREATE TYPE retPhotosByProduct AS (
	photoid varchar, 
	phototitle varchar, 
	photoauthor varchar, 
	place int, 
	valstr varchar, 
	imgname varchar,
	pos int,
	frst int
);

CREATE OR REPLACE FUNCTION GetPhotosByProduct(pstoryid int, psid int) RETURNS SETOF retPhotosByProduct AS 
$$
	DECLARE
		lResult retPhotosByProduct%ROWTYPE;
		lphotoid int;
		lpropid int;
	BEGIN
		lpropid := 2;
		
		FOR lResult IN
			SELECT sp.valint as picid, p.title, p.author, sum(sp.valint2) as place, max(sp.valstr) as valstr, p.imgname, sp.valint3 as pos, s.id as frst
				FROM storyproperties sp
				INNER JOIN photos p ON (sp.valint = p.guid)
				LEFT JOIN store_products s on s.previewpicid = sp.valint and s.id = pstoryid
				WHERE sp.guid = pstoryid AND sp.propid = lpropid	
				GROUP BY sp.valint, p.title, p.author, p.imgname, sp.valint3,s.id
				ORDER BY pos ASC
		LOOP
			RETURN NEXT lResult;
		END LOOP;		
		
		RETURN;
	END;
$$ LANGUAGE 'plpgsql' SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION GetPhotosByProduct(pstoryid int, psid int) TO iusrpmt;
REVOKE ALL ON FUNCTION GetPhotosByProduct(pstoryid int, psid int) FROM public;

--
-- addproducttostory
--

CREATE OR REPLACE FUNCTION addproducttostory(pstoryid int4, pstoryid1 int4, pproptype int4)
  RETURNS int4 AS
$$
DECLARE
lguid INT;
BEGIN
IF NOT EXISTS(SELECT guid FROM storyproperties WHERE guid = pstoryid AND valint = pstoryid1 AND propid = pproptype) THEN
INSERT INTO storyproperties (guid, propid, valint) VALUES (pstoryid, pproptype, pstoryid1);
END IF;
RETURN 1;
END;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION addproducttostory(pstoryid int4, pstoryid1 int4, pproptype int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION addproducttostory(pstoryid int4, pstoryid1 int4, pproptype int4) TO postgres84;
GRANT EXECUTE ON FUNCTION addproducttostory(pstoryid int4, pstoryid1 int4, pproptype int4) TO iusrpmt;

--
-- concatcoma
--

CREATE OR REPLACE FUNCTION concatcoma(text, text)
  RETURNS text AS
$$
  DECLARE
    t text;
  BEGIN
    IF  character_length($1) > 0 THEN
      t = $1 ||', '|| $2;
    ELSE
      t = $2;
    END IF;
    RETURN t;
  END;
$$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION concatcoma(text, text) OWNER TO postgres84;

CREATE AGGREGATE aggr_concatcoma(
  BASETYPE=text,
  SFUNC=public.concatcoma,
  STYPE=text
);
ALTER AGGREGATE aggr_concat(text) OWNER TO postgres84;

--
-- concat_distinct
--

CREATE OR REPLACE FUNCTION concat_distinct(text, text)
  RETURNS text AS
$$
	DECLARE
	t text;
	lTmpArr1 text[];
	lTmpArr2 text[];
	lArrIter1 int4;
	lArrIter2 int4;
	lArrSize1 int4;
	lArrSize2 int4;
	BEGIN
		IF  character_length($1) > 0  THEN

			lTmpArr1 = string_to_array($1, ';');
			lArrSize1 := array_upper(lTmpArr1, 1);
			lArrIter1 := 1;
			
			t = $2;
			WHILE (lArrIter1 <= lArrSize1) LOOP
				IF t NOT LIKE '%;' || lTmpArr1[lArrIter1] || ';%' AND t NOT LIKE  lTmpArr1[lArrIter1] || ';%' AND t NOT LIKE  '%;' || lTmpArr1[lArrIter1]  AND t NOT LIKE lTmpArr1[lArrIter1] THEN
					t := t ||';'|| lTmpArr1[lArrIter1];
				END IF;
				lArrIter1 := lArrIter1 + 1;
			END LOOP;
		ELSE
			t := $2;
		END IF;
		RETURN t;
	END;
$$
  LANGUAGE 'plpgsql' VOLATILE;
ALTER FUNCTION concat_distinct(text, text) OWNER TO postgres84;

CREATE AGGREGATE aggr_concat_distinct(
  BASETYPE=text,
  SFUNC=concat_distinct,
  STYPE=text
);
ALTER AGGREGATE aggr_concat_distinct(text) OWNER TO postgres84;

--
-- spproductssavedata
--

CREATE TYPE retproducts AS
(
    id int4,
    store_products_cat_id int4,
    name varchar,
    manufacturer int4,
    measureunit int4,
    supplier int4,
    promo int4,
    propertytype int4,
    newproduct int4,
    color int4,
    createdate timestamp(0),
    ord int4,
    rubr varchar,
    rubrnames varchar,
    pubdate timestamp,
	state int,
	small_description text,
	big_description text
);
ALTER TYPE retproducts OWNER TO postgres84;

CREATE OR REPLACE FUNCTION spproductssavedata(
	pop int4, 
	pid int4, 
	pstore_products_cat_id int4, 
	pname "varchar", 
	pmanufacturer int4, 
	pmeasureunit int4, 
	psupplier int4, 
	ppromo int4, 
	ppropertytype int4, 
	pnewproduct int4, 
	pcolor int4, 
	pord int4, 
	prubr "varchar", 
	ppubdate timestamp, 
	p text,
	pstate int,
	pDescription text
)
  RETURNS retproducts AS
$$
DECLARE
	lResult retProducts;
BEGIN

	IF (pOp = 0) THEN
		SELECT INTO lResult 
			id, 
			store_products_cat_id, 
			name, 
			manufacturer, 
			measureunit, 
			supplier, 
			promo, 
			propertytype, 
			newproduct, 
			color, 
			createdate, 
			ord, 
			null, 
			null, 
			pubdate,
			state,
			small_description,
			big_description
		FROM store_products
		WHERE id = pId;
		
		SELECT INTO lResult.rubr, lResult.rubrnames  
			aggr_concatcoma(r.id::text), 
			aggr_concatcoma(r.name[1]) 
		FROM storyproperties sp 
		JOIN rubr r ON r.id = sp.valint 
		WHERE guid = pId AND propid = 1;
	
	ELSIF (pOp = 1) THEN
		IF (pRubr IS NOT NULL AND pstore_products_cat_id IS NULL) THEN
		RAISE EXCEPTION 'Щом сте избрали вторични рубрики трябва да изберете и главна рубрика!';
		END IF;
		IF (pId is null) THEN
			INSERT INTO store_products(
				store_products_cat_id ,
				name  ,
				manufacturer ,
				measureunit ,
				supplier ,
				promo  ,
				propertytype,
				newproduct  ,
				color,
				ord ,
				createdate,
				pubdate,
				state,
				small_description,
				big_description
			)
			VALUES(
				pstore_products_cat_id ,
				pName  ,
				pManufacturer ,
				pMeasureunit ,
				pSupplier ,
				pPromo  ,
				pPropertytype ,
				pNewproduct  ,
				pColor,
				pOrd ,
				now(), 
				coalesce( ppubdate, now()),
				pstate,
				pDescription,
				p
			);
			
			lResult.id = currval('stories_guid_seq');
			PERFORM SaveStoriesRubriki(lResult.id, pRubr, pstore_products_cat_id, 0, 1);
		ELSE
			UPDATE store_products
				SET
				store_products_cat_id = pstore_products_cat_id,
				name  = pName,
				manufacturer = pManufacturer,
				measureunit = pMeasureUnit,
				supplier = pSupplier,
				promo  = pPromo,
				propertytype = ppropertytype,
				newproduct  = pNewProduct,
				color = pColor,
				ord = pOrd,
				pubdate = coalesce( ppubdate, pubdate, now()),
				state = pstate,
				small_description = pDescription,
				big_description = p
			WHERE id = pId;
			lResult.id = pId;
		DELETE FROM storyproperties WHERE guid = pId AND (propid=1 OR propid=4);
		PERFORM SaveStoriesRubriki(pId, pRubr, pstore_products_cat_id, 0, 1);
		END IF;
	ELSIF (pOp = 3) THEN
		DELETE FROM store_products WHERE id = pId;
		DELETE FROM storyproperties WHERE guid = pId;
		DELETE FROM storyproperties WHERE valint = pId and propid = 16;
		DELETE FROM storiesft WHERE guid = pId;
	END IF;
	RETURN lResult;
END ;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

ALTER FUNCTION spproductssavedata(
	pop int4, 
	pid int4, 
	pstore_products_cat_id int4, 
	pname "varchar", 
	pmanufacturer int4, 
	pmeasureunit int4, 
	psupplier int4, 
	ppromo int4, 
	ppropertytype int4, 
	pnewproduct int4, 
	pcolor int4, 
	pord int4, 
	prubr "varchar", 
	ppubdate timestamp,
	p text,
	pstate int,
	pDescription text
) OWNER TO postgres84;

GRANT EXECUTE ON FUNCTION spproductssavedata(
	pop int4, 
	pid int4, 
	pstore_products_cat_id int4, 
	pname "varchar", 
	pmanufacturer int4, 
	pmeasureunit int4, 
	psupplier int4, 
	ppromo int4, 
	ppropertytype int4,
	pnewproduct int4, 
	pcolor int4, 
	pord int4, 
	prubr "varchar", 
	ppubdate timestamp,
	p text,
	pstate int,
	pDescription text
) TO public;

GRANT EXECUTE ON FUNCTION spproductssavedata(
	pop int4, 
	pid int4,
	pstore_products_cat_id int4, 
	pname "varchar", 
	pmanufacturer int4,
	pmeasureunit int4,
	psupplier int4,
	ppromo int4, 
	ppropertytype int4, 
	pnewproduct int4,
	pcolor int4, 
	pord int4, 
	prubr "varchar", 
	ppubdate timestamp,
	p text,
	pstate int,
	pDescription text
) TO postgres84;

GRANT EXECUTE ON FUNCTION spproductssavedata(
	pop int4, 
	pid int4,
	pstore_products_cat_id int4, 
	pname "varchar", 
	pmanufacturer int4,
	pmeasureunit int4,
	psupplier int4, 
	ppromo int4,
	ppropertytype int4,
	pnewproduct int4,
	pcolor int4, 
	pord int4,
	prubr "varchar",
	ppubdate timestamp,
	p text,
	pstate int,
	pDescription text
) TO iusrpmt;

--
-- spproductssavedetdata
--

CREATE TYPE retproductsdet AS (
	id integer,
    store_product_id integer,
    code character varying,
    colorid integer,
    price numeric(10,2),
    promoprice numeric(10,2),
    deliveryprice numeric(10,2),
    state integer,
    available integer,
    description character varying
);
ALTER TYPE retproductsdet OWNER TO postgres84;

CREATE OR REPLACE FUNCTION spproductssavedetdata(
	pop integer, 
	pid integer, 
	pstore_product_id integer, 
	pcode character varying, 
	pcolorid integer, 
	pprice numeric,
	ppromoprice numeric, 
	pdeliveryprice numeric, 
	pstate integer, 
	pavailable integer, 
	pdescription character varying
) RETURNS retproductsdet AS
$$
DECLARE
	lResult retProductsDet;
	lHasColor int4;
	lDescription varchar;
BEGIN

	IF(pdescription IS NULL) THEN
		lDescription = 'Цена';
	ELSE 
		lDescription = pdescription;
	END IF;
	
	IF (pOp = 0) THEN
		SELECT INTO lResult id, productid, code, colorid, price, promoprice, deliveryprice, state, available, description
		FROM store_products_det
		WHERE id = pId;
	ELSIF (pOp = 1) THEN
	SELECT INTO lHasColor color FROM store_products WHERE id = pstore_product_id;

		IF (pId is null) THEN
			INSERT INTO store_products_det(
				productid, 
				code, 
				colorid, 
				price, 
				promoprice, 
				deliveryprice, 
				state, 
				available,
				description
			)
			VALUES(
				pstore_product_id,
				pcode ,
				(CASE WHEN lHasColor = 1 THEN pcolorid ELSE null END),
				pprice,
				ppromoprice,
				pdeliveryprice,
				pstate,
				pavailable,
				lDescription
			);
			
			lResult.id = currval('store_products_det_id_seq');
		ELSE
			UPDATE store_products_det
				SET
					code = pcode, 
					colorid = (CASE WHEN lHasColor = 1 THEN pcolorid ELSE null END),
					price = pprice, 
					promoprice = ppromoprice, 
					deliveryprice = pdeliveryprice, 
					state = pstate, 
					available = pavailable,
					description = lDescription
			WHERE id = pId;
			
		END IF;
		lResult.store_product_id = pstore_product_id;
	ELSIF (pOp = 3) THEN
		SELECT INTO lResult.store_product_id so.id FROM store_products so JOIN store_products_det sod ON so.id = sod.productid WHERE sod.id = pId;
		DELETE FROM store_products_det WHERE id = pId;
	END IF;
	RETURN lResult;

END ;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
  
ALTER FUNCTION spproductssavedetdata(
	integer, 
	integer, 
	integer, 
	character varying, 
	integer,
	numeric, 
	numeric, 
	numeric, 
	integer, 
	integer, 
	character varying
) OWNER TO postgres84;

GRANT EXECUTE ON FUNCTION spproductssavedetdata(
	integer, 
	integer, 
	integer, 
	character varying, 
	integer,
	numeric, 
	numeric, 
	numeric, 
	integer, 
	integer, 
	character varying
) TO public;

GRANT EXECUTE ON FUNCTION spproductssavedetdata(
	integer, 
	integer, 
	integer, 
	character varying, 
	integer,
	numeric, 
	numeric, 
	numeric, 
	integer, 
	integer, 
	character varying
) TO postgres84;

GRANT EXECUTE ON FUNCTION spproductssavedetdata(
	integer, 
	integer, 
	integer, 
	character varying, 
	integer,
	numeric, 
	numeric, 
	numeric, 
	integer, 
	integer, 
	character varying
) TO iusrpmt;

--
-- storeorders
--

CREATE TYPE ret_storeorders AS (
  id int4,
  createdate timestamp,
  recipient_name varchar,
  recipient_city_name varchar,
  recipient_address varchar,
  recipient_phone varchar,
  total numeric(10,2),
  ip_addr inet,
  state int,
  delivery_price numeric(10,2)
);
 
CREATE OR REPLACE FUNCTION storeorders
(
	pop int4, 
	pid int4,
	precipient_name varchar, 
	precipient_city_name varchar,
	precipient_address varchar,
	precipient_phone varchar, 
	pstate int,
	ptotal numeric(10,2),
	pip inet,
	pdelivery_price numeric(10,2)
) RETURNS ret_storeorders AS
$$
DECLARE
	lRes ret_storeorders;
	lTotal numeric(10,2);
	lpayid int4;
BEGIN
	lTotal := (SELECT sum(price) from store_orders_det where store_orders_id = pID);
	IF (pOp = 0) THEN --GET
		SELECT INTO lRes 
			so.id,
			so.createdate,
			so.recipient_name,
			so.recipient_city_name,
			so.recipient_address,
			so.recipient_phone,
			so.total,
			so.ip_addr,
			p.state,
			so.delivery_price
			FROM store_orders so
			JOIN pays p ON so.payid = p.payid
			WHERE id = pID;
	ELSIF (pOp = 1) THEN --INSERT UPDATE
		SELECT INTO lpayid payid FROM store_orders WHERE id = pID;
		
		UPDATE store_orders
		SET 
			recipient_name = precipient_name, 
			recipient_city_name = precipient_city_name,
			recipient_address = precipient_address,
			recipient_phone = precipient_phone, 
			total= lTotal + pdelivery_price,
			ip_addr = pip,
			delivery_price = pdelivery_price
		WHERE id = pID;
		
		UPDATE pays SET 
			state = pState 
		WHERE payid = lpayid;	
		
		PERFORM storeorderscalculatesums(pID);
	ELSIF (pOp = 3) THEN --DELETE
		SELECT INTO lpayid payid FROM store_orders WHERE id = pID;
		DELETE FROM store_orders WHERE id = pID;
		DELETE FROM pays WHERE payid = lpayid;
	END IF;
	RETURN lRes;

END ;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
  
ALTER FUNCTION storeorders(
	pop int4, 
	pid int4,
	precipient_name varchar, 
	precipient_city_name varchar,
	precipient_address varchar,
	precipient_phone varchar, 
	pstate int,
	ptotal numeric(10,2),
	pip inet,
	pdelivery_price numeric(10,2)
)
OWNER TO postgres84;

GRANT EXECUTE ON FUNCTION storeorders(
	pop int4, 
	pid int4,
	precipient_name varchar, 
	precipient_city_name varchar,
	precipient_address varchar,
	precipient_phone varchar, 
	pstate int,
	ptotal numeric(10,2),
	pip inet,
	pdelivery_price numeric(10,2)
)
TO postgres84;

GRANT EXECUTE ON FUNCTION storeorders(
	pop int4, 
	pid int4,
	precipient_name varchar, 
	precipient_city_name varchar,
	precipient_address varchar,
	precipient_phone varchar, 
	pstate int,
	ptotal numeric(10,2),
	pip inet,
	pdelivery_price numeric(10,2)
)TO iusrpmt;

--
-- storeordersdet
--

CREATE TYPE ret_storeordersdet AS (
	id int,
	store_orders_id int, 
	store_products_id int, 
	store_products_det_id int,
	qty int
);

CREATE OR REPLACE FUNCTION storeordersdet(
	pop int4,
	pid int4,
	porderid int4, 
	pproductid int4, 
	pproductdetid int4,
	pqty int4
) RETURNS ret_storeordersdet AS
$$
DECLARE
	lRes ret_storeordersdet;
BEGIN
	lRes.store_orders_id:=pOrderid;
	IF (pOp = 0) THEN
		SELECT INTO lRes id, store_orders_id, store_products_id, store_products_det_id, qty from store_orders_det WHERE id = pID;
	ELSIF (pOp = 1) THEN 
		IF NOT EXISTS(SELECT * FROM store_orders_det WHERE id = pID) THEN --INSERT
			INSERT INTO store_orders_det(
					store_orders_id, 
					store_products_id,
					store_products_det_id,
					qty,
					createdate
				)
				VALUES(
					pOrderid,
					pProductid, 
					pProductdetid,
					pQty, 
					CURRENT_TIMESTAMP
				);
		ELSE --update
			UPDATE store_orders_det
				SET qty = pqty
			WHERE id = pID;
		END IF;
	ELSIF (pOp = 3) THEN --DELETE
		DELETE FROM store_orders_det WHERE id = pID;
	END IF;
	
	RETURN lRes;

 END;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
  
ALTER FUNCTION storeordersdet(
	pop int4,
	pid int4,
	porderid int4, 
	pproductid int4, 
	pproductdetid int4,
	pqty int4
)
OWNER TO postgres84;

GRANT EXECUTE ON FUNCTION storeordersdet(
	pop int4,
	pid int4,
	porderid int4, 
	pproductid int4, 
	pproductdetid int4,
	pqty int4
)
TO postgres84;

GRANT EXECUTE ON FUNCTION storeordersdet(
	pop int4,
	pid int4,
	porderid int4, 
	pproductid int4, 
	pproductdetid int4,
	pqty int4
)
TO iusrpmt;

--
-- delproductfromstory
--

CREATE OR REPLACE FUNCTION delproductfromstory(pstoryid int4, pstoryid1 int4, ppropid int4)
  RETURNS int4 AS
$$
DECLARE
	lguid INT;
BEGIN
	DELETE FROM storyproperties WHERE guid = pstoryid AND valint = pstoryid1 AND propid = ppropid;
	RETURN 1;
END;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
ALTER FUNCTION delproductfromstory(pstoryid int4, pstoryid1 int4, ppropid int4) OWNER TO postgres84;
GRANT EXECUTE ON FUNCTION delproductfromstory(pstoryid int4, pstoryid1 int4, ppropid int4) TO postgres84;
GRANT EXECUTE ON FUNCTION delproductfromstory(pstoryid int4, pstoryid1 int4, ppropid int4) TO iusrpmt;

--
-- addshopmenu
--

CREATE OR REPLACE FUNCTION addshopmenu(
	pop int
) RETURNS int AS
$$
DECLARE
	lMenuId int;
BEGIN
	lMenuId := 0;
	IF(pop = 1) THEN -- insert
		INSERT INTO secsites(url, name, cnt, ord, type) VALUES ('/store/', 'Магазин', 2, 4, 1);
		lMenuId := currval('secsites_id_seq');
		INSERT INTO secgrpacc(gid, sid, type) VALUES (2, lMenuId, 6);
		
		INSERT INTO secsites(url, name, cnt, ord, type) VALUES ('/store/products/', 'Продукти', 3, 2, 1);
		lMenuId := currval('secsites_id_seq');
		INSERT INTO secgrpacc(gid, sid, type) VALUES (2, lMenuId, 6);
		
		INSERT INTO secsites(url, name, cnt, ord, type) VALUES ('/store/orders/', 'Поръчки', 3, 1, 1);
		lMenuId := currval('secsites_id_seq');
		INSERT INTO secgrpacc(gid, sid, type) VALUES (2, lMenuId, 6);
		
		INSERT INTO secsites(url, name, cnt, ord, type) VALUES ('/store/orders/store_orders_det/', '*Поръчки - продукти', 4, 1, 1);
		lMenuId := currval('secsites_id_seq');
		INSERT INTO secgrpacc(gid, sid, type) VALUES (2, lMenuId, 6);
	END IF;
	
	RETURN 1;
 END;
$$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;
  
ALTER FUNCTION addshopmenu(
	pop int
)
OWNER TO postgres84;

GRANT EXECUTE ON FUNCTION addshopmenu(
	pop int
)
TO postgres84;

GRANT EXECUTE ON FUNCTION addshopmenu(
	pop int
)
TO iusrpmt;

--
-- insert na menutata v administraciqta
--

SELECT * FROM addshopmenu(1);