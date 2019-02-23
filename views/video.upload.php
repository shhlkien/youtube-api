<form action="<?= baseUrl('video/upload') ?>" method="post">
	<div class="fixed-row">
		<input type="submit" value="go" class="btn">
		<span>Add to existed playlist</span>
		<select name="playlistId" id="playlistId">
			<option value="">Choose a playlist</option>
			<?php foreach ($list as $id => $title)
				echo $id != 'nextPageToken' ? sprintf('<option value="%s">%s</option>', $id, $title) : sprintf('<option value="%s">see more</option>', $title);
			?>
		</select>
		<span>Or create new</span>
		<input type="text" name="playlistTitle" placeholder="title">
		<input type="text" name="playlistDescription" placeholder="description">
		<select name="playlistPrivacy">
			<option value="private">private</option>
			<option value="public">public</option>
			<option value="unlisted">unlisted</option>
		</select>
	</div>
	<?php if (isset($msg)) echo sprintf('<div class="msg">%s</div>', $msg); ?>
	<ul class="list">
		<li class="list-item form">
			<div class="row">
				<div class="form-item">
					<p class="label">path</p>
					<input type="text" name="path[]"/>
				</div>
				<div class="form-item">
					<p class="label">title (optional)</p>
					<input type="text" name="title[]"/>
				</div>
				<div class="form-item">
					<p class="label">tags (optional)</p>
					<input type="text" name="tags[]"/>
				</div>
				<div class="form-item">
					<p class="label">category id</p>
					<input type="text" name="categoryId[]" value="27"/>
				</div>
				<div class="form-item">
					<p class="label">privacy</p>
					<select name="status[]">
						<option value="private">private</option>
						<option value="public">public</option>
						<option value="unlisted">unlisted</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="form-item">
					<p class="label">description (optional)</p>
					<textarea name="description[]"></textarea>
				</div>
			</div>
		</li>
		<li class="list-item form">
			<input class="btn" id="add" type="button" value="+"/>
		</li>
	</ul>
</form>
<script>
	document.getElementById('add').addEventListener('click', function() {
		let form = document.getElementsByClassName('form')[0].cloneNode(true);
		document.getElementsByClassName('list')[0].insertBefore(form, this.parentNode);
	});
</script>