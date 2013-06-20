ALTER TABLE pwt.msg ADD COLUMN is_resolved boolean DEFAULT false;
ALTER TABLE pwt.msg ADD COLUMN resolve_uid int REFERENCES public.usr(id);
ALTER TABLE pwt.msg ADD COLUMN resolve_date timestamp;