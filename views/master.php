<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>YOUTUBE API V3</title>
	<style>*,*:before,*:after{box-sizing:border-box;font-family:Calibri, sans-serif;margin:0;padding:0}a{text-decoration:none}a,a:active,a:focus,a:visited{color:#fff;outline:0}.btn{background:#f00;border-radius:0.4em;border:none;color:#fff;font-size:1.1em;font-weight:700;margin:0 0.5em;padding:0.5em 1em;text-transform:uppercase}.btn:active{background:#ea0000}body{background:#121212;color:#fff;padding-top:3em}.menu{background:#1a1a1a;height:3em;line-height:3em;position:fixed;top:0;left:0;width:100%}.menu-item{display:inline-block}.menu-item.brand a{background:#f00;color:#fff}.menu-item.brand a:hover,.menu-item.brand a.active{background:#f00}.menu-item a{color:#aaa;font-weight:700;padding:1em;text-transform:uppercase;transition:0.3s}.menu-item a:hover,.menu-item a.active{background:#121212;color:#fff}.main{padding:4em 1em 1em}.main form{padding-top:2em}.main .fixed-row{position:fixed;top:3em;left:0;width:100%;background:#121212;height:4em;line-height:4em;text-align:center}.list{display:flex;flex-wrap:wrap;justify-content:space-around}.list-item{list-style:none;padding:1em;width:213px}.list-item.form,.list-item:hover{background:#1a1a1a}.list-item.form{width:100%}.list-item.form .btn{width:inherit;margin:auto}.list-item .title{color:#fff;margin-bottom:1.5em}.list-item input[type=checkbox]{float:left}.list-item .btn{float:right}.pagination{margin:2em 0;text-align:center;list-style:none}.pagination li{display:inline-block}.row{float:left;padding:0 0.5em;width:50%}.form-item{margin:0.5em 0}.form-item .label{color:#aaa;margin-bottom:0.5em}.form-item input,.form-item textarea,.form-item select{width:100%;padding:0.3em;resize:none;border:none;background:#f1f1f1}.form-item textarea{height:16.5em}.msg{margin-bottom:2em;text-align:center}.notif{background:#1a1a1a;bottom:1em;color:#fff;display:none;padding:1em 2em 1em 1em;position:fixed;right:1em;width:20em}.notif.show{display:block;animation:3s ease-in-out forwards show}@keyframes show{0%{transform:translateY(10em)}10%,90%{transform:translateY(0)}100%{transform:translateY(10em)}}</style>
</head>
<body>
	<ul class="menu">
	  <li class="menu-item brand"><a href="javascript:void(0)">Youtube API</a></li>
	  <li class="menu-item"><a href="<?= baseUrl('video') ?>">videos</a></li>
	  <li class="menu-item"><a href="<?= baseUrl('playlist') ?>">playlists</a></li>
	  <li class="menu-item"><a href="<?= baseUrl('video/upload') ?>">upload video</a></li>
	</ul>
	<div class="main">
		<?php isset($content) && require $content ?>
	</div>
	<script>
		function MenuActivator(listItem) {
			this.listItem = document.querySelectorAll(listItem);
			this.url = window.location.pathname;

			this.getCurrentPage = function (listItem, url) {
				let segment = url.split('/');
				segment = segment[4] != undefined ? `${segment[3]}/${segment[4]}` : segment[3];
				segment = new RegExp(segment.replace(/\.(html?|php)/, '') + '$', 'i');
				for (let i = listItem.length - 1; i >= 0; i--) {
					if (segment.test(listItem[i].href)) return listItem[i];
				}
				return;
			}

			this.activate = function (activeClass) {
				let current = this.getCurrentPage(this.listItem, this.url),
					activated = document.querySelector(`${listItem}.${activeClass}`);
				try {
					if (!current) throw('cannot get the current page');
					!!activated && activated.classList.remove(activeClass);
					current.classList.add(activeClass);
				} catch(err) {
					console.error(err);
				}
			}
		}
		let menu = new MenuActivator('.menu-item a');
		menu.activate('active');
	</script>
</body>
</html>