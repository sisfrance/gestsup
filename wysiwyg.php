<!-- basic scripts -->
		<!--[if !IE]> -->
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='./template/assets/js/jquery-2.0.3.min.js'>"+"<"+"/script>");
		</script>
		<!-- <![endif]-->
		<!--[if IE]>
		<script type="text/javascript">
		 window.jQuery || document.write("<script src='./template/assets/js/jquery-1.10.2.min.js'>"+"<"+"/script>");
		</script>
		<![endif]-->
		
		<script src="./template/assets/js/bootstrap-wysiwyg.min.js"></script>
		<!-- ace scripts -->
		<script src="./template/assets/js/ace-elements.min.js"></script>
		
		<script type="text/javascript">
			//load text from editor to input value
			function loadVal(){
				<?php
				if ($rright['ticket_description']!=0 || $_GET['action']=='new')
				{
					echo '
					text = $("#editor").html();
					document.myform.text.value = text;
					';
				}
				if ($rright['ticket_thread_add']!=0)
				{
					echo '
					text2 = $("#editor2").html();
					document.myform.text2.value = text2;
					';
				}
				?>
			}
			
			jQuery(function($) {
				$('#editor').ace_wysiwyg({
					toolbar:
					[
						{
							name:'font',
							title:'Police',
							values:['Some Special Font!','Arial','Verdana','Comic Sans MS','Custom Font!']
						},
						null,
						{
							name:'fontSize',
							title:'Taille',
							values:{1 : 'Size#1 Text' , 2 : 'Size#1 Text' , 3 : 'Size#3 Text' , 4 : 'Size#4 Text' , 5 : 'Size#5 Text'} 
						},
						null,
						{name:'bold', title:'Gras'},
						{name:'italic', title:'Italique'},
						{name:'underline', title:'Sousligner'},
						null,
						{name:'insertunorderedlist', title:'Liste à puce'},
						{name:'insertorderedlist', title:'Liste numéroté'},
						{name:'outdent', title:'Augmenter le retrait'},
						{name:'indent', title:'Diminuer le retrait'},
						null,
						{name:'justifyleft',title:'Aligner à gauche'},
						{name:'justifycenter',title:'Aligner au centre'},
						{name:'justifyright',title:'Aligner à droite'},
						{name:'justifyfull',title:'Justifier'},
						null,
						{
							name:'insertImage',
							title:'Inserer une image',
							placeholder:'Inserer une image',
							button_class:'btn-inverse',
							//choose_file:false,//hide choose file button
							button_text:'Selectionner une image',
							button_insert_class:'btn-pink',
							button_insert:'Inserer une image'
						},
						null,
						{name:'foreColor',title:'Couleurs',values:['red','green','blue','orange','black'],},
						null,
						{name:'undo',title:'Annuler'},
						{name:'redo',title:'Rétablir'}
					],
					speech_button:false,//hide speech button on chrome
					
					'wysiwyg': {
						hotKeys : {} //disable hotkeys
					}
					
				}).prev().addClass('wysiwyg-style2');
				$('#editor2').ace_wysiwyg({
					toolbar:
					[
						{
							name:'font',
							title:'Police',
							values:['Some Special Font!','Arial','Verdana','Comic Sans MS','Custom Font!']
						},
						null,
						{
							name:'fontSize',
							title:'Taille',
							values:{1 : 'Size#1 Text' , 2 : 'Size#1 Text' , 3 : 'Size#3 Text' , 4 : 'Size#4 Text' , 5 : 'Size#5 Text'} 
						},
						null,
						{name:'bold', title:'Gras'},
						{name:'italic', title:'Italique'},
						{name:'underline', title:'Sousligner'},
						{name:'insertunorderedlist', title:'Liste à puce'},
						{name:'insertorderedlist', title:'Liste numéroté'},
						{name:'outdent', title:'Augmenter le retrait'},
						{name:'indent', title:'Diminuer le retrait'},
						null,
						{name:'justifyleft',title:'Aligner à gauche'},
						{name:'justifycenter',title:'Aligner au centre'},
						{name:'justifyright',title:'Aligner à droite'},
						{name:'justifyfull',title:'Justifier'},
						null,
						{
							name:'insertImage',
							title:'Inserer une image',
							placeholder:'Inserer une image',
							button_class:'btn-inverse',
							//choose_file:false,//hide choose file button
							button_text:'Selectionner une image',
							button_insert_class:'btn-pink',
							button_insert:'Inserer une image'
						},
						null,
						{name:'foreColor',title:'Couleurs',values:['red','green','blue','orange','black'],},
						null,
						{name:'undo',title:'Annuler'},
						{name:'redo',title:'Rétablir'}
					],
					speech_button:false,//hide speech button on chrome
					
					'wysiwyg': {
						hotKeys : {} //disable hotkeys
					}
					
				}).prev().addClass('wysiwyg-style2');
			});
		</script>
