var gFigureObjectId = 221;
var gFigureImageType = 1;
var gFigurePlateType = 2;
var gFigureVideoType = 3;
var gChangeFigureTypeActionId = 119;
var gChangePlateTypeActionId = 121;
var gCreatePlateDetailsActionId = 122;
var gDeletePlateDetailsActionId = 123;
var gPlateTypeFieldId = 485;
function AddFigureImage(pParentInstanceId){
	CreateImagePopup(pParentInstanceId, gFigureImageType);
}

function AddFigurePlate(pParentInstanceId){
	CreateImagePopup(pParentInstanceId, gFigurePlateType);
}

function AddFigureMovie(pParentInstanceId){
	CreateImagePopup(pParentInstanceId, gFigureVideoType);
}

function CreateImagePopup(pParentInstanceId, pFigureType){
	var lAdditionalData = {
		'figure_type' : pFigureType
	};
	CreatePopup(pParentInstanceId, gFigureObjectId, lAdditionalData);
}

function ChangeFigureType(pFigureInstanceId, pFigureType){
	executeAction(gChangeFigureTypeActionId, pFigureInstanceId, pFigureType);
}

function ChangeFigureMenuActiveTab(pFigureType){
	var lActiveMenuClass = 'P-Active';
	$('#popUp_nav li').removeClass(lActiveMenuClass);
	$('#popUp_nav li[figure_type="' + pFigureType + '"]').addClass(lActiveMenuClass);
}

//function ChangePlateType(pPlateInstanceId, pPlateType){
//	executeAction(gChangePlateTypeActionId, pPlateInstanceId, pPlateType);
//}

function InitPlateTypeOnchangeEvents(pInstanceId, pPlateName){
//	$('form[name="' + gActiveInstanceFormName +  '"] input[name="' + pPlateName + '"]').bind('change', function(){
//			ChangePlateType(pInstanceId, $(this).val());
//	});
}

function CreatePlateDetails(pPlateInstanceId){
	var lPlateType = $('form[name="' + gActiveInstanceFormName+ '"] input[name="' + pPlateInstanceId + gInstanceFieldNameSeparator + gPlateTypeFieldId + '"]:checked').val();
	if(!lPlateType){
		alert('You have to select plate type first!');
		return;
	}
	executeAction(gCreatePlateDetailsActionId, pPlateInstanceId, lPlateType);
}

function DeletePlateDetails(pPlateInstanceId){
	if(!confirm('Are you sure you want to delete the current plate details?')){
		return;
	}
	executeAction(gDeletePlateDetailsActionId, pPlateInstanceId);
}

function UploadFigureImageFile(pBtnId, pDocId, pInstanceId, pFieldId, pIsPlate) {
	var btnUpload = $('#' + pBtnId);
	var AjaxFileUpload = new AjaxUpload(btnUpload, {
		action: '/lib/ajax_srv/figure_file_upload_srv.php',
		responseType: 'json',
		name: 'uploadfile',
		hoverClass: 'UploadHover',
		data: {
			document_id: pDocId,
		},
		onSubmit: function(file, ext){
			showLoading();
			if (! (ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){
				hideLoading();
				$('#' + pUpdateHolder).text('Only JPG, PNG and GIF files are allowed');
				return false;
			}
		},
		onComplete: function(file, response){
			hideLoading();
			if(response != 0 && !response['err_cnt']){
//				$('#field_' + pInstanceId + '__' + pFieldId).siblings('.' + gUploadFileNameHolderClass).html(response['file_name']);
				$('#field_' + pInstanceId + '__' + pFieldId).val(response['file_id']);
				UpdateInstanceFieldValue(pInstanceId, pFieldId, response['file_id'], 1);
				if(!pIsPlate){
					var dims = jQuery.parseJSON(response['img_dims']); // get picture dimensions and resize container
					$('.P-Plate-Part').width(dims[0] + 20);
					$('.P-Plate-Part-WithPic').width(dims[0] + 20);
					$('.P-Plate-Part').height(dims[1] + 20);
					$('.P-Plate-Part-WithPic').height(dims[1] + 20);
					$('.P-Add-Plate-Holder').width(dims[0]);
					$('.P-Add-Plate-Holder').height(dims[1]);
					$("#uploaded_photo").attr("src", "/showfigure.php?filename=" + response['html'] + ".jpg");
					$('#figures_image_holder').closest('.P-Plate-Part').removeClass('P-Plate-Part').addClass('P-Plate-Part-WithPic');
					$('#figures_image_holder').html('<img id="uploaded_photo" src="/showfigure.php?filename=' + response['html'] + '.jpg"></img>');
					$("#uploaded_photo").attr("src","/showfigure.php?filename=" + response['html'] + ".jpg&" + Math.random()); // za da se refreshne snimkata
					if(response['pic_id']) { //update pic holder
						$('#P-Figures-Row-' + response['pic_id'] + ' .P-Picture-Holder').html('<img src="/showfigure.php?filename=c90x82y_' + response['pic_id'] + '.jpg&' + Math.random() + '"></img>');
					}
				}else{
					var lUpdateHolder = $('#figures_image_plate_holder_' + pInstanceId + '_' + pFieldId);
					lUpdateHolder.closest('.P-Plate-Part').removeClass('P-Plate-Part').addClass('P-Plate-Part-WithPic');
					lUpdateHolder.html('<img  id="uploaded_photo_' + response['pic_id'] + '" src="/showfigure.php?filename=' + response['plate_pic'] + '.jpg"></img>');
					$("#uploaded_photo_" + response['pic_id']).attr("src","/showfigure.php?filename=" + response['plate_pic'] + ".jpg&" + Math.random()); // za da se refreshne snimkata
				}
			}else{
				if(response['err_msg']) {
					$('#' + pUpdateHolder).html(response['err_msg']);
				} else {
					$('#' + pUpdateHolder).html('error uploading file');
				}
			}
			

		}
	});
}