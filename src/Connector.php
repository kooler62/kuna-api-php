<?php namespace Kuna;


use GuzzleHttp\Exception\ClientException;
use Kuna\Exception\EmptyResultException;
use Kuna\Exception\KunaException;

/**
 * Class Connector
 * @package Kuna
 * @author   https://github.com/reilag/kuna-api
 * @link     https://github.com/reilag/kuna-api
 *
 * Created by PhpStorm.
 * User: Tymchyk Maksym
 *
 */
class Connector
{

	/**
	 * @param $path
	 * @param array $params
	 * @param string $method
	 */
	public static function execute($path, array $params = [], $method = "GET")
	{
		$path = trim($path, '/');
		$uri = implode('/', [Config::HOST, Config::BASE_PATH, $path]);

		$http = new \GuzzleHttp\Client();

		// only support GET & POST
		$method = strtoupper($method);
		$is_post = ($method == 'POST');
		if (!$is_post)
		{
			$method = 'GET';
		}

		$query = http_build_query($params);

		try
		{
			$response = $http->request($method, $uri, [
				'query' => $query
			]);
		}
		catch (ClientException $e)
		{
			return null;
		}

		$body = $response->getBody();

		if ($response->getStatusCode() !== "200")
		{
			return null;
		}

		if (empty($body))
		{
			throw new EmptyResultException();
		}

		$obj = json_decode($body, true);
		if (empty($obj))
		{
			throw new KunaException("JSON decode failed, content: " . $body);
		}

		return $body;
	}

	/**
	 * @return int
	 */
	public static function timestamp()
	{
		$result = self::execute("timestamp");

		return (int)$result;
	}


}