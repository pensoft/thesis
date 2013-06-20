<?php
// @formatter->off
$gTemplArr = array(
	'duedatepopup.edit_form' => '
			<div class="loginFormRegErrHolder">{~}{~~}</div>	
			<!-- {_ChangeDueDate(1)} -->
			<div class="input-reg-title">{*duedate} <span class="txtred">*</span></div>
			<div class="P-Input-Full-Width P-W100 fieldHolder" id="duedateFields">
				{duedate}
				<script type="text/javascript">
					$(function() {
						$("#dueDate").datepicker({
							showOn: "button",
							buttonImage: "i/calendar.png",
							buttonImageOnly: true,
							dateFormat: \'yy/mm/dd\',
							minDate: 1,
							defaultDate: "+1d",
						});
						$(".ui-datepicker-trigger").attr(\'title\', \'Choose a date\');
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
	',	
);

?>