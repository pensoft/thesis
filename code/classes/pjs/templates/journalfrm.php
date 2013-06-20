<?php

$gTemplArr = array(
	'journalfrm.edit_form' => '
		{~}{~~}{guid}{parent_id}{journal_id}
		<div class="P-Input-Full-Width P-W100 fieldHolder">
			{title}
		</div>
		<br />
		{description}
		{_createHtmlEditorBase(description_textarea)}
		<br/>
		{add_to_sidebar}
		<div class="P-Grey-Btn-Holder P-Reg-Btn">
		<div class="P-Grey-Btn-Left"></div>
		<div class="P-Grey-Btn-Middle">{save}</div>
		<div class="P-Grey-Btn-Right"></div>
		</div>
		<div class="P-Grey-Btn-Holder P-Reg-Btn-R">
		<div class="P-Grey-Btn-Left"></div>
		<div class="P-Grey-Btn-Middle">
			<input type="submit" name="tAction" value="cancel" onclick="redirectToBrowsePage();return false;"></input>
		</div>
		<div class="P-Grey-Btn-Right"></div>
		</div>
		<div class="clear"></div>
		
		<script type="text/javascript">
			function redirectToBrowsePage(){
				var lJournalId = $(\'#journalIdFld\').val();
				window.location.href="/manage_journal_about_pages.php?journal_id=" + lJournalId + "";
			}
		</script>
	',
);

?>