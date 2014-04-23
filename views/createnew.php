<?php
if (isset($form_data)) {
	/**
	 * @variable    $checked_fields
	 * @uses        Contains list of fields that were checked
	 */
	$checked_fields = $form_data['fields'];
	/**
	 * @variable    $field_infos
	 * @uses        Contains info on each field of the form
	 */
	$field_infos = $form_data['fieldsinfo'];
} else
	$checked_fields = array();

?>
<?php
$fields = $commonfields;
?>
<!-- Preprocessing starts -->
<div class="w3eden"><br/>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-4">
                <input type="text" style="font-weight: 700" class="form-control text-center shortcode-code" readonly="readonly" value="<?php echo "[liveform form_id={$form_post_id}]" ?>"/><br/>

                <div class="panel-group" id="accordion">



					<div class="panel panel-default">
						<div class="panel-heading"><a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#commonfields">Commonly
								Used Fields</a></div>
						<div id="commonfields" class="panel-collapse collapse in">
							<div class="panel-body">
								<ul id="availablefields" class="list-group">
									<!-- Populating Common Fields list -->
									<?php foreach ($fields as $key => $data): ?>
										<li class="list-group-item" for="<?php echo $key; ?>">
											<span class="lfi lfi-<?php echo $key; ?>"></span> <?php echo $data['label']; ?>
											<a title="<?php echo $data['label']; ?>" rel="<?php echo $key; ?>"
											   class="add" <?php if (isset($data['options'])) echo 'data-options="1"'; ?>
											   href="#"><i class="glyphicon glyphicon-plus-sign pull-right ttipf" title=""></i></a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading"><a class="collapsed" data-toggle="collapse" data-parent="#accordion"
													  href="#genericfields">Generic
								Fields</a></div>
						<div id="genericfields" class="panel-collapse collapse">
							<div class="panel-body">
								<ul id="availablefields" class="list-group">
									<!-- Populating Generic Fields list -->
									<?php foreach ($generic_fields as $key => $data): ?>
										<li class="list-group-item" for="<?php echo $key; ?>">
											<span class="lfi lfi-<?php echo $key; ?>"></span> <?php echo $data['label']; ?>
											<a title="<?php echo $data['label']; ?>" rel="<?php echo $key; ?>"
											   class="add" <?php if (isset($data['options'])) echo 'data-options="1"'; ?>
											   href="#"><i class="glyphicon glyphicon-plus-sign pull-right ttipf" title=""></i></a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						</div>
					</div>
					<!-- Advanced fields start -->
					<div class="panel panel-default">
						<div class="panel-heading"><a class="collapsed" data-toggle="collapse" data-parent="#accordion"
													  href="#advancedfields">Advanced
								Fields</a></div>
						<div id="advancedfields" class="panel-collapse collapse">
							<div class="panel-body">
                                <ul class="list-group" id="availablefields">
                                    <!-- Populating Advanced Fields list -->
                                    <li for="file" class="list-group-item">
                                        <span class="lfi lfi-file"></span> File Upload											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="captcha" class="list-group-item">
                                        <span class="lfi lfi-captcha"></span> Captcha											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="fullname" class="list-group-item">
                                        <span class="lfi lfi-fullname"></span> Full name											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="address" class="list-group-item">
                                        <span class="lfi lfi-address"></span> Address											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="pageseparator" class="list-group-item">
                                        <span class="lfi lfi-pageseparator"></span> Page Separator											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Website											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Date											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Time											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Date Range											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Calendar											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Phone											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Countries ( + States )											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                    <li for="payment" class="list-group-item">
                                        <span class="lfi lfi-payment"></span> Payment methods											<a href="#" title="Available in pro only!"><i class="glyphicon glyphicon-info-sign pull-right ttipf text-warning" title="Available in pro only!"></i></a>
                                    </li>
                                </ul>
							</div>
						</div>
					</div>
					<!-- Advanced fields end -->
				</div>
			</div>
			<div class="col-md-8">


				<!-- <div class="form-group">
																<label for="title">Title: </label>
																<input type="text" name="contact[title]" class="form-control" id="title"
																		   value="<?php //echo isset($form_data) ? $form_data['title'] : "";                 ?>"/>
														</div>-->

				<div class="panel panel-default">
					<div class="panel-heading">Form Fields</div>
					<div class="panel-body">
						<ul id="selectedfields" class="list-group noborder">
							<?php if (isset($checked_fields)) { ?>
								<?php foreach ($checked_fields as $fieldindex => $fieldid): ?>
									<?php // Generating the 'fields' list from DB ?>
									<li class="list-group-item" id="field_<?php echo $fieldindex; ?>"><input type="hidden"
																											 name="contact[fields][<?php echo $fieldindex ?>]"
																											 id="<?php echo $key; ?>"
																											 value="<?php echo $fieldid; ?>">
										<span
											id="label_<?php echo $fieldindex; ?>"><?php echo strstr($fieldindex, 'pageseparator_') ? '[Separator] ' : '' ; echo $field_infos[$fieldindex]['label']; ?></span>
										<a href="#" rel="field_<?php echo $fieldindex; ?>" class="remove"><i
												class="glyphicon glyphicon-remove-circle pull-right"></i></a>
										<a href="#" class="cog-trigger" rel="#cog_<?php echo $fieldindex; ?>"><i
												class="glyphicon glyphicon-cog pull-right button-buffer-right"></i></a>

										<div class="cog" id="cog_<?php echo $fieldindex; ?>" style='display: none'>
											<fieldset>
												<h5>Settings</h5>
												<div class="form-group">
													<label><?php echo strstr($fieldindex, 'pageseparator_') ? 'Separator label' : 'Label' ?>:</label>
													<input class="form-control form-field-label"
														   data-target="#label_<?php echo $fieldindex; ?>" type="text"
														   value="<?php echo $field_infos[$fieldindex]['label'] ?>"
														   name="contact[fieldsinfo][<?php echo $fieldindex ?>][label]"
														   <?php echo strstr($fieldindex, 'pageseparator_') ? "data-field-type='separator'" : "" ?>/>
												</div>
												<div class="form-group">
													<label>Note</label>
													<textarea class="form-control" type="text" value=""
															  name="contact[fieldsinfo][<?php echo $fieldindex ?>][note]"><?php echo $field_infos[$fieldindex]['note'] ?></textarea>
												</div>
												<?php if (isset($field_infos[$fieldindex]['options'])): ?>
													<?php /*    Adding table of
													  Options -> Value IF
													  the field is
													  one of ('Select', 'Radio', 'Checkbox')
													  types
													 */
													?>
													<div class="form-group">
														<label>Options</label>
														<table class="options" id="option_<?php echo $fieldindex ?>">
															<?php for ($i = 0; $i < count($field_infos[$fieldindex]['options']['name']); $i++): ?>
																<tr>
																	<td><input type="text"
																			   name="contact[fieldsinfo][<?php echo $fieldindex; ?>][options][name][]"
																			   class="form-control" placeholder="Name"
																			   value="<?php echo $field_infos[$fieldindex]['options']['name'][$i] ?>"/>
																	</td>
																	<td><input type="text"
																			   name="contact[fieldsinfo][<?php echo $fieldindex; ?>][options][value][]"
																			   class="form-control" placeholder="Value"
																			   value="<?php echo $field_infos[$fieldindex]['options']['value'][$i] ?>"/>
																	</td>
																	<?php /* Each field has a +/- add/delet with
																	  it to help add or delete entry points
																	 */ ?>
																	<td>
																		<a href="#" class="add-option"
																		   rel="<?php echo $fieldindex ?>"><i
																				class="glyphicon glyphicon-plus-sign pull-left"></i></a>
																		<a href="#" class="del-option"
																		   rel="<?php echo $fieldindex ?>"><i
																				class="glyphicon glyphicon-minus-sign pull-left"></i></a>
																	</td>
																</tr>
															<?php endfor; ?>
														</table>
													</div>
												<?php endif; ?>
												<?php if (isset($field_infos[$fieldindex]['payment'])) { ?>
													<div class='form-group'>
														<div class='row'>
															<div class='col-md-6'>
																<label>Amount</label>
																<input type="text" class="form-control" value="<?php echo $field_infos[$fieldindex]['amount'] ?>" placeholder="Enter an amount here" name="contact[fieldsinfo][<?php echo $fieldindex ?>][amount]"/>
															</div>
															<div class='col-md-6'>
																<label>Currency</label>
																<?php $current_selection = $field_infos[$fieldindex]['currency']; ?>
																<select class='form-control' name="contact[fieldsinfo][<?php echo $fieldindex ?>][currency]">
																	<option value="none" <?php if ($current_selection == 'none') echo 'selected="selected"' ?>>Select a currency</option>
																	<?php foreach(currencies() as $value => $currency) { ?> 
																		<option <?php if ($current_selection == $value) echo 'selected="selected"' ?> value="<?php echo $value ?>"><?php echo $currency ?></option>
																	<?php } ?>
																</select>
															</div>
														</div>
													</div>
													<div class="form-group">
														<div class="checkboxes">
															<ul>
																<input type="hidden"
																	   name="contact[fieldsinfo][<?php echo $fieldindex ?>][payment][]"
																	   value="none"
																	   />
																	   <?php
																	   foreach ($methods_set as $value => $name) {
																		   ?>

																		<?php $payment_class = ucwords($value); ?>
																	<li style="list-style: none"><label><input type="checkbox"
																											   name="contact[fieldsinfo][<?php echo $fieldindex ?>][payment][]"
																											   value="<?php echo $value ?>" <?php if (in_array($value, $field_infos[$fieldindex]['payment'])) echo 'checked="checked"'; ?> class="payment-method-select" data-config-panel="<?php echo $value.'-'.$fieldindex ?>" <?php if (!class_exists($payment_class)) echo 'disabled="disabled"' ?>/> <?php echo $name ?>
																		</label>
																		<?php if (class_exists($payment_class)) : ?> 
																		<div id='configs-<?php echo $value.'-'.$fieldindex ?>' class='form-group <?php if (!in_array($value, $field_infos[$fieldindex]['payment'])) echo 'hidden'; ?>'>
																			<?php $payment = new $payment_class(); ?> 
																			<?php 
																			$fieldprefix = "contact[fieldsinfo][{$fieldindex}][paymethods]";
																			$cache = $field_infos[$fieldindex]['paymethods'][$payment_class];
																			echo $payment->ConfigOptions($fieldprefix, $cache);	?>
																		</div>
																		<?php endif ?>
																	</li>
																	<?php
																}
																?>
															</ul>
														</div>
													</div>
												<?php } ?>
												<?php if (isset($field_infos[$fieldindex]['fileinput'])) { ?>
													<input type='hidden'
														   name="contact[fieldsinfo][<?php echo $fieldindex ?>][fileinput]"/>
													<input type="file"
														   name="contact[fieldsinfo][<?php echo $fieldindex ?>][file][]"/>
													   <?php } ?>
												<div class="form-group">
													<label><input rel="req-params" class="req" type="checkbox"
																  name="contact[fieldsinfo][<?php echo $fieldindex ?>][required]"
																  value="1" <?php echo (isset($field_infos[$fieldindex]['required']) ? "checked=checked" : "") ?> />
														Required</label>

													<div
														class="req-params" <?php echo (!isset($field_infos[$fieldindex]['required']) ? "style='display: none'" : "") ?>>
														<input type="text"
															   name="contact[fieldsinfo][<?php echo $fieldindex ?>][reqmsg]"
															   placeholder="Field Required Message"
															   value="<?php echo $field_infos[$fieldindex]['reqmsg'] ?>"
															   class="form-control"/>
														<label>Validation:</label>
														<?php if ($checked_fields[$fieldindex] == "file") { ?>
															<div class="form-group">
																<label><input type="text"
																			  name="contact[fieldsinfo][<?php echo $fieldindex ?>][filesize]"
																			  placeholder="File size limit"
																			  value="<?php echo (isset($field_infos[$fieldindex]['filesize']) ? $field_infos[$fieldindex]['filesize'] : '') ?>"/>
																	Filesize</label>
																<label><input type="text"
																			  name="contact[fieldsinfo][<?php echo $fieldindex ?>][extensions]"
																			  placeholder="Allowed extensions"
																			  value="<?php echo (isset($field_infos[$fieldindex]['extensions']) ? $field_infos[$fieldindex]['extensions'] : '') ?>"/>
																	Extensions</label>
															</div>
														<?php } else if (false) { ?>
															<!-- For other blocks -->
														<?php } else { ?>
															<select
																name="contact[fieldsinfo][<?php echo $fieldindex ?>][validation]"
																class="form-control">
																	<?php
																	$validation_ops = array('text' => 'Text', 'numeric' => 'Numeric', 'email' => 'Email', 'url' => 'URL');
																	foreach ($validation_ops as $value => $text) {
																		echo '<option value="' . $value . '" ' . ($field_infos[$fieldindex]['validation'] == $value ? 'selected="selected "' : "") . '>' . $text . '</option>';
																	}
																	?>
															</select>
														<?php } ?>
													</div>
												</div>
											</fieldset>
										</div>
										<div class="field-preview">
											<?php
											$finfo = $field_infos[$fieldindex];
											$finfo['id'] = $fieldindex;
											if (isset($fields[$fieldid]))
												echo formfields::$fields[$fieldid]['type']($finfo);
											else if (isset($generic_fields[$fieldid]))
												echo formfields::$fieldid($finfo);
											/* Advanced field part start */
											else
												echo advancedfields::$fieldid($finfo);
											/* Advanced field part end */
											?>

										</div>
									</li>
								<?php endforeach; ?>
							<?php } ?>
						</ul>
						<div class="form-group">
							<button id="submit_button" type="submit" disabled="disabled"
									class="btn btn-default"><?php
										if (!isset($form_data['buttontext']) || $form_data['buttontext'] == '')
											echo "Submit";
										else
											echo $form_data['buttontext'];
										?></button>
						</div>
					</div>
				</div>

				<div class='form-group'>
					<div class="col-12">
						<label form="submit">Button label: </label>
						<input type="text" class="form-control" name="contact[buttontext]" placeholder="Submit button text"
							   id="buttontext" data-target="#submit_button"
							   value="<?php echo (isset($form_data['buttontext']) ? $form_data['buttontext'] : "") ?>"/>
                                                
						<label form="email">Thank you message: </label>
						<textarea rows="2" class="form-control" name="contact[thankyou]" placeholder="Thank you message"
								  id="thankyou"><?php echo (isset($form_data['thankyou']) ? $form_data['thankyou'] : "") ?></textarea>
                                                <br/>
                                                <b>Confirmation Email Settings</b>
                                                <hr/>
                                                <label form="email">From Email: </label>
						<input type="text" class="form-control" name="contact[email]" placeholder="Email address" id="email"
							   value="<?php echo (isset($form_data['email']) ? $form_data['email'] : "") ?>"/>
						<label form="email">From Name: </label>
						<input type="text" class="form-control" name="contact[from]" placeholder="Name to show in From field"
							   id="from" value="<?php echo (isset($form_data['from']) ? $form_data['from'] : "") ?>"/>                  
					</div>
				</div>

			</div>

		</div>
	</div>
</div>
<!-- Preprocessing ends -->
<!-- Teamplates start -->
<!--
    @script #template
    @uses   To populate 'Settings' panel with
            'Required message' and 'Validation'
            type list when [Required] is checked
    @access Mustache (Theme engine) via attr('id')
-->
<script type="text/x-mustache" id="template">
    <li class="list-group-item" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
        <span id="label_{{ID}}">{{title}}</span>
        <a href="#" rel="field_{{ID}}" class="remove"><i class="glyphicon glyphicon-remove-circle pull-right"></i></a>
        <a href="#" class="cog-trigger" rel="#cog_{{ID}}"><i class="glyphicon glyphicon-cog pull-right"></i></a>

        <div class="field-preview">
            {{fieldpreview}}
        </div>
        <div class="cog" id="cog_{{ID}}" style="display: none;">
            <fieldset>
                <h5>Settings</h5>

                <div class="form-group">
                    <label>Label:</label>
                    <input class="form-control form-field-label" data-target="#label_{{ID}}" type="text"
                           value="{{title}}"
                           name="contact[fieldsinfo][{{ID}}][label]"/>
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <textarea class="form-control" type="text" value=""
                              name="contact[fieldsinfo][{{ID}}][note]"></textarea>
                </div>
                <div class="form-group">
                    <label><input rel="req-params" class="req" type="checkbox"
                                  name="contact[fieldsinfo][{{ID}}][required]" value="1"/> Required</label>

                    <div class="req-params" style="display: none">
                        <input type="text" name="contact[fieldsinfo][{{ID}}][reqmsg]"
                               placeholder="Field Required Message" value="" class="form-control"/>
                        <label>Validation:</label>
                        <select name="contact[fieldsinfo][{{ID}}][validation]" class="form-control">
                            <option value="text">Text</option>
                            <option value="numeric">Numeric</option>
                            <option value="email">Email</option>
                            <option value="url">URL</option>
                            <option value="creditcard">Credit card</option>
                        </select>
                    </div>
                </div>
            </fieldset>
        </div>
    </li>
</script>
<!--
    @script #template-options
    @uses   To populate 'Settings' panel with
            'Required message','Validation'
            type list and 'Options' entry interface
            when [Required] is checked on 'Select',
            'Checkbox' and 'Radio' types
    @access Mustache (Theme engine) via attr('id')
-->
<script type="text/x-mustache" id="template-options">
    <li class="list-group-item" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
        <span id="label_{{ID}}">{{title}}</span>
        <a href="#" rel="field_{{ID}}" class="remove"><i class="glyphicon glyphicon-remove-circle pull-right"></i></a>
        <a href="#" class="cog-trigger" rel="#cog_select_{{ID}}"><i class="glyphicon glyphicon-cog pull-right"></i></a>

        <div class="field-preview">
            {{fieldpreview}}
        </div>
        <div class="cog" id="cog_select_{{ID}}" style="display: none;">
            <fieldset>
                <h5>Settings</h5>

                <div class="form-group">
                    <label>Label:</label>
                    <input class="form-control form-field-label" data-target="#label_{{ID}}" type="text"
                           value="{{title}}"
                           name="contact[fieldsinfo][{{ID}}][label]"/>
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <textarea class="form-control" type="text" value=""
                              name="contact[fieldsinfo][{{ID}}][note]"></textarea>
                </div>
                <div class="form-group">
                    <label>Options</label>

                    <table class="options" id="option_{{ID}}">
                        <tr>
                            <td><input type="text" name="contact[fieldsinfo][{{ID}}][options][name][]"
                                       class="form-control" placeholder="Name"/></td>
                            <td><input type="text" name="contact[fieldsinfo][{{ID}}][options][value][]"
                                       class="form-control" placeholder="Value"/></td>
                            <td>
                                <a href="#" class="add-option" rel="{{ID}}"><i
                                        class="glyphicon glyphicon-plus-sign pull-left"></i></a>
                                <a href="#" class="del-option" rel="{{ID}}"><i
                                        class="glyphicon glyphicon-minus-sign pull-left"></i></a>
                            </td>
                        </tr>
                    </table>
                    <label><input rel="req-params" class="req" type="checkbox"
                                  name="contact[fieldsinfo][{{ID}}][required]" value="1"/> Required</label>

                    <div class="req-params" style="display: none">
                        <input type="text" name="contact[fieldsinfo][{{ID}}][reqmsg]"
                               placeholder="Field Required Message" value="" class="form-control"/>
                        <label>Validation:</label>
                        <select name="contact[fieldsinfo][{{ID}}][validation]" class="form-control">
                            <option value="text">Text</option>
                            <option value="numeric">Numeric</option>
                            <option value="email">Email</option>
                            <option value="url">URL</option>
                            <option value="creditcard">Credit card</option>
                        </select>
                    </div>
                </div>
            </fieldset>
        </div>
    </li>
</script>
<!-- Advanced field part start -->
<script type="text/x-mustache" id="template-payment">
    <li class="list-group-item" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
        <span id="label_{{ID}}">{{title}}</span>
        <a href="#" rel="field_{{ID}}" class="remove"><i class="glyphicon glyphicon-remove-circle pull-right"></i></a>
        <a href="#" class="cog-trigger" rel="#cog_select_{{ID}}"><i class="glyphicon glyphicon-cog pull-right"></i></a>

        <div class="field-preview">
            {{fieldpreview}}
        </div>
        <div class="cog" id="cog_select_{{ID}}" style="display: none;">
            <fieldset>
                <h5>Settings</h5>

                <div class="form-group">
                    <label>Label:</label>
                    <input class="form-control form-field-label" data-target="#label_{{ID}}" type="text"
                           value="{{title}}"
                           name="contact[fieldsinfo][{{ID}}][label]"/>
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <textarea class="form-control" type="text" value=""
                              name="contact[fieldsinfo][{{ID}}][note]"></textarea>
                </div>
				<div class='form-group'>
					<div class='row'>
						<div class='col-md-6'>
							<label>Amount</label>
							<input type="text" class="form-control" value="" placeholder="Enter an amount here" name="contact[fieldsinfo][{{ID}}][amount]"/>
						</div>
						<div class='col-md-6'>
							<label>Currency</label>
							<select class='form-control' name="contact[fieldsinfo][{{ID}}][currency]">
								<option value="none" selected="selected">Select a currency</option>
								<?php foreach(currencies() as $value => $currency) { ?> 
									<option value="<?php echo $value ?>"><?php echo $currency ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</div>
                <div class="form-group">
                    <label>Options</label>

                    <div class="form-group">
                        <div class="checkboxes">
                            <ul>
                                <input type="hidden" name="contact[fieldsinfo][{{ID}}][payment][]" value="none"/>
                                <?php
                                foreach ($methods_set as $value => $name) {
                                    ?>

								<?php $payment_class = ucwords($value); ?>
                                    <li style="list-style: none">
										<label><input type="checkbox"
                                                                               name="contact[fieldsinfo][{{ID}}][payment][]"
                                                                               value="<?php echo $value ?>" data-config-panel="<?php echo $value ?>-{{ID}}" class="payment-method-select" <?php if (!class_exists($payment_class)) echo 'disabled="disabled"' ?>/> <?php echo $name ?>
																			   
																			   
                                        </label>
										<?php if (class_exists($payment_class)) { ?>
										<div id='configs-paypal-{{ID}}' class='form-group hidden'>
										<?php $payment = new $payment_class() ?> 
										<?php echo $payment->ConfigOptions($fieldprefix = 'contact[fieldsinfo][{{ID}}][paymethods]')	?>
										</div>
										
										<?php } ?>
									</li>
                                <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <label><input rel="req-params" class="req" type="checkbox"
                                  name="contact[fieldsinfo][{{ID}}][required]" value="1"/> Required</label>

                    <div class="req-params" style="display: none">
                        <input type="text" name="contact[fieldsinfo][{{ID}}][reqmsg]"
                               placeholder="Field Required Message" value="" class="form-control"/>
                        <label>Validation:</label>
                        <select name="contact[fieldsinfo][{{ID}}][validation]" class="form-control">
                            <option value="text">Text</option>
                            <option value="numeric">Numeric</option>
                            <option value="email">Email</option>
                            <option value="url">URL</option>
                            <option value="creditcard">Credit card</option>
                        </select>
                    </div>
                </div>
            </fieldset>
        </div>
    </li>
</script>
<script type="text/x-mustache" id="template-file">
    <!-- File uploader template -->
    <li class="list-group-item" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
        <span id="label_{{ID}}">{{title}}</span>
        <a href="#" rel="field_{{ID}}" class="remove"><i class="glyphicon glyphicon-remove-circle pull-right"></i></a>
        <a href="#" class="cog-trigger" rel="#cog_{{ID}}"><i class="glyphicon glyphicon-cog pull-right"></i></a>

        <div class="field-preview">
            {{fieldpreview}}
        </div>
        <div class="cog" id="cog_{{ID}}" style="display: none;">
            <fieldset>
                <h5>Settings</h5>

                <div class="form-group">
                    <label>Label:</label>
                    <input class="form-control form-field-label" data-target="#label_{{ID}}" type="text"
                           value="{{title}}"
                           name="contact[fieldsinfo][{{ID}}][label]"/>
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <textarea class="form-control" type="text" value=""
                              name="contact[fieldsinfo][{{ID}}][note]"></textarea>
                </div>
                <div class="form-group">
                    <input type='hidden' name="contact[fieldsinfo][{{ID}}][fileinput]"/>
                    <input type="file" name="contact[fieldsinfo][{{ID}}][file][]"/>
                </div>
                <div class="form-group">
                    <label><input rel="req-params" class="req" type="checkbox"
                                  name="contact[fieldsinfo][{{ID}}][required]" value="1"/> Required</label>

                    <div class="req-params" style="display: none">
                        <input type="text" name="contact[fieldsinfo][{{ID}}][reqmsg]"
                               placeholder="Field Required Message" value="" class="form-control"/>
                        <label>Validation:</label>

                        <div class="form-group">
                            <label><input type="text" name="contact[fieldsinfo][{{ID}}][filesize]"
                                          placeholder="File size limit" value=""/> Filesize</label>
                            <label><input type="text" name="contact[fieldsinfo][{{ID}}][extensions]"
                                          placeholder="Allowed extensions" value=""/> Extensions</label>
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
    </li>
</script>
<script type="text/x-mustache" id="template-separator">
    <!-- Separator template -->
    <li class="list-group-item" id="field_{{ID}}"><input type="hidden" name="contact[fields][{{ID}}]" value="{{value}}">
        <span id="label_{{ID}}">{{title}}</span>
        <a href="#" rel="field_{{ID}}" class="remove"><i class="glyphicon glyphicon-remove-circle pull-right"></i></a>
        <a href="#" class="cog-trigger" rel="#cog_{{ID}}"><i class="glyphicon glyphicon-cog pull-right"></i></a>

        <div class="field-preview">
            {{fieldpreview}}
        </div>
        <div class="cog" id="cog_{{ID}}" style="display: none;">
            <fieldset>
                <h5>Settings</h5>
                <div class="form-group">
                    <label>Text/HTML:</label>
                    <input class="form-control form-field-label" data-target="#label_{{ID}}" data-field-type="separator" type="text"
                           value="{{title}}"
                           name="contact[fieldsinfo][{{ID}}][label]"/>
                </div>
                <div class="form-group">
                    <label>Note</label>
                    <textarea class="form-control" type="text" value=""
                              name="contact[fieldsinfo][{{ID}}][note]"></textarea>
                </div>
            </fieldset>
        </div>
    </li>
</script>
<!-- Advanced field part end -->
<!-- Teamplates end -->
<!-- Engine functions start -->
<script type="text/javascript">
function add_field(obj, position) {
    //Add field with form
    if (jQuery(obj).attr('data-options') != undefined)
        var tmp = jQuery("#template-options").html();
    else if (jQuery(obj).attr('data-template') != undefined)
        var tmp = jQuery("#template-" + jQuery(obj).attr('data-template')).html();
    /*else if (jQuery(this).attr('data-payment') != undefined)
     var tmp = jQuery("#template-payment").html();*/
    else
        var tmp = jQuery("#template").html();

    var ID = obj.attr('rel') + "_" + new Date().getTime();
    position.html(Mustache.render(tmp, {title: obj.attr('title'), value: obj.attr('rel'), ID: ID}));
    position.unwrap();
    //jQuery('#selectedfields li.ui-draggable').after(Mustache.render(tmp, {title: obj.attr('title'), value: obj.attr('rel'), ID: ID}));
    //position.remove();

    jQuery('.form-field-label').on('keyup', function () {
        jQuery(jQuery(this).attr('data-target')).html(jQuery(this).val());
    });

    /**/
    jQuery('.cog-trigger').unbind();
    jQuery('.cog-trigger').on('click', function () {
        jQuery(this.rel).slideToggle();
        return false;
    });

    /**/
    jQuery('.req').unbind();
    jQuery('.req').on('click', function () {
        jQuery(this).parent().next('.req-params').slideToggle();

    });

    jQuery('.remove').unbind();
    jQuery('.remove').on('click', function () {
        if (confirm('Are you sure?')) {
            jQuery('#' + this.rel).slideUp(function () {
                jQuery(this).remove();
            });
        }
        return false;
    });

    jQuery('.add-option').unbind();
    jQuery('.add-option').click(function () {
        jQuery("#option_" + this.rel + " tbody").append(jQuery("#option_" + this.rel + " tr:last-child").clone(function () {

            /* Re-bind click event with delete option button */
            jQuery('.del-option').unbind();
            jQuery('.del-option').click(function () {
                console.log('#options_' + this.rel);
                //console.log(jQuery('#options_'+this.rel+" tr").html());
                if (jQuery('#options_' + this.rel + " tr").length > 2)
                    jQuery(this).parent().parent().remove();
                else
                    alert("Can't be delete. Atleast One Option is required.");
                return false;
            });


        }));

        return false;
    });

    jQuery('.del-option').unbind();
    jQuery('.del-option').click(function () {
        if (jQuery('#option_' + this.rel + " tr").length > 1)
            jQuery(this).parent().parent().remove();
        else
            alert("Can't be delete. Atleast One Option is required.");
        return false;
    });

    return false;
}


jQuery(function ($) {

//    $('#availablefields li').draggable({
//        connectToSortable: "#selectedfields",
//        helper: "clone",
//        revert: "invalid"
//    });

    
//    $("#selectedfields").droppable({
//        activeClass: "ui-state-highlight",
//        drop: function (event, ui) {
//            var position = ui.draggable;
//            var obj = position.find("a.add");
//            add_field(obj, position);
//            //return false;
//        }
//    });
	$("#selectedfields").sortable();
    $('#availablefields .add').click(function () {
        //Add field with form
        if ($(this).attr('data-options') != undefined)
            var tmp = $("#template-options").html();
        else if ($(this).attr('data-template') != undefined)
            var tmp = $("#template-" + $(this).attr('data-template')).html();
		else if ($(this).attr('data-separator') != undefined) 
            var tmp = $("#template-" + $(this).attr('data-separator')).html();
		
        /*else if ($(this).attr('data-payment') != undefined)
         var tmp = $("#template-payment").html();*/
        else
            var tmp = $("#template").html();


        var ID = this.rel + "_" + new Date().getTime();

        $('#selectedfields').append(Mustache.render(tmp, {title: this.title, value: this.rel, ID: ID}));

		$('.payment-method-select').unbind();
		$('.payment-method-select').on('change', function(){
			div = $(this).attr('data-config-panel');
			console.log($('#configs-'+div));
			if ($('#configs-'+div).hasClass('hidden')) {
				$('#configs-'+div).removeClass('hidden');
			} else {
				$('#configs-'+div).addClass('hidden');
			}
		});

        $('.form-field-label').on('keyup', function () {
			if ($(this).attr('data-field-type') == 'separator') {
				$($(this).attr('data-target')).html('(Separator) '+$(this).val());
			} else {
				$($(this).attr('data-target')).html($(this).val());
			}
        });


        /**/
        $('.cog-trigger').unbind();
        $('.cog-trigger').on('click', function () {
            $(this.rel).slideToggle();
            return false;
        });

        /**/
        $('.req').unbind();
        $('.req').on('click', function () {
            $(this).parent().next('.req-params').slideToggle();

        });

        $('.remove').unbind();
        $('.remove').on('click', function () {
            if (confirm('Are you sure?')) {
                $('#' + this.rel).slideUp(function () {
                    $(this).remove();
                });
            }
            return false;
        });

        $('.add-option').unbind();
        $('.add-option').click(function () {
            $("#option_" + this.rel + " tbody").append($("#option_" + this.rel + " tr:last-child").clone(function () {
                /* Re-bind click event with delete option button */
                $('.del-option').unbind();
                $('.del-option').click(function () {
                    console.log('#options_' + this.rel);
                    //console.log($('#options_'+this.rel+" tr").html());
                    if ($('#options_' + this.rel + " tr").length > 2)
                        $(this).parent().parent().remove();
                    else
                        alert("Can't be delete. Atleast One Option is required.");
                    return false;
                });
            }));
            return false;
        });

        $('.del-option').unbind();
        $('.del-option').click(function () {
            if ($('#option_' + this.rel + " tr").length > 1)
                $(this).parent().parent().remove();
            else
                alert("Can't be delete. Atleast One Option is required.");
            return false;
        });
        return false;
    });

    $('.cog-trigger').on('click', function () {
        $(this.rel).slideToggle();
        return false;
    });

    $('.req').on('click', function () {
        $(this).parent().next('.req-params').slideToggle();
        //return false;
    });

    $('.req').each(function () {
        if ($(this).attr('checked') == 'checked')
            $(this).parent().next('.req-params').slideDown();
        //return false;
    });
    $('.remove').on('click', function () {
        if (confirm('Are you sure?')) {
            $('#' + this.rel).slideUp(function () {
                $(this).remove();
            });
        }
        return false;
    });
    $('.form-field-label').on('keyup', function () {
		console.log($(this).attr('data-field-type'));
		if ($(this).attr('data-field-type') == 'separator') {

			$($(this).attr('data-target')).html('[Separator] '+$(this).val());
		} else {
			$($(this).attr('data-target')).html($(this).val());
		}
    });

    $('#buttontext').on('keyup', function () {
        $($(this).attr('data-target')).html($(this).val());
    });

    $('.add-option').click(function () {
        $("#option_" + this.rel + " tbody").append($("#option_" + this.rel + " tr:last-child").clone(function () {

            /* Re-bind click event with delete option button */
            $('.del-option').unbind();
            $('.del-option').click(function () {
                //console.log($('#options_'+this.rel+" tr").html());
                if ($('#options_' + this.rel + " tr").length > 2)
                    $(this).parent().parent().remove();
                else
                    alert("Can't be delete. Atleast One Option is required.");
                return false;
            });
        }));
        return false;
    });

    $('.del-option').click(function () {
        if ($('#option_' + this.rel + " tr").length > 1)
            $(this).parent().parent().remove();
        else
            alert("Can't be delete. Atleast One Option is required.");
        return false;
    });

    $('#selectedfields').hover(function () {
        $(this).removeClass('noborder');
    }, function () {
        $(this).addClass('noborder');
    });

	$('.payment-method-select').on('change', function(){
		div = $(this).attr('data-config-panel');
		console.log('#configs-'+div);
		if ($('#configs-'+div).hasClass('hidden')) {
			$('#configs-'+div).removeClass('hidden');
		} else {
			$('#configs-'+div).addClass('hidden');
		}
	});

    $('.field-preview input,.field-preview select,.field-preview textarea').attr('disabled', 'disabled');

});

</script>
<!-- Engine functions end -->
