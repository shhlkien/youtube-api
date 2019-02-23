<form action="<?= baseUrl('video/update') ?>" method="post">
	<div class="fixed-row"><input type="submit" value="go" class="btn"></div>
	<?php if (isset($msg)) echo sprintf('<div class="msg">%s</div>', $msg); ?>
	<ul class="list">
		<li class="list-item form">
			<div class="row">
				<input type="hidden" name="id" value="<?= $video['id'] ?>">
				<div class="form-item">
					<p class="label">title (optional)</p>
					<input type="text" value="<?= $video['snippet']['title'] ?>" name="title"/>
				</div>
				<div class="form-item">
					<p class="label">tags (optional)</p>
					<input type="text" value="<?= $video['snippet']['tags'] ?>" name="tags"/>
				</div>
				<div class="form-item">
					<p class="label">category id</p>
					<input type="text" name="categoryId" value="<?= $video['snippet']['categoryId'] ?>"/>
				</div>
				<div class="form-item">
					<p class="label">privacy</p>
					<select name="privacy">
						<?php 
						$privacy = ['private', 'public', 'unlisted'];
						for ($i = count($privacy) - 1; $i >= 0; --$i) {
							echo sprintf('<option value="%1$s"%2$s>%1$s</option>', $privacy[$i],
								$video['status']['privacyStatus'] == $privacy[$i] ? ' selected': null);
						}
						?>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="form-item">
					<p class="label">description (optional)</p>
					<textarea name="description"><?= $video['snippet']['description'] ?></textarea>
				</div>
			</div>
		</li>
	</ul>
</form>