<?php

class YouTubeAPI {

	protected $client = null;
	protected $clientId = '';
	protected $clientSecret = '';
	protected $youtube = null;
	public $messages = [];

	function __construct()
	{
		if ( !file_exists(__DIR__.'/vendor/autoload.php') )
			throw new \Exception('please run "composer require google/apiclient:~2.0" in "' . __DIR__ .'"');

		require_once 'vendor/autoload.php';
		!isset($_SESSION) && session_start();
	}

	public function isLogin()
	{
		$this->client === null && $this->initClient();
		$tokenSessionKey = sprintf('token-%s', $this->client->prepareScopes());
		return isset($_SESSION[$tokenSessionKey]);
	}

	protected function errorHandler($e)
	{
		$err = json_decode($e->getMessage(), true);
		http_response_code($err['error']['code']);
		$this->messages['error'] = $err['error']['message'];
		
		if (http_response_code() == 401) {
			$state = mt_rand();
			$this->client->setState($state);
			$_SESSION['state'] = $state;
			$this->messages['url'] = $this->client->createAuthUrl();
		}
	}

	protected function initClient()
	{
		$this->client = new Google_Client();
		$this->client->setClientId($this->clientId);
		$this->client->setClientSecret($this->clientSecret);
		$this->client->setScopes('https://www.googleapis.com/auth/youtube');
		$redirect = filter_var(sprintf('http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['PHP_SELF']), FILTER_SANITIZE_URL);
		$this->client->setRedirectUri($redirect);
	}

	public function revokeToken()
	{
		$this->client = $this->client ?? new Google_Client();
		$this->client->revokeToken();
		$_SESSION = [];
		session_destroy();
	}

	public function authorize()
	{
		$this->client === null && $this->initClient();
		// Check if an auth token exists for the required scopes
		$tokenSessionKey = sprintf('token-%s', $this->client->prepareScopes());
		if ( isset($_GET['code']) ) {
			if ( strval($_SESSION['state']) !== strval($_GET['state']) ) {
				$this->messages = ['error' => 'The session state did not match'];
				return;
			}

			$this->client->authenticate($_GET['code']);
			$_SESSION[$tokenSessionKey] = $this->client->getAccessToken();
			header('Location: ' . $this->client->getRedirectUri());
		}

		if ( isset($_SESSION[$tokenSessionKey]) ) {
			$this->client->setAccessToken($_SESSION[$tokenSessionKey]);
			// create a resource to do awesome things
			$this->youtube = new Google_Service_YouTube($this->client);
		}

		if ($this->client->getAccessToken() != null)
			return;
		elseif ($this->clientId === null) {
			http_response_code(500);
			$this->messages = ['error' => 'unset $clientId or $clientSecret'];
			return;
		}
		else {
		   // If the user hasn't authorized the app, initiate the OAuth flow
			$state = mt_rand();
			$this->client->setState($state);
			$_SESSION['state'] = $state;
			header('location:'. $this->client->createAuthUrl());
		}
	}
}