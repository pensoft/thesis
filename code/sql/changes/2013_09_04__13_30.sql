ALTER TABLE pwt.documents ADD COLUMN last_content_change timestamp DEFAULT NOW();
ALTER TABLE pwt.documents ADD COLUMN lock_primary_date timestamp DEFAULT NOW();


/*
	Modified sps
	pwt.spMarkInstanceAsModified
	spInstanceModifiedTrigger - Change the trigger when
	spAutoUnlockDocument
	pwt.spLockDocument
	pwt.spAutoLockDocument
*/