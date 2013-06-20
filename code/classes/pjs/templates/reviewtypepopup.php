<?php
// @formatter->off
$gTemplArr = array(
	'reviewtypepopup.edit_form' => '
			<div style="margin-left: 20px;">
				<div class="loginFormRegErrHolder">{~}{~~}</div>	
				<!-- {_ChangeDueDate(1)} -->
				{document_id}
				<div class="input-reg-title">{*review_type} <span class="txtred">*</span></div>
				<div class="P-Input-Full-Width P-W100 fieldHolder" id="duedateFields">
					{review_type}
					<script type="text/javascript">
						$(document).ready(function(){
							$(\'.review_types option:first\').attr(\'disabled\', \'disabled\');
						});
					</script>
				</div>
					<div class="P-Clear"></div>
					<div class="P-Grey-Btn-Holder P-Reg-Btn" style="padding-top: 10px;">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">{save}</div>
						<div class="P-Grey-Btn-Right"></div>
					</div>
					<div class="P-Grey-Btn-Holder P-Reg-Btn-R simplemodal-close" style="padding-top: 10px;">
						<div class="P-Grey-Btn-Left"></div>
						<div class="P-Grey-Btn-Middle">{cancel}</div>
						<div class="P-Grey-Btn-Right"></div>
				</div>
			</div>
	',	
);

?>