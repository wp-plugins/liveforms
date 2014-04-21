<?php
// setup wordpress url prefix
$url = get_permalink(get_the_id());
$sap = strpos($url, "?") ? "&" : "?";
$purl = $url . $sap;
?>
<div class="w3eden">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-5">
				<button class="btn btn-disabled btn-bordered btn-block text-left">
					Form Name:
					<h3 style="margin: 10px 0;"><?php echo $form['title']; ?></h3>
				</button>
			</div>
			<div class="col-md-7 text-right">
				<div class="row btns">
					<div class="col-md-3"><button class="btn btn-primary btn-block showreqs" data-status="new"><h3 id="new" style="margin: 10px 0"><?php echo $counts['new'] ?></h3>New Entries</button></div>
					<div class="col-md-3"><button class="btn btn-success btn-block showreqs" data-status="inprogress"><h3 id="inprogress" style="margin: 10px 0"><?php echo $counts['inprogress'] ?></h3>In Progress</button></div>
					<div class="col-md-3"><button class="btn btn-warning btn-block showreqs" data-status="onhold"><h3 id="onhold" style="margin: 10px 0"><?php echo $counts['onhold'] ?></h3>On Hold</button></div>
					<div class="col-md-3"><button class="btn btn-default btn-block showreqs" data-status="resolved"><h3 id="resolved" style="margin: 10px 0"><?php echo $counts['resolved'] ?></h3>Resolved</button></div>
				</div>
			</div>

		</div><br/>
		<form id="reqform" method="post" action=''>
			<div class="panel panel-default">
				<div class="panel-heading">
					<b>Form Entries</b>
					<div class="pull-right" style="margin-top: -2px;margin-right: -3px">
						<button type="button" disabled="disabled" name="action" value="resolved" class="btn btn-xs btn-success"><i class="fa fa-check"></i> &nbsp;Resolve</button>
						<button type="submit" id="btn_delete" name="action" value="delete" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> &nbsp;Delete</button>
						<button type="button" disabled="disabled" name="action" value="onhold" class="btn btn-xs btn-warning"><i class="fa fa-clock-o"></i> &nbsp;Hold</button>
					</div>
				</div>
				<div class="panel-body np" id="form-entries">
					<table class='table table-striped table-hover'>
						<thead><tr><th><input id="fic" type='checkbox' /></th><th>Action</th><th>Token</th><th>Time</th>
									<?php
									foreach ($form_fields as $id => $field) {
										$fieldids[] = $id;
										echo "<th>{$field['label']}</th>";
									}
									?>
							</tr></thead><tbody>
							<?php
							foreach ($reqlist as $req) {
								$time = date('d-m-Y', $req['time']);
								echo "<tr id='fer_{$id}'><td><input type='checkbox' class='fic' name='ids[]' value='{$req['id']}' /></td><td><a href='{$purl}section=request&form_id={$form['id']}&req_id={$req['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> View</a></td><td>{$req['token']}</td><td>{$time}</td>";
								$req = unserialize($req['data']);
								foreach ($fieldids as $id) {
									$value = isset($req[$id]) ? $req[$id] : '';
									$value = is_array($value) ? implode(", ", $value) : $value;
									echo "<td>{$value}&nbsp;</td>";
								}
								echo "</tr>";
							}
							?>
						</tbody></table>
				</div>
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	jQuery(function($) {
		$('#fic').on('click', function() {
			if (this.checked)
				$('.fic').prop('checked', true);
			else
				$('.fic').prop('checked', false);
		});
		$('#fef').submit(function() {
			$(this).ajaxSubmit({
				beforeSubmit: function(reqs) {
				}
			});
			return false;
		});
		var options = {
			url: '<?php echo $purl ?>action=change_req_state&form_id=<?php echo $_REQUEST['form_id'] ?>&status=',
			reqstatus: 'new',
			newstatus: 'new',
			beforeSubmit: function() {
				$('#form-entries').prepend("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>");
			},
			success: function(response) {

				var jsonData = JSON.parse(response);

				if (jsonData['html'] != '') {
					$('#form-entries').html(jsonData['html']);
				}


				$('#' + this.reqstatus).html(jsonData['count']);
				$('#' + this.newstatus).html(jsonData['changed']);
			}
		}
		$('#reqform').on('submit', function() {
			var new_status = $('button[type=submit][clicked=true]').val();
			// Deep copy
			var current_options = jQuery.extend(true, {}, options);
			current_options.newstatus = new_status;
			current_options.url += new_status + '&query_status=' + current_options.reqstatus;

			$(this).ajaxSubmit(current_options);
			return false;
		});


		$('#reqform button[type=submit]').click(function() {
			$("button[type=submit]", $(this).parents("#reqform")).removeAttr("clicked");
			$(this).attr("clicked", "true");
		});
		$('.showreqs').on('click', function(e) {
			e.preventDefault();
			var status = $(this).attr('data-status');
			options.reqstatus = status;
			$('#form-entries').prepend("<div class='data-loading'><i class='fa fa-spinner fa-spin'></i> &nbsp; loading...</div>").load('<?php echo $purl; ?>section=stat_req&form_id=<?php echo $_REQUEST['form_id']; ?>&status=' + status, function() {
				window.history.pushState("", "Title", '<?php echo $purl; ?>section=stat_req&form_id=<?php
							echo $_REQUEST['form_id'];
							;
							?>&status=' + status);
				$('#fic').on('click', function() {
					if (this.checked)
						$('.fic').prop('checked', true);
					else
						$('.fic').prop('checked', false);
				});
			});
		});
	});
</script>