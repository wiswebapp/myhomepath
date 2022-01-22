<?php 
						
	function buildInputText($title,$name,$type,$value = '',$required = "required",$id = '',$readOnly=''){
		//					1		2		3	4				5					6			7	
		$mark = "";
		if($required == "required"){
			$mark = " <span class='text-danger'>*</span>";
		}
		
		$id = !empty($id) ? "id = '".$id."'" : "";
	
		$op = "";
		if($type == "textarea"){
	        $op .=	'<div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">';
	        $op .=	'<label>'.$title.''.$mark.'</label>';
	        $op .=	'<textarea class="form-control '.$id.'" name="'.$name.'" placeholder="Enter '.$title.'" '.$required.' '.$readOnly.'>'.$value.'</textarea>';
	        $op .=	'</div>';
	    }else{
	    	$op .=	'<div class="form-group col-xl-7 col-lg-7 col-md-7 col-sm-12 col-12">';
	        $op .=	'<label>'.$title.''.$mark.'</label>';
	        $op .=	'<input type="'.$type.'" '.$id.' name="'.$name.'" class="form-control '.$id.'" placeholder="Enter '.$title.'" value="'.$value.'" '.$required.' '.$readOnly.'>';
	        $op .=	'</div>';
	    }

        return $op;
	}

	function buildSettingInput($inputType,$inputName,$inputValue,$selectVal = ''){

		switch (ucfirst(strtolower($inputType))) {
			case 'Text':
				$op = "<input type='text' name='".$inputName."' required='required' class='bulkAction form-control' value='".$inputValue."'>";
				break;
			case 'Textarea':
				$op = "<textarea name='".$inputName."' required='required' class='bulkAction form-control'>".$inputValue."</textarea>";
				break;
			case 'Number':
				$op = "<input type='number' name='".$inputName."' required='required' class='bulkAction form-control' value='".$inputValue."'>";
				break;
			case 'Select':
				$op = "<select class='bulkAction form-control' name='".$inputName."'>";
				foreach (explode(',', $selectVal) as $selvalue) {
					if($inputValue == $selvalue){
						$op .= "<option selected value='".$selvalue."'>".$selvalue."</option>";
					}else{
						$op .= "<option value='".$selvalue."'>".$selvalue."</option>";
					}
				}
				$op .= "</select>";
				break;
			default:
				$op = "<input type='text' required='required' class='bulkAction form-control' value='".$inputValue."'>";
				break;
		}

		return $op;
	}



?>