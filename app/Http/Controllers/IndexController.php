<?php

namespace App\Http\Controllers;

use Elasticsearch\Client;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * @var \Elasticsearch\Client
     */
    private $client;


    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function index(Request $request)
    {
        $variables = [];

        if ($request->isMethod('post')) {
            $query = $request->input('query');
            $variables['query'] = $query;

            $params = [
                'index' => 'ecommerce',
                'type' => 'product',
                'body' => [
                    'query' => [
                        'match' => [
                            'name' => $query,
                        ],
                    ],
                ],
            ];

            $result = $this->client->search($params);

            if (isset($result['hits']['hits'])) {
                $variables['hits'] = $result['hits']['hits'];
            }
        }

        return view('index.index', $variables);
    }
} 