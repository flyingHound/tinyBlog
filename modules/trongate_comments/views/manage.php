<h1>Trongate Comments</h1>
<?= flashdata('<p style="color:lime;">', '</p>') ?>
<p>
	<?php
	$attr['class'] = 'button';
	// echo anchor('trongate_comments/create', 'Create New Record <i class="fa fa-pencil"></i></button>', $attr);
	?>
</p>
<div class="table-responsive">
<table class="table table-bordered table-light table-striped table-sm table-hover">
	<caption>List of comments</caption>
	<thead class="table-dark">
		<tr>
			<th scope="row">ID</th>
			<th scope="row">Comment</th>
			<th scope="row">Created</th>
			<th scope="row">User ID</th>
			<th scope="row">Target Table</th>
			<th scope="row">Target ID</th>
			<th scope="row">Code</th>
			<th scope="row" style="width: 100px;">Action</th>
		</tr>
		
	</thead>
	<tbody id="records" class="table-group-divider">
		<?php
		foreach ($rows as $row) { ?>
			<tr>
				<th scope="row"><?= $row->id ?></td>
				<td><?= $row->comment ?></td>
				<td><?= date('y-m-d h:i:s', $row->date_created) ?></td>
				<td><?= $row->user_id ?></td>
				<td><?= $row->target_table ?></td>
				<td><?= $row->update_id ?></td>
				<td><?= $row->code ?></td>
				<td style="text-align: center;">
					<?php
					$edit_url = BASE_URL . 'trongate_comments/create/' . $row->id;
					// echo anchor($edit_url, 'Edit <i class="fa fa-pencil"></i></button>', $attr);
					?>
					<p>-</p>
				</td>
			</tr>
		<?php
		}
		?>
	</tbody>
</table>
</div>