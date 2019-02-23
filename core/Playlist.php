<?php require_once 'YouTubeAPI.php';

class Playlist extends YouTubeAPI {

	function __construct()
	{
		parent::__construct();
		$this->authorize();
	}

	public function addToPlaylist($videoId, $playlistId)
	{
		$resourceId = new Google_Service_YouTube_ResourceId();
		$resourceId->setVideoId($videoId);
		$resourceId->setKind('youtube#video');

		$playlistItemSnippet = new Google_Service_YouTube_PlaylistItemSnippet();
		$playlistItemSnippet->setPlaylistId($playlistId);
		$playlistItemSnippet->setResourceId($resourceId);

		$playlistItem = new Google_Service_YouTube_PlaylistItem();
		$playlistItem->setSnippet($playlistItemSnippet);
		$playlistItemResponse = $this->youtube->playlistItems->insert('snippet', $playlistItem);

		$this->messages['success'] = sprintf('https://www.youtube.com/playlist?list=%s', $playlistId);
	}

	public function create($config)
	{
		try {
			$playlistSnippet = new Google_Service_YouTube_PlaylistSnippet();
			$playlistSnippet->setTitle($config['title']);
			$playlistSnippet->setDescription($config['description']);

			$playlistStatus = new Google_Service_YouTube_PlaylistStatus();
			$playlistStatus->setPrivacyStatus($config['privacy'] ?? 'private');

			$youTubePlaylist = new Google_Service_YouTube_Playlist();
			$youTubePlaylist->setSnippet($playlistSnippet);
			$youTubePlaylist->setStatus($playlistStatus);

			$playlistResponse = $this->youtube->playlists->insert('snippet,status', $youTubePlaylist);

			return $playlistResponse['id'];

		} catch (Google_Service_Exception $e) {
			$this->errorHandler($e);
		} catch (Google_Exception $e) {
			$this->errorHandler($e);
		}
	}

	/**
	 * @method delete
	 * @param  [mixed] $id an array of ids or a single id
	 */
	public function delete($id)
	{
		try {
			if ( !is_array($id) )
				$this->youtube->playlists->delete($id);
			else {
				for ($i = count($id) - 1; $i >= 0; --$i) {
					$this->youtube->playlists->delete($id[$i]);
				}
			}
			$this->messages = ['success' => 'Done!'];

		} catch (Google_Service_Exception $e) {
			$this->errorHandler($e);
		} catch (Google_Exception $e) {
			$this->errorHandler($e);
		}
	}

	public function listPlaylists($pageToken, $maxResults = 25)
	{
		// $this->authorize(); // must authorize to init new $youtube object, I don't know why???
		// $this->client = new Google_Client();
		// $youtube = new Google_Service_YouTube($this->client);
		// echo '<pre>';
		// var_dump ($_SESSION, $this->client, $youtube);
		// die;
		return $this->youtube->playlists->listPlaylists('snippet', [
			'channelId' => 'UC-DPvvl6eKLsoewWLtNgAOQ',
			'maxResults' => $maxResults,
			'pageToken' => $pageToken
		]);
	}
}