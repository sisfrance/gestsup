/*!
################################################################################
* Name : ticket.js
# Description : form color for mandatory fields
# Call : /ticket.php 
# Author : Flox
# Create : 08/10/2019
# Update : 08/10/2019
# Version : 3.1.45
################################################################################
*/
 
//check default values
	//type field
	var type=document.getElementById('type_field_mandatory').value;
	if(type == "0"){
		document.getElementById('type_field_mandatory').style.borderColor = "#f09784";
		document.getElementById('type_field_mandatory').style.color = "#d68273";
		document.getElementById('type_label_mandatory').style.color = "#d68273";
	}else{
		document.getElementById('type_field_mandatory').style.borderColor = "#92bf65";
		document.getElementById('type_field_mandatory').style.color = "#8bad4c";
		document.getElementById('type_label_mandatory').style.color = "#7ba065";
		if(document.getElementById('type_warning')){document.getElementById('type_warning').style.display = "none";}
	}
	
	//category
	var category=document.getElementById('category_field_mandatory').value;
	var subcat=document.getElementById('subcat').value;
    if(subcat == ""){
        document.getElementById('category_field_mandatory').style.borderColor = "#f09784";
        document.getElementById('category_field_mandatory').style.color = "#d68273";
		document.getElementById('subcat').style.borderColor = "#d68273";
		document.getElementById('subcat').style.color = "#d68273";
		document.getElementById('cat_label_mandatory').style.color = "#d68273";
    }else{
        document.getElementById('category_field_mandatory').style.borderColor = "#92bf65";
        document.getElementById('category_field_mandatory').style.color = "#8bad4c";
		document.getElementById('subcat').style.borderColor = "#92bf65";
		document.getElementById('subcat').style.color = "#8bad4c";
		document.getElementById('cat_label_mandatory').style.color = "#7ba065";
    }

//check on change value
function FormValidation(){
	//type 
	var type=document.getElementById('type_field_mandatory').value;
    if(type == "0"){
        document.getElementById('type_field_mandatory').style.borderColor = "#f09784";
        document.getElementById('type_field_mandatory').style.color = "#d68273";
		document.getElementById('type_label_mandatory').style.color = "#d68273";
    }else{
        document.getElementById('type_field_mandatory').style.borderColor = "#92bf65";
        document.getElementById('type_field_mandatory').style.color = "#8bad4c";
		document.getElementById('type_label_mandatory').style.color = "#7ba065";
		if(document.getElementById('type_warning')){document.getElementById('type_warning').style.display = "none";}
    }
	
	//category
	var category=document.getElementById('category_field_mandatory').value;
	var subcat=document.getElementById('subcat').value;
    if(subcat == ""){
        document.getElementById('category_field_mandatory').style.borderColor = "#f09784";
        document.getElementById('category_field_mandatory').style.color = "#d68273";
		document.getElementById('subcat').style.borderColor = "#d68273";
        document.getElementById('subcat').style.color = "#d68273";
		document.getElementById('cat_label_mandatory').style.color = "#d68273";
    }else{
        document.getElementById('category_field_mandatory').style.borderColor = "#92bf65";
        document.getElementById('category_field_mandatory').style.color = "#8bad4c";
		document.getElementById('subcat').style.borderColor = "#92bf65";
        document.getElementById('subcat').style.color = "#8bad4c";
		document.getElementById('cat_label_mandatory').style.color = "#7ba065";
    }
}