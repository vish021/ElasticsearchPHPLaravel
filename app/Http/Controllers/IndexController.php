<?php

namespace App\Http\Controllers;

use Elasticsearch\Client;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    const RESULTS_PER_PAGE = 5;

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

        if ($query = $request->query('query')) {
            $query = trim($query);
            $page = $request->query('page', 1);
            $from = (($page - 1) * self::RESULTS_PER_PAGE);

            $variables['page'] = $page;
            $variables['from'] = $from;
            $variables['query'] = $query;

            $queryArray = [
                'bool' => [
                    'must' => [],
                ],
            ];
            $tokens = explode(' ', $query);

            foreach ($tokens as $token) {
                $queryArray['bool']['must'][] = [
                    'match' => [
                        'name' => [
                            'query' => $token,
                            'fuzziness' => 'AUTO',
                        ],
                    ],
                ];
            }

            $params = [
                'index' => 'ecommerce',
                'type' => 'product',
                'body' => [
                    'query' => $queryArray,
                    'size' => self::RESULTS_PER_PAGE,
                    'from' => $from,
                ],
            ];

            $result = $this->client->search($params);
            $total = $result['hits']['total'];
            $variables['total'] = $total;

            $to = ($page * self::RESULTS_PER_PAGE);
            $to = ($to > $total ? $total : $to);
            $variables['to'] = $to;

            if (isset($result['hits']['hits'])) {
                $variables['hits'] = $result['hits']['hits'];
            }
        }

        return view('index.index', $variables);
    }
} 