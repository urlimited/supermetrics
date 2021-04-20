<?php

namespace Application;

use Application\Exceptions\ApiTokenExpiredException;
use Core\Singleton;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class SuperMetricsApiHandler extends Singleton
{
    protected Client $client;

    protected string $slToken = '';

    protected string $apiUrl = 'https://api.supermetrics.com/assignment/posts';

    /**
     * SuperMetricsApiHandler constructor.
     */
    protected function __construct()
    {
        parent::__construct();

        $this->client = new Client();
    }

    /**
     * @param int $page
     * @return array
     * @throws GuzzleException
     */
    public function getPosts(int $page = 1): array
    {
        try {
            if (!$this->client)
                $this->client = new Client();

            if ($this->slToken === '')
                $this->registerApi();

            $params = 'sl_token=' . $this->slToken . '&page=' . $page;

            $uri = $this->apiUrl . '?' . $params;

            $result = $this->client->request('GET', $uri);

            if ($result->getStatusCode() === 500 && json_decode($result->getBody()->getContents(), true)['error']['message'] === 'Invalid SL Token')
                throw new ApiTokenExpiredException('The API Supermetrics token probably expired, try to receive new one');

            return json_decode($result->getBody()->getContents(), true)['data']['posts'];

        } catch (ApiTokenExpiredException $exception) {
            if (!$this->registerApi())
                return [];

            return $this->getPosts($page);
        }
    }

    /**
     * @return bool
     * @throws GuzzleException
     */
    protected function registerApi(): bool
    {
        $result = $this->client->post(
            'https://api.supermetrics.com/assignment/register',
            array(
                'form_params' => array(
                    'client_id' => 'ju16a6m81mhid5ue1z3v2g0uh',
                    'email' => 'maxim.tsoy.cv@gmail.com',
                    'name' => 'Maxim'
                )
            )
        );

        $this->slToken = json_decode($result->getBody()->getContents(), true)['data']['sl_token'];

        return true;
    }
}