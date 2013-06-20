DROP TYPE ret_spGetInstanceBaseInfo CASCADE;
CREATE TYPE ret_spGetInstanceBaseInfo AS (
	object_id bigint,
	name varchar,
	display_label int,
	display_nesting_indicator int,
	css_class varchar,
	document_id bigint,
	display_title_and_top_actions int,
	display_name varchar,
	allowed_modes integer[],
	default_mode_id int,
	default_new_mode_id int,
	display_default_actions int,
	title_display_style int,
	is_new int,
	dto_id bigint,
	idx int,
	up int,
	down int,
	
	allow_remove int,
	allow_add int,
	
	displayed_positions int[],
	has_field_comments int,
	xsl_dir_name varchar,
	view_xpath_selection varchar,
	view_xsl_template_mode varchar
);

CREATE OR REPLACE FUNCTION spGetInstanceBaseInfo(
	pInstanceId bigint,
	pMode int
)
  RETURNS ret_spGetInstanceBaseInfo AS
$BODY$
	DECLARE
		lRes ret_spGetInstanceBaseInfo;	
		lMode int;
	BEGIN
		lMode = pMode;
		SELECT INTO lRes
			o.id as object_id, 
			o.name, 
			o.display_label::int as display_label, 
			o.display_nesting_indicator::int as display_nesting_indicator, 
			o.css_class,
			di.document_id, 
			dto.display_title_and_top_actions::int, 
			di.display_name, 
			dto.allowed_modes, 
			dto.default_mode_id, 
			dto.default_new_mode_id,
			dto.display_default_actions::int as display_default_actions, 
			dto.title_display_style, 
			di.is_new::int as is_new,
			dto.id as dto_id,
			(SELECT count(*) + 1 FROM
				pwt.document_object_instances
			WHERE object_id = di.object_id AND parent_id = di.parent_id AND pos < di.pos) as idx,
			null as up, 
			null as down,
			null as allow_remove,
			null as allow_add,
			null as displayed_positions, 
			null as has_field_comments,
			tem.xsl_dir_name as xsl_dir_name,
			dto.view_xpath_sel as view_xpath_selection,
			dto.view_xsl_templ_mode as view_xsl_template_mode
		FROM pwt.objects o
		JOIN pwt.document_object_instances di ON di.object_id = o.id
		JOIN pwt.document_template_objects dto ON dto.id = di.document_template_object_id
		JOIN pwt.templates tem ON tem.id = dto.template_id
		WHERE di.id = pInstanceId;
		
		SELECT INTO lRes.up, lRes.down
			up::int, down::int
		FROM spCheckInstanceForAvailableMovement(pInstanceId);
		
		SELECT INTO lRes.allow_remove, lRes.allow_add
			allow_remove::int, allow_add::int
		FROM spCheckInstanceForAvailableAddRemove(pInstanceId);
		
		IF NOT lMode = ANY (lRes.allowed_modes) THEN
			IF lRes.is_new > 0 THEN
				lMode = lRes.default_new_mode_id;
			ELSE
				lMode = lRes.default_mode_id;
			END IF;
		END IF;
		
		SELECT INTO lRes.displayed_positions
			coalesce(at.displayed_positions, ARRAY[]::int[])
			FROM  pwt.document_template_objects dto
			LEFT JOIN pwt.object_displayed_actions_types_details at ON at.type_id = dto.displayed_actions_type AND at.mode = lMode
			WHERE dto.id = lRes.dto_id;
		
		lRes.has_field_comments = 0;
		
		IF EXISTS (
			SELECT * 
			FROM pwt.msg
			WHERE (start_object_instances_id = pInstanceId AND coalesce(start_object_field_id, 0) > 0 AND start_offset >= 0) 
				OR (end_object_instances_id = pInstanceId AND coalesce(end_object_field_id, 0) > 0 AND end_offset >= 0)
			LIMIT 1
		) THEN
			lRes.has_field_comments = 1;
		END IF;
		
		RETURN lRes;
	END
$BODY$
  LANGUAGE 'plpgsql' VOLATILE SECURITY DEFINER;

GRANT EXECUTE ON FUNCTION spGetInstanceBaseInfo(
	pInstanceId bigint,
	pMode int
) TO iusrpmt;
