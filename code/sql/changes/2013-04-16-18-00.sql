ALTER TABLE pjs.msg ADD COLUMN is_resolved boolean DEFAULT false;
ALTER TABLE pjs.msg ADD COLUMN resolve_uid int REFERENCES public.usr(id);
ALTER TABLE pjs.msg ADD COLUMN resolve_date timestamp;