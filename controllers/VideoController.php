<?php

class VideoController {

	static $vid = null;

	function __construct()
	{
		require 'core/Video.php';
		if (self::$vid === null) self::$vid = new Video();
		if ( !self::$vid->isLogin() ) {
			// self::$vid->revokeToken();
			self::$vid->authorize();
		}
	}

	public function index($pageToken)
	{
		$list = self::$vid->listVideos($pageToken[2]);
		$content = 'views/video.index.php';
		
		require 'views/master.php';
	}

	public function listPlaylists($pageToken = null)
	{
		require_once 'core/Playlist.php';
		$playlist = new Playlist();
		$list = $playlist->listPlaylists($pageToken, 5);
		$res = [];

		for ($i = count($list['items']) - 1; $i >= 0; --$i)
			$res[ $list['items'][$i]['id'] ] = $list['items'][$i]['snippet']['title'];
		if ( isset($list['nextPageToken']) ) $res['nextPageToken'] = $list['nextPageToken'];

		return $res;
	}

	public function update($config)
	{
		if ( isset($config['id']) ) {
			if ( !empty($config['tags']) ) $config['tags'] = explode(',', $config['tags']);
			self::$vid->update($config);
		}
		// if uses post then you have 'id' else uses get
		$video = self::$vid->get($config['id'] ?? $config[2]);
		if ( isset($video['snippet']['tags']) ) $video['snippet']['tags'] = implode(', ', $video['snippet']['tags']);

		if ( isset(self::$vid->messages['error']) )
			$msg = self::$vid->messages['error'];
		elseif ( isset(self::$vid->messages['success']) )
			$msg = self::$vid->messages['success'];

		$content = 'views/video.update.php';
		require 'views/master.php';
	}

	public function upload($config)
	{
		if ($config != null) {

			if ( count($config['path']) == 1 ) {
				$i = 0;
				foreach (glob($config['path'][0]) as $f) {
					$config[$i]['path'] = $f;
					$config[$i]['title'] = $config['title'][0];
					$config[$i]['status'] = $config['status'][0];
					$config[$i]['description'] = empty( $config['description'][$i] ) ? '' : $config['description'][$i];
					$config[$i]['tags'] = empty( $config['tags'][$i] ) ? '' : explode(',', $config['tags'][$i] );
					$config[$i++]['categoryId'] = empty( $config['categoryId'][0] ) ? 27 : $config['categoryId'][0];
				}
			}
			else {
				for ($i = count($config['path']) - 1; $i >= 0; --$i) {
					$config[$i]['path'] = $config['path'][$i];
					$config[$i]['title'] = $config['title'][$i];
					$config[$i]['description'] = empty( $config['description'][$i] ) ? '' : $config['description'][$i];
					$config[$i]['tags'] = empty( $config['tags'][$i] ) ? '' : explode(',', $config['tags'][$i] );
					$config[$i]['categoryId'] = empty( $config['categoryId'][$i] ) ? 27 : $config['categoryId'][$i];
					$config[$i]['status'] = $config['status'][$i];
				}
			}
			
			unset($config['path'], $config['title'], $config['categoryId'], $config['status'], $config['description'], $config['tags']);
			// echo '<pre>';
			// print_r ($config);
			// die;

			require_once 'core/Playlist.php';
			$playlist = new Playlist();

			if ( !empty($config['playlistId']) )
				$playlistId = $config['playlistId'];
			elseif ( !empty($config['playlistTitle']) ) {
				$playlistConfig = [
					'title' => $config['playlistTitle'],
					'description' => empty($config['playlistDescription']) ? '' : $config['playlistDescription'],
					'privacy' => $config['playlistPrivacy']
				];
				$playlistId = $playlist->create($playlistConfig);
				// echo '<pre>';
				// var_dump($playlist);
			}
			// die;
			// var_dump($playlistId);die;

			unset($config['playlistId'], $config['playlistTitle'], $config['playlistDescription'], $config['playlistPrivacy']);

			if (count($config) == 1) {
				$videoId = self::$vid->upload($config[0]);
				$playlist->addToPlaylist($videoId, $playlistId);
			}
			else {
				for ($i = 0; $i < count($config); ++$i) {
					$videoId = self::$vid->upload($config[$i]);
					$playlist->addToPlaylist($videoId, $playlistId);
				}
			}
		}

		if ( isset(self::$vid->messages['error']) )
			$msg = self::$vid->messages['error'];
		elseif ( isset(self::$vid->messages['success']) ) {

			$msg = '';
			for ($i = count(self::$vid->messages['success']) - 1; $i >= 0; --$i) {
				$msg = sprintf('%s<a href="%s" target="blank" class="btn">%s</a>', $msg, self::$vid->messages['success'][$i]['link'], self::$vid->messages['success'][$i]['status']);
			}
		}

		$list = $this->listPlaylists();
		$content = 'views/video.upload.php';

		require 'views/master.php';
	}

	public function delete($id)
	{
		self::$vid->delete(json_decode($id[2]));
		(http_response_code() == 401) && self::$vid->authorize();
		echo json_encode(self::$vid->messages);
	}
}