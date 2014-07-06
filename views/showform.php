 
	<?php
	/**
	 * @variable    $formsetting
	 * @uses        Contains form element configurations
	 * @origin      Controller: contactforms, Method: showform()
	 */
	$url = get_permalink(get_the_ID());
	$sap = strpos($url, "?") ? "&" : "?";
	$purl = $url . $sap;
	?>
    <div class="w3eden">
		<div class="crumbstrail">
			<div class="crumbs">
				<ul>
					<?php if (count($form_parts_names) > 1) { ?>
						<?php foreach ($form_parts_names as $index => $crumb_text) { ?>
							<li id='<?php echo $index . "_crumb" ?>' class='breadcrumbli <?php if ($index == "form_part_0") echo "active visited" ?>' data-part="<?php echo $index ?>"><a disabled='disabled' class="breadcrumbs" href="" onclick='return false'><?php echo $crumb_text ?></a></li>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>	
		</div>
        <div class="container-fluid">
			<div id="method">

			</div>
            <div id="formarea">

                <form id="form"  action="" method="post" enctype="multipart/form-data" >
                    <input type="hidden" id="formid" name="form_id" value="<?php echo $form_id ?>" />
					<?php

                    //do something
                    do_action('liveform-showform_before_form_fields', $form_id);

					foreach ($form_parts_html as $part)
						echo $part;

                    //do something
                    do_action('liveform-showform_after_form_fields', $form_id);
					?>
                </form>
            </div>
        </div>
    </div>

    <script type='text/javascript'>
		jQuery(function($) {
			var submit_btn_text;
			var next_part_id;
			var this_part_id;
			$(document).ready(function() { //code 
				// Show hard form partitions
				var set_show = {display: 'block'};
				var set_hide = {display: 'none'};
				var validator = $('#form').validate({
				});
				var validInput = true;

				//$('#form_part_0').css(set_show);
				$('.change-part').on('click', function() {
					next_part_id = $(this).attr('data-next');
					this_part_id = $(this).attr('data-parent');


					// Pre validate
					validInput = true;
					var $inputs = $('#' + this_part_id).find("input");
					$inputs.each(function() {
						if (!validator.element(this) && validInput) {
							validInput = false;
                            $(this).parent('.form-group').removeClass('has-success').addClass('has-error');
						} else {
                            if(!validator.element(this))
                                $(this).parent('.form-group').removeClass('has-success').addClass('has-error');
                            else
                            $(this).parent('.form-group').removeClass('has-error').addClass('has-success');
                        }
					});

					if (validInput == true) {
						if (next_part_id != undefined) {
							$('#' + this_part_id).css(set_hide);
							$('#' + next_part_id).css(set_show);
						}
						$('#' + next_part_id + '_crumb').addClass('active');
						$('#' + next_part_id + '_crumb').addClass('visited');
						$('#' + this_part_id + '_crumb').removeClass('active');
                        $(this).parent('.form-group').removeClass('has-error').addClass('has-success');

					} else {
						msgs = new Array();
						msgs.push("Please fill all required fields properly");
						showAlerts(msgs, 'danger');
					}
				});

				$('.breadcrumbs').on('click', function() {
					var set_show = {display: 'block'};
					var set_hide = {display: 'none'};
					show_part_id = $(this).parent().attr('data-part');
					hide_part_id = $('.breadcrumbli.active').attr('data-part');
					if ($('#' + show_part_id + '_crumb').hasClass('visited')) {
						$('.breadcrumbli.active').removeClass('active');
						$(this).parent().addClass('active');
						$('#' + hide_part_id).css(set_hide);
						$('#' + show_part_id).css(set_show);
					} else {
						// Show the error
						msgs = new Array();
						msgs.push('Fill the current area to proceed');
						showAlerts(msgs, 'danger');
					}

				});

				// validation using jquery


				// ajax submit
				var options = {
					url: '<?php echo $purl ?>action=submit_form',
					resetForm: true,
					beforeSubmit: function() {
						submit_btn_text = $('#submit').html();
						$('#submit').html("<i id='spinner' class='fa fa-spinner fa-spin'></i> Please wait");
					}, // pre-submit callback
					success: function(response) {
						//console.log(response);
                         alert(response);
						msgs = new Array();
						$('#spinner').remove();
						$('#submit').html(submit_btn_text);
						$('#'+this_part_id).css(set_hide);
						$('#form_part_0').css(set_show);
						try {
							response_vars = JSON.parse(response);
						} catch (e) {
							console.log(e);
						}
						if (response_vars.action == 'success' && validInput === true) {
							msgs.push(response_vars.message);
							showAlerts(msgs, 'success');
						} else {
							if(typeof(response_vars) != 'undefined' && response_vars.action=='payment'){
								msgs.push('<?php echo (!isset($formsetting['thankyou']) || $formsetting['thankyou'] == '' ? 'Form submitted succesfully' : $formsetting['thankyou']) ?>');
								showAlerts(msgs, 'success');
								console.log(response_vars.paymentform);
								$('#method').html(response_vars.paymentform);
							} else {
								msgs.push('Form submission failed, please check the entries again');
								showAlerts(msgs, 'danger');
							}
						}
					}
				};
				$('#form').on('submit', function() {
					if (validInput == true) {
						$(this).ajaxSubmit(options);
					}
					return false;
				});
			});
		});

		function showAlerts(msgs, type) {
            jQuery('.formnotice').slideUp();
			alert_box = '<div style="margin-top: 20px" class="alert formnotice alert-' + type + ' disappear"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
			for (i = 0; i < msgs.length; i++) {
				alert_box += '' + msgs[i] + '<br/>';
			}
			alert_box += '</div>';
			jQuery('#form').append(alert_box);

		}
    </script>
