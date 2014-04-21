<div class="w3eden">
    <div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-body np">
						<?php if (count($agent_forms) > 0) { ?>
							<table class="table table-striped" style="margin-bottom: 0">
								<thead>
								<th>Form ID</th>
								<th>Form Title</th>
								<th><div class='pull-right'>Action</div></th>
								</thead>
								<tbody>
									<?php
									$url = get_permalink(get_the_ID());
									$sap = strpos($url, "?") ? "&" : "?";

									foreach ($agent_forms as $form) {
										?>
										<tr>
											<td><?php echo $form['ID'] ?></td>
											<td><?php echo $form['post_title'] ?></td>
											<td><a href="<?php echo $url . $sap . "section=requests&form_id={$form['ID']}" ?>" class="btn btn-primary btn-xs ttip pull-right" title="Manage From"><i class='fa fa-desktop'></i></a></td>
										</tr>
									<?php } ?>

								</tbody>
							</table>
						<?php } else {
							?>
							No forms have been assigned to you
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>
<script>
	jQuery(function($) {
		$('.ttip').tooltip({placement: 'bottom'});
	});
</script>