<?php require_once 'YouTubeAPI.php';

class Video extends YouTubeAPI {

	function __construct()
	{
		parent::__construct();
		$this->authorize();
	}

	public function listVideos($pageToken)
	{
		$this->authorize(); // must authorize to init new $youtube object, I don't know why???
		return $this->youtube->playlistItems->listPlaylistItems('snippet', [
			'maxResults' => 25,
			'playlistId' => 'UU-DPvvl6eKLsoewWLtNgAOQ'
		]);
	}

	/** 'categoryId' is required */
	public function update($config)
	{
		$updateVideo = $this->get($config['id']);
		$updateVideo['snippet']['categoryId'] = $config['categoryId'];
		$updateVideo['snippet']['title'] = $config['title'];
		$updateVideo['snippet']['description'] = $config['description'];
		$updateVideo['snippet']['tags'] = $config['tags'];
		// keyword: ArrayAccess in php
		// you must indirectly assign value for this
		$status = $updateVideo['status'];
		$status['privacyStatus'] = $config['privacy'];
		$updateVideo['status'] = $status;

		try {
			$videoUpdateResponse = $this->youtube->videos->update('snippet,status', $updateVideo);

			$this->messages['success'] = 'Done!';
		}
		catch (Google_Service_Exception $e) {

			$this->errorHandler($e);
		}
		catch (Google_Exception $e) {

			$this->errorHandler($e);
		}
	}

	/** 
	 * @method upload
	 * @param  array $config [path, title, description, tags, status]
	 */
	public function upload($config)
	{
		try {
			$videoPath = $config['path'];

			$snippet = new Google_Service_YouTube_VideoSnippet();
			$snippet->setTitle( $config['title'] ?: preg_replace('/.+[\/\\\](.+).mp4$/', '$1', $videoPath) );
			$snippet->setDescription($config['description']);
			$snippet->setTags($config['tags']);

			// See https://developers.google.com/youtube/v3/docs/videoCategories/list
			$snippet->setCategoryId($config['categoryId']);

		 	// Valid statuses are "public", "private" and "unlisted"
			$status = new Google_Service_YouTube_VideoStatus();
			$status->privacyStatus = $config['status'] ?? 'private';

		 	// Associate the snippet and status objects with a new video resource.
			$video = new Google_Service_YouTube_Video();
			$video->setSnippet($snippet);
			$video->setStatus($status);

		 	// Specify the size of each chunk of data, in bytes. Set a higher value for
		 	// reliable connection as fewer chunks lead to faster uploads. Set a lower
		 	// value for better recovery on less reliable connections.
			$chunkSizeBytes = 1 * 1024 * 1024;

		 	// Setting the defer flag to true tells the client to return a request which can be called
		 	// with ->execute(); instead of making the API call immediately.
			$this->client->setDefer(true);

		 	// Create a request for the API's videos.insert method to create and upload the video.
			$insertRequest = $this->youtube->videos->insert('status,snippet', $video);

		 	// Create a MediaFileUpload object for resumable uploads.
			$media = new Google_Http_MediaFileUpload(
				$this->client,
				$insertRequest,
				'video/*',
				null,
				true,
				$chunkSizeBytes
			);
			$media->setFileSize(filesize($videoPath));

		 	// Read the media file and upload it chunk by chunk.
			$status = false;
			$handle = fopen($videoPath, 'rb');
			while ( !$status && !feof($handle) ) {
				$chunk = fread($handle, $chunkSizeBytes);
				$status = $media->nextChunk($chunk);
			}

			fclose($handle);

		 	// If you want to make other calls after the file upload, set setDefer back to false
			$this->client->setDefer(false);

			$this->messages['success'][] = [
				'status' => $status['status']['uploadStatus'],
				'link' => sprintf('https://www.youtube.com/watch?v=%s', $status['id'])
			];

			return $status['id'];

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
				$this->youtube->videos->delete($id);
			else {
				for ($i = count($id) - 1; $i >= 0; $i--)
					$this->youtube->videos->delete($id[$i]);
			}
			
			$this->messages = ['success' => 'Done!'];

		} catch (Google_Service_Exception $e) {
			$this->errorHandler($e);
		} catch (Google_Exception $e) {
			$this->errorHandler($e);
		}
	}

	public function get($id)
	{
		return $this->youtube->videos->listVideos('snippet,status', ['id' => $id])['items'][0];
	}
}