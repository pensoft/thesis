spMoveGroupOrUserUpDown
-- WHERE pos > lPos ORDER BY pos ASC LIMIT 1; old
	WHERE pos > lPos AND journal_user_group_id = lGId ORDER BY pos ASC LIMIT 1;
-- WHERE pos < lPos ORDER BY pos DESC LIMIT 1; old
WHERE pos < lPos AND journal_user_group_id = lGId ORDER BY pos DESC LIMIT 1;