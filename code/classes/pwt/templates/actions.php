<?php

$gTemplArr = array(
	'actions.moveUpRow' => '
				<div id="move_up_link_instance_{instance_id}" style="display:{display};" href="javascript:void(0)" onclick=\'{js_action}\' class="section_arrow_up"></div>
	',

	'actions.rightMoveUpRow' => '
				<div id="move_up_right_link_instance_{instance_id}" style="display:{display};" href="javascript:void(0)" onclick=\'{js_action}\' class="section_arrow_up"></div>
				{*actions.rightActionCommon}
	',
	
	'actions.rightActionCommon' => '<script type="text/javascript">MarkInstanceRightActionsHolderAsNotInited({instance_id});</script>',

	'actions.moveDownRow' => '
				<div id="move_down_link_instance_{instance_id}" style="display:{display};" href="javascript:void(0)" onclick=\'{js_action}\' class="section_arrow_down"></div>
	',

	'actions.rightMoveDownRow' => '
				<div id="move_down_right_link_instance_{instance_id}" style="display:{display};" href="javascript:void(0)" onclick=\'{js_action}\' class="section_arrow_down"></div>
				{*actions.rightActionCommon}
	',

	'actions.rightDeleteRow' => '
				<div class="P-Delete-Btn" onclick=\'{js_action}\'></div>
				{*actions.rightActionCommon}
	',

	'actions.topRedRow' => '
				<div class="P-Remove-Btn-Holder P-Remove-Right" onclick=\'{js_action}\'>
					<div class="P-Remove-Btn-Left"></div>
					<div class="P-Remove-Btn-Middle">{display_name}</div>
					<div class="P-Remove-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.addRow' => '
				<div onclick=\'{js_action}\' class="P-Grey-Btn-Holder P-Add">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.bottomEditRow' => '
				<div onclick=\'{js_action}\' class="P-Grey-Btn-Holder P-Edit P-Bottom-Action">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.addAllRow' => '
				<div onclick=\'{js_action}\' class="P-Grey-Btn-Holder P-Add-All">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.commentRow' => '
				<div onclick=\'{js_action}\' class="P-Grey-Btn-Holder P-Comment">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.validationRow' => '
				<div onclick=\'{js_action}\' class="P-Grey-Btn-Holder P-Validation">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.checkNameAvailabilityRow' => '
				<div onclick=\'{js_action}\' class="P-Grey-Btn-Holder">
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.bottomSaveRow' => '
				<div class="P-Green-Btn-Small-Holder P-Bottom-Action" onclick=\'{js_action}\'>
					<div class="P-Green-Btn-Small-Left"></div>
					<div class="P-Green-Btn-Small-Middle">{display_name}</div>
					<div class="P-Green-Btn-Small-Right"></div>
				</div>
	',

	'actions.bottomCancelRow' => '
				<div class="P-Grey-Btn-Holder2 P-Bottom-Action" onclick=\'{js_action}\'>
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
	',

	'actions.bottomRedRow' => '
				<div class="P-Remove-Btn-Holder P-Bottom-Action" onclick=\'{js_action}\'>
					<div class="P-Remove-Btn-Left"></div>
					<div class="P-Remove-Btn-Middle">{display_name}</div>
					<div class="P-Remove-Btn-Right"></div>
					<div class="unfloat"></div>
				</div>
	',

	'actions.topChangeModeRow' => '
				<div class="P-Grey-Btn-Holder2 P-Edit Action-Top-Btn-Holder" onclick=\'{js_action}\'>
					<div class="P-Grey-Btn-Left"></div>
					<div class="P-Grey-Btn-Middle"><div class="P-Btn-Icon"></div>{display_name}</div>
					<div class="P-Grey-Btn-Right"></div>
				</div>
	',
);

?>