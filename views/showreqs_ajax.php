<?php
// setup wordpress url prefix
$purl = '?';
$params = array('post_type', 'page', 'page_id');
foreach ($params as $param) {
	if (isset($_REQUEST[$param]))
		$purl .= "{$param}={$_REQUEST[$param]}&";
}
?>
<table class='table table-striped table-hover'>
	<thead><tr><th><input id="fic" type='checkbox' /></th><th>Action</th><th>Token</th>
				<?php
				foreach ($form_fields as $id => $field) {
					$fieldids[] = $id;
					echo "<th>{$field['label']}</th>";
				}
				?>
		</tr></thead><tbody>
		<?php
		foreach ($reqlist as $req) {
			echo "<tr id='fer_{$id}'><td><input type='checkbox' class='fic' name='ids[]' value='{$req['id']}' /></td><td><a href='{$purl}section=request&form_id={$form['id']}&req_id={$req['id']}' class='btn btn-info btn-xs'><i class='fa fa-eye'></i> View</a></td><td>{$req['token']}</td>";
			$req = maybe_unserialize($req['data']);
			foreach ($fieldids as $id) {
				$value = isset($req[$id]) ? $req[$id] : '';
				$value = is_array($value) ? implode(", ", $value) : $value;
				echo "<td>{$value}&nbsp;</td>";
			}
			echo "</tr>";
		}
		?>

	</tbody>
</table>
