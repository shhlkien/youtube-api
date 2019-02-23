<?php

class PlaylistController
{
	static $playlist = null;

	function __construct()
	{
		require 'core/Playlist.php';
		if (self::$playlist === null) self::$playlist = new Playlist();
		if ( !self::$playlist->isLogin() ) self::$playlist->authorize();
	}

	public function index($pageToken)
	{
		$list = self::$playlist->listPlaylists($pageToken[2]);
		$content = 'views/list.index.php';

		require 'views/master.php';
	}

	public function test()
	{
		$playlistId = self::$playlist->create([
			'title' => 'test playlist 5',
			'description' => '',
			// 'privacy' => 'public'
		]);
		self::$playlist->addToPlaylist(['SZj6rAYkYOg'], $playlistId);

		echo sprintf('<a target="blank" href="%s">view</a>', self::$playlist->messages['success']);
	}

	public function delete($id)
	{
		self::$playlist->delete(json_decode($id[2]));
		(http_response_code() == 401) && self::$playlist->authorize();
		echo json_encode(self::$playlist->messages);
	}
}