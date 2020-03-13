<?php
################################################################################
# @Name : procedure.php
# @Description : display, edit and add procedure
# @Call : /index.php
# @Parameters : 
# @Author : Flox
# @Create : 03/09/2013
# @Update : 18/10/2019
# @Version : 3.1.45
################################################################################

//initialize variables 
if(!isset($_POST['addprocedure'])) $_POST['addprocedure'] = '';
if(!isset($_POST['save'])) $_POST['save'] = '';
if(!isset($_POST['modif'])) $_POST['modif'] = '';
if(!isset($_POST['return'])) $_POST['return'] = '';
if(!isset($_POST['subcat'])) $_POST['subcat'] = '';
if(!isset($_POST['company'])) $_POST['company'] = '';
if(!isset($_POST['name'])) $_POST['name'] = '';
if(!isset($_POST['category'])) $_POST['category'] = '';
	
if(!isset($_GET['procedure'])) $_GET['procedure'] = '';
if(!isset($_GET['edit'])) $_GET['edit'] = '';
if(!isset($_GET['delete_file'])) $_GET['delete_file'] = '';

//delete procedure
if($_GET['action']=='delete' && $rright['procedure_delete']!=0)
{
	//disable procedure
	$qry=$db->prepare("UPDATE `tprocedures` SET `disable`='1' WHERE `id`=:id");
	$qry->execute(array('id' => $_GET['id']));
	//display delete message
	echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i>	'.T_('Procédure supprimée').'.</center></div>';
	//redirect
	$www = "./index.php?page=procedure";
	echo "<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='$www'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}

//if delete file is submit
if($_GET['delete_file'] && $rright['procedure_modify']!=0)
{
	//disable ticket
	if($_GET['id']) {unlink('./upload/procedure/'.$_GET['id'].'/'.$_GET['delete_file'].'');}
	//display delete message
	echo '<div class="alert alert-block alert-danger"><center><i class="icon-remove red"></i>	'.T_('Fichier supprimé').'.</center></div>';
	//redirect
	$www = './index.php?page=procedure&action=edit&id='.$_GET['id'];
	echo "<SCRIPT LANGUAGE='JavaScript'>
		<!--
		function redirect()
		{
		window.location='$www'
		}
		setTimeout('redirect()',$rparameters[time_display_msg]);
		-->
	</SCRIPT>";
}

//if add procedure is submit
if($_GET['action']=='add' && $rright['procedure_add']!=0)
{
	//database modification
	if($_POST['save'])
	{
		//create procedure folder if not exist
		if(!file_exists('./upload/procedure')) {
			mkdir('./upload/procedure', 0777, true);
		}
	
		//secure string
		$_POST['name']=strip_tags($_POST['name']);
		$_POST['category']=strip_tags($_POST['category']);
		$_POST['subcat']=strip_tags($_POST['subcat']);
		$_POST['company']=strip_tags($_POST['company']);
		
		$qry=$db->prepare("INSERT INTO `tprocedures` (`name`,`text`,`category`,`subcat`,`company_id`) VALUES (:name,:text,:category,:subcat,:company_id)");
		$qry->execute(array('name' => $_POST['name'],'text' => $_POST['text'],'category' => $_POST['category'],'subcat' => $_POST['subcat'],'company_id' => $_POST['company']));
			
		$procedure_id=$db->lastInsertId();
		
		//upload file in /upload/procedure directory
		if($_FILES['procedure_file']['name'])
		{
			$filename = $_FILES['procedure_file']['name'];
			//change special character in filename
			$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
			$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
			$file_rename = str_replace($a,$b,$_FILES['procedure_file']['name']);
			//secure upload excluding certain extension files
			$whitelist =  array('pdf','doc','docx','png','jpg','jpeg' ,'gif' ,'bmp' , 'rar','zip','7z','ace','arj','bz2','cab','gz','iso','jar','lz','lzh','tar','uue','xz','z','zipx','001');
			//black list exclusion for extension
			$blacklist =  array('php', 'php1', 'php2','php3' ,'php4' ,'php5', 'php6', 'php7', 'php8', 'php9', 'php10', 'js', 'htm', 'html', 'phtml', 'exe', 'jsp' ,'pht', 'shtml', 'asa', 'cer', 'asax', 'swf', 'xap', 'phphp', 'inc', 'htaccess', 'sh', 'py', 'pl', 'jsp', 'asp', 'cgi', 'json', 'svn', 'git', 'lock', 'yaml', 'com', 'bat', 'ps1', 'cmd', 'vb', 'hta', 'reg', 'ade', 'adp', 'app', 'asp', 'bas', 'bat', 'cer', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'der', 'exe', 'fxp', 'gadget', 'hlp', 'hta', 'inf', 'ins', 'isp', 'its', 'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt', 'mdw', 'mdz', 'msc', 'msh', 'msh1', 'msh2', 'mshxml', 'msh1xml', 'msh2xml', 'msi', 'msp', 'mst', 'ops', 'pcd', 'pif', 'plg', 'prf', 'prg', 'pst', 'reg', 'scf', 'scr', 'sct', 'shb', 'shs', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'tmp', 'url', 'vb', 'vbe', 'vbs', 'vsmacros', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xnk');
			//default value
			$blacklistedfile=0;
			$ext=explode('.',$filename);
			foreach ($ext as &$value) {
				$value=strtolower($value);
				if(in_array($value,$blacklist) ) {
					$blacklistedfile=1;
				} 
			}
			if(in_array(end($ext),$whitelist) && $blacklistedfile==0 ) {
				//create procedure directory if not exist
				if(!file_exists('./upload/procedure/'.$procedure_id.'/')) {
					mkdir('./upload/procedure/'.$procedure_id.'', 0777, true);
				}
				$dest_folder = './upload/procedure/'.$procedure_id.'/';
				if(move_uploaded_file($_FILES['procedure_file']['tmp_name'], $dest_folder.$file_rename)) 
				{
				} else {
				echo T_('Erreur de transfert vérifier le chemin').' '.$dest_folder;
				}
			} else {
				echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>'.T_('Blocage de sécurité').':</strong> '.T_('Fichier interdit').'.<br></div>';
			}
		}
		
		//display action message
		echo '
			<div class="alert alert-block alert-success">
				<i class="icon-ok green"></i>
				'.T_('La procédure a été sauvegardée').'.
			</div>
		';
		
		 //redirect
		$www = "./index.php?page=procedure";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	////////////////////////////////////////////////////////// START FORM ADD NEW PROCEDURE ///////////////////////////////////////////////////
	echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-book"></i> '.T_('Ajout d\'une procédure').'
			</h1>
		</div>
		<fieldset>
			<div class="col-xs-12">
				<form method="POST" enctype="multipart/form-data" name="myform" id="myform" action="" onsubmit="loadVal();" >
					<label for="name">'.T_('Nom de la procédure').' :</label>
					<input name="name" size="50px" type="text" value="'; echo $_POST['name']; echo '">
					<br />
					<br />';
					if($rright['procedure_company']!=0)
					{
						echo '
						<label for="company">'.T_('Société').' :</label>
						<select name="company">
							';
							$qry2=$db->prepare("SELECT `id`,`name` FROM `tcompany` WHERE `disable`='0' ORDER BY name");
							$qry2->execute();
							while($row2=$qry2->fetch()) 
							{
								if($_POST['company'])
								{
									if($row2['id']==$_POST['company']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
								} elseif($row['company_id']==$row2['id']) 
								{
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								} 
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
							$qry2->closeCursor();
								
							echo '
						</select>
						<br />
						<br />
						';
					}
					echo '
					<label for="category">'.T_('Catégorie').' :</label>
					<select name="category" onchange="submit();">
					    ';
						$qry2=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
						$qry2->execute();
						while($row2=$qry2->fetch()) 
						{
							 if($_POST['category'])
							{
								if($row2['id']==$_POST['category']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
							} elseif($row['category']==$row2['id']) 
							{
								echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
							} 
							echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
						}
						$qry2->closeCursor();
					    echo '
					</select>
					<br />
					<br />
					<label for="subcat">'.T_('Sous-catégorie').' :</label>
					<select name="subcat">
					   ';
						if($_POST['category'])
						{
							$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE `cat` LIKE :cat ORDER BY `name` ASC"); 
							$qry2->execute(array('cat' => $_POST['category']));
						} else {
							$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE `cat`='1' ORDER BY `name` ASC");
							$qry2->execute();
						}
						while($row2=$qry2->fetch()) 
						{
							if($_POST['subcat'])
							{
								if($row2['id']==$_POST['subcat'])
								{
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								}
							} elseif($row['subcat']==$row2['id']) {
								echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							
						}
						$qry2->closeCursor();
					    echo '
					</select>
					<br /><br />
					<label for="procedure_file">'.T_('Joindre un fichier').' :</label>
					<input name="procedure_file"  type="file" style="display:inline" />
					<br /><br />
					<div id="editor" class="wysiwyg-editor"></div>
					<input type="hidden" name="text" />
					<input type="hidden" name="text2" />
					<div class="form-actions align-right clearfix">
						<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
							<i class="icon-undo bigger-110"></i>
							'.T_('Retour').'
						</button>
						&nbsp;&nbsp;&nbsp;
						<button name="save" value="save" id="save" type="submit" class="btn btn-success">
							<i class="icon-save bigger-110"></i>
							'.T_('Sauvegarder').'
						</button>
					</div>
				</form>
			</div>
		</fieldset>			
	';
	////////////////////////////////////////////////////////// END FORM ADD NEW PROCEDURE ///////////////////////////////////////////////////
}
elseif($_GET['action']=='edit')
{
	
	//Database modification
	if($_POST['modif'])
	{
		//create procedure folder if not exist
		if(!file_exists('./upload/procedure')) {
			mkdir('./upload/procedure', 0777, true);
		}
		
		//upload file in /upload/procedure directory
		if($_FILES['procedure_file']['name'])
		{
			$filename = $_FILES['procedure_file']['name'];
			//change special character in filename
			$a = array('à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'œ', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'š', 'ž', "'", " ", "/", "%", "?", ":", "!", "’", ",",">","<");
			$b = array("a", "a", "a", "a", "a", "a", "ae", "c", "e", "e", "e", "e", "i", "i", "i", "i", "n", "o", "o", "o", "o", "o", "o", "oe", "u", "u", "u", "u", "y", "y", "s", "z", "-", "-", "-", "-", "", "-", "", "-", "-", "", "");
			$file_rename = str_replace($a,$b,$_FILES['procedure_file']['name']);
			//secure upload excluding certain extension files
			$whitelist =  array('pdf','doc','docx','png','jpg','jpeg' ,'gif' ,'bmp' , 'rar','zip','7z','ace','arj','bz2','cab','gz','iso','jar','lz','lzh','tar','uue','xz','z','zipx','001');
			//black list exclusion for extension
			$blacklist =  array('php', 'php1', 'php2','php3' ,'php4' ,'php5', 'php6', 'php7', 'php8', 'php9', 'php10', 'js', 'htm', 'html', 'phtml', 'exe', 'jsp' ,'pht', 'shtml', 'asa', 'cer', 'asax', 'swf', 'xap', 'phphp', 'inc', 'htaccess', 'sh', 'py', 'pl', 'jsp', 'asp', 'cgi', 'json', 'svn', 'git', 'lock', 'yaml', 'com', 'bat', 'ps1', 'cmd', 'vb', 'hta', 'reg', 'ade', 'adp', 'app', 'asp', 'bas', 'bat', 'cer', 'chm', 'cmd', 'com', 'cpl', 'crt', 'csh', 'der', 'exe', 'fxp', 'gadget', 'hlp', 'hta', 'inf', 'ins', 'isp', 'its', 'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt', 'mdw', 'mdz', 'msc', 'msh', 'msh1', 'msh2', 'mshxml', 'msh1xml', 'msh2xml', 'msi', 'msp', 'mst', 'ops', 'pcd', 'pif', 'plg', 'prf', 'prg', 'pst', 'reg', 'scf', 'scr', 'sct', 'shb', 'shs', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'tmp', 'url', 'vb', 'vbe', 'vbs', 'vsmacros', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xnk');
			//default value
			$blacklistedfile=0;
			$ext=explode('.',$filename);
			foreach ($ext as &$value) {
				$value=strtolower($value);
				if(in_array($value,$blacklist) ) {
					$blacklistedfile=1;
				} 
			}
			if(in_array(end($ext),$whitelist) && $blacklistedfile==0 ) {
				//create procedure directory if not exist
				if(!file_exists('./upload/procedure/'.$_GET['id'].'/')) {
					mkdir('./upload/procedure/'.$_GET['id'].'', 0777, true);
				}
				$dest_folder = './upload/procedure/'.$_GET['id'].'/';
				if(move_uploaded_file($_FILES['procedure_file']['tmp_name'], $dest_folder.$file_rename)   ) 
				{
				} else {
				echo T_('Erreur de transfert vérifier le chemin').' '.$dest_folder;
				}
			} else {
				echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i>'.T_('Blocage de sécurité').':</strong> '.T_('Fichier interdit').'.<br></div>';
			}
		}
		
		//secure string
		$_POST['name']=strip_tags($_POST['name']);
		$_POST['category']=strip_tags($_POST['category']);
		$_POST['subcat']=strip_tags($_POST['subcat']);
		$_POST['company']=strip_tags($_POST['company']);
	
		$qry=$db->prepare("UPDATE `tprocedures` SET `name`=:name, `text`=:text, `category`=:category,`subcat`=:subcat,`company_id`=:company_id  WHERE `id`=:id");
		$qry->execute(array('name' => $_POST['name'],'text' => $_POST['text'],'category' => $_POST['category'],'subcat' => $_POST['subcat'],'company_id' => $_POST['company'],'id' => $_GET['id']));
		
		//display action message
		echo '
			<div class="alert alert-block alert-success">
				<i class="icon-ok green"></i>
				'.T_('La procédure').'
				<strong class="green">
					<small>'.$_GET['id'].'</small>
				</strong>
				'.T_('a été sauvegardée').'.
			</div>
		';
		
		 //redirect
		$www = "./index.php?page=procedure&id=$_GET[id]&action=edit&";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	if($_POST['return'])
	{
		//redirect
		$www = "./index.php?page=procedure";
		echo "<SCRIPT LANGUAGE='JavaScript'>
			<!--
			function redirect()
			{
			window.location='$www'
			}
			setTimeout('redirect()',$rparameters[time_display_msg]);
			-->
			</SCRIPT>";
	}
	//get data of current selected procedure
	$qry=$db->prepare("SELECT * FROM `tprocedures` WHERE id=:id");
	$qry->execute(array('id' => $_GET['id']));
	$row=$qry->fetch();
	$qry->closeCursor();
	
	
	//detect <br> for wysiwyg transition from 2.9 to 3.0
	$findbr=stripos($row['text'], '<br>');
	if($findbr === false) {$text=nl2br($row['text']);} else {$text=$row['text'];}
	
	////////////////////////////////////////////////////////// START FORM VIEW OR MODIFY EXISTING PROCEDURE ///////////////////////////////////////////////////
	if($row['company_id']==$ruser['company'] || $rright['procedure_list_company_only']==0) //security check before display procedure
	{
		echo '
			<div class="page-header position-relative">
				<h1>
					<i class="icon-book"></i> '.T_('Procédure').' n°'.$row['id'].' : '.$row['name'].'
				</h1>
			</div>
			<fieldset>
				<div class="col-xs-12">
					<form method="POST" enctype="multipart/form-data" name="myform" id="myform" action="" onsubmit="loadVal();" >
						<label for="name">'.T_('Nom de la procédure').' :</label>
						<input name="name" size="50px" type="text" value="'.$row['name'].'" '; if($rright['procedure_modify']==0) {echo 'readonly="readonly"';} echo '>
						<br />
						<br />
						';
						if($rright['procedure_company']!=0)
						{
							echo '
							<label for="company">'.T_('Société').' :</label>
							<select name="company" onchange="">
								';
								$qry2=$db->prepare("SELECT `id`,`name` FROM `tcompany` WHERE disable='0' ORDER BY `name`");
								$qry2->execute();
								while($row2=$qry2->fetch()) 
								{
									if($_POST['company']==$row2['id'])
									{
										if($row2['id']==$_POST['company']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
									} elseif($row['company_id']==$row2['id']) 
									{
										echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
									} else {
										echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
									}
								}
								$qry2->closeCursor();
								echo '
							</select>
							<br />
							<br />
							';
						}
						echo '
						<label for="category">'.T_('Catégorie').' :</label>
						<select name="category" onchange="submit();" '; if($rright['procedure_modify']==0) {echo 'disabled="disabled"';} echo '>
							';
							$qry2=$db->prepare("SELECT `id`,`name` FROM `tcategory` ORDER BY `name`");
							$qry2->execute();
							while($row2=$qry2->fetch()) 
							{
								if($_POST['category'])
								{
									if($row2['id']==$_POST['category']) {echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';}
								} elseif($row['category']==$row2['id']) 
								{
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								} 
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
							$qry2->closeCursor(); 
							echo '
						</select>
						<br />
						<br />
						<label for="subcat">'.T_('Sous-catégorie').' :</label>
						<select name="subcat" '; if($rright['procedure_modify']==0) {echo 'disabled="disabled"';} echo '>
						   ';
							if($_POST['category'])
							{
								$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat LIKE :cat ORDER BY `name` ASC");
								$qry2->execute(array('cat' => $_POST['category']));
							} else {
								$qry2= $db->prepare("SELECT `id`,`name` FROM `tsubcat` WHERE cat LIKE :cat ORDER BY `name` ASC");
								$qry2->execute(array('cat' => $row['category']));
							}
							while($row2=$qry2->fetch()) 
							{
								if($_POST['subcat'])
								{
									if($row2['id']==$_POST['subcat'])
									{
										echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
									}
								} elseif($row['subcat']==$row2['id']) {
									echo '<option selected value="'.$row2['id'].'">'.$row2['name'].'</option>';
								}
								echo '<option value="'.$row2['id'].'">'.$row2['name'].'</option>';
							}
							$qry2->closeCursor();	
							echo '
						</select>
						<br /><br />
						';
						if($rright['procedure_modify']) {
							echo '
							<label for="procedure_file">'.T_('Joindre un fichier').' :</label>
							<input name="procedure_file"  type="file" style="display:inline" />
							<br /><br />
							';
						}
						
						//listing of attach file
						if(file_exists('./upload/procedure/'.$_GET['id'].'/')) {	
							if($handle = opendir('./upload/procedure/'.$_GET['id'].'/')) {
								while (false !== ($entry = readdir($handle))) {
									if($entry != "." && $entry != "..") {
										echo '
										<i class="icon-paperclip grey bigger-130"></i> 
										<a target="_blank" title="'.T_('Télécharger le fichier').' '.$entry.'" href="./upload/procedure/'.$_GET['id'].'/'.$entry.'">'.$entry.'</a>
										';
										if($rright['procedure_modify']!=0) {echo '<a href="./index.php?page=procedure&id='.$_GET['id'].'&action=edit&delete_file='.$entry.'" title="'.T_('Supprimer').'"<i class="icon-trash red bigger-130"></i></a>';}
										echo '
										<br />
										';
									}
								}
								closedir($handle);
							}
						}
						echo '<br />';
						if($rright['procedure_modify']==0) 
						{echo '<label for="procedure">'.T_('Procédure').' :</label><br /><br />'.$text;} 
						else
						{echo '<div id="editor" class="wysiwyg-editor">'.$text.'</div>';}
						echo '
						<input type="hidden" name="text" />
						<input type="hidden" name="text2" />
						<div class="form-actions align-right clearfix">
							<button name="return" value="return" id="return" type="submit" class="btn btn-danger">
								<i class="icon-undo bigger-110"></i>
								'.T_('Retour').'
							</button>
							';
							if($rright['procedure_modify']!=0) {
								echo '
								&nbsp;&nbsp;&nbsp;
								<button name="modif" value="modif" id="modif" type="submit" class="btn btn-success">
									<i class="icon-save bigger-110"></i>
									'.T_('Sauvegarder').'
								</button>
								';
							}
							echo '
						</div>
					</form>
				</div>
			</fieldset>			
		';
	} else {
		//display right error
		echo '<div class="alert alert-danger"><strong><i class="icon-remove"></i> '.T_('Erreur').':</strong> '.T_("Vous n'avez pas les droits d'accès à cette procédure. Contacter votre administrateur.").'<br></div>';
	}
	////////////////////////////////////////////////////////// END FORM MODIFY EXISTING PROCEDURE ///////////////////////////////////////////////////
} else {
	//////////////////////////////////////////////////////////////// START PROCEDURE LIST ///////////////////////////////////////////////////////////
	
	if($rright['procedure_list_company_only'])
	{
		//get name of company of current user
		$qry=$db->prepare("SELECT `name` FROM `tcompany` WHERE id=:id AND disable='0'");
		$qry->execute(array('id' => $ruser['company']));
		$company=$qry->fetch();
		$qry->closeCursor();
		$company=T_(' de la société ').$company['name'];
		
		//count procedure
		$qry=$db->prepare("SELECT COUNT(*) FROM `tprocedures` WHERE company_id=:company_id AND disable=0");
		$qry->execute(array('company_id' => $ruser['company']));
		$row=$qry->fetch();
		$qry->closeCursor();
	} else {
		$company='';
		$qry=$db->prepare("SELECT COUNT(*) FROM `tprocedures` WHERE disable='0'");
		$qry->execute();
		$row=$qry->fetch();
		$qry->closeCursor();
	}
	
	echo '
		<div class="page-header position-relative">
			<h1>
				<i class="icon-book"></i> 
				'.T_('Liste des procédures').$company.'
				<small>
					<i class="icon-double-angle-right"></i>
					&nbsp;'.T_('Nombre').': '.$row[0].' &nbsp;&nbsp;
				</small>
			</h1>
		</div>
	';

	//begin table
	echo '
	<table id="sample-table-1" class="table table-striped table-bordered table-hover">
			<thead>
				<tr>
					<th><i class="icon-circle"></i> '.T_('Numéro').'</th>
					<th><i class="icon-sign-blank"></i> '.T_('Catégorie').'</th>
					<th><i class="icon-sitemap"></i> '.T_('Sous-catégorie').'</th>
					<th><i class="icon-tag"></i> '.T_('Nom de la procédure').'</th>
					<th><i class="icon-play"></i> '.T_('Actions').'</th>
				</tr>
			</thead>
			<tbody>
				';
					//limit result to procedure of company of current connected user
					if($rright['procedure_list_company_only'])
					{
						$masterquery = $db->prepare("SELECT * FROM `tprocedures` WHERE `company_id`=:company_id AND `disable`='0' ORDER BY `category`,`subcat` ASC");
						$masterquery->execute(array('company_id' => $ruser['company']));
					} else {
						$masterquery = $db->prepare("SELECT * FROM `tprocedures` WHERE `disable`='0' ORDER BY `category`,`subcat` ASC");
						$masterquery->execute();
					}
					while ($row=$masterquery->fetch())
					{
						//get category name
						$qry=$db->prepare("SELECT `name` FROM `tcategory` WHERE id=:id");
						$qry->execute(array('id' => $row['category']));
						$rcat=$qry->fetch();
						$qry->closeCursor();
						
						//get sub-category name
						$qry=$db->prepare("SELECT `name` FROM `tsubcat` WHERE id=:id");
						$qry->execute(array('id' => $row['subcat']));
						$rscat=$qry->fetch();
						$qry->closeCursor();
						echo '
						<tr >	
							<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$row['id'].'</td>
							<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$rcat[0].'</td>
							<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$rscat[0].'</td>
							<td onclick="document.location=\'./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit\'" >'.$row['name'].'</td>
							<td>
								<div class="hidden-phone visible-desktop btn-group">	
									';
									//display actions buttons
									if($rright['procedure_modify']) {echo'<a style="margin: 0px 0px 0px 0px; height:23px; width:30px;" class="btn btn-minier btn-warning" href="./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=edit" title="'.T_('Modifier cette procédure').'" ><center><i class="icon-pencil white bigger-160"></i></center></a>';}
									if($rright['procedure_delete']) {echo'<a style="margin: 0px 0px 0px 5px; height:23px; width:30px;" class="btn btn-minier btn-danger" onClick="javascript: return confirm(\''.T_('Êtes-vous sur de vouloir supprimer cette procédure ?').'\');" href="./index.php?page=procedure&amp;id='.$row['id'].'&amp;action=delete" title="'.T_('Supprimer cette procédure').'" ><center><i class="icon-trash white bigger-160"></i></center></a>';}
									echo '
								</div>
							</td>
						</tr>
						';
					}
					$masterquery->closeCursor();
				echo '
			</tbody>
		</table>
	';
	//////////////////////////////////////////////////////////////// END PROCEDURE LIST ///////////////////////////////////////////////////////////
}
include ('./wysiwyg.php');
?>