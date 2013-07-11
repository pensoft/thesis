/**
	Action 4
			if(pAjaxResult['err_cnt'] > 0){
				alert(pAjaxResult['err_msg']);
				return;
			}
			if(typeof HandleActiveMenuAfterInstanceCreation == typeof(Function)){
				HandleActiveMenuAfterInstanceCreation(pAjaxResult);
			}
			if(gPreviewMode){
				HandlePreviewModeCreateInstance(pAjaxResult);
				return;
			}
			
			if(pAjaxResult['display_in_tree'] > 0){
				window.location.href = '/display_document.php?instance_id=' + pAjaxResult['new_instance_id'];
				return;
			}
			//Reload container items
			executeAction(gActionReloadContainerActionsId, pInstanceId, pAjaxResult['container_id'], pAjaxResult['root_instance_id'], pAjaxResult['new_instance_id'], 1);
*/