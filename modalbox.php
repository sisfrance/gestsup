<?php
################################################################################
# @Name : modalbox.php
# @Description : display modalbox
# @Call : ticket, dashboard
# @Parameters : $boxtitle $boxtext $valid $cancel $action1 $action2
# @Author : Flox
# @Create : 19/10/2013
# @Update : 21/06/2019
# @Version : 3.1.42
################################################################################

//initialize variables 
if(!isset($boxtext)) $boxtext = '';
if(!isset($boxtitle)) $boxtitle = '';
if(!isset($boxsize)) $boxsize = '';
if(!isset($_GET['id'])) $_GET['id'] = '';
?>

<div id="dialog-confirm" >
	<div class="alert alert-info bigger-110">
		<?php echo "$boxtext"; ?>
	</div>
</div><!-- #dialog-confirm -->

<!-- datetime picker scripts  -->
<script src="./components/moment/min/moment.min.js" charset="UTF-8"></script>
<?php 
	if($ruser['language']=='fr_FR') {echo '<script src="./components/moment/locale/fr.js" charset="UTF-8"></script>';} 
	if($ruser['language']=='de_DE') {echo '<script src="./components/moment/locale/de.js" charset="UTF-8"></script>';} 
	if($ruser['language']=='es_ES') {echo '<script src="./components/moment/locale/es.js" charset="UTF-8"></script>';} 
?>
<script src="./components/datetimepicker/build/js/bootstrap-datetimepicker.min.js" charset="UTF-8"></script>

<!-- inline scripts related to this page date_start -->
<script type="text/javascript">
	jQuery(function($) {
		$('#event_date').datetimepicker({
			format: 'YYYY-MM-DD',
		});
		$('#calendar_date_start').datetimepicker({
			format: 'YYYY-MM-DD',
		});
		$('#calendar_date_end').datetimepicker({
			format: 'YYYY-MM-DD',
		});
	});
	
	jQuery(function($) {
		//override dialog's title function to allow for HTML titles
		$.widget("ui.dialog", $.extend({}, $.ui.dialog.prototype, {
			_title: function(title) {
				var $title = this.options.title || '&nbsp;'
				if( ("title_html" in this.options) && this.options.title_html == true )
					title.html($title);
				else title.text($title);
			}
		}));
		
		$( "#dialog-confirm" ).removeClass('hide').dialog({
			<?php echo $boxsize; ?>
			resizable: false,
			modal: true,
			title: "<div class='widget-header widget-header-small'><h4 class='smaller'><?php echo $boxtitle; ?></h4></div>",
			title_html: true,
			buttons: [
				{
					html: "<i class='icon-ok bigger-110'></i>&nbsp; <?php echo $valid; ?>",
					"class" : "btn btn-success btn-xs",
					click: function() {
						<?php echo $action1; ?>
					}
				}
				,
				{
					html: "<i class='icon-remove bigger-110'></i>&nbsp; <?php echo $cancel; ?>",
					"class" : "btn btn-danger btn-xs",
					click: function() {
						<?php echo $action2; ?> 

					}
				}
			]
		});
	});
</script>