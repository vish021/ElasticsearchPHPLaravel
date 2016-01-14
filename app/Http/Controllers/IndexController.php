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
            $variables['aggregations'] = $this->getSearchFilterAggregations();

            $query = trim($query);
            $page = $request->query('page', 1);
            $from = (($page - 1) * self::RESULTS_PER_PAGE);

            $variables['page'] = $page;
            $variables['from'] = $from;
            $variables['query'] = $query;

            $queryArray = [
                'bool' => [
                    'must' => [],
                    'filter' => [],
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

            /* Filters */
            $startPrice = $request->query('startprice');
            $endPrice = $request->query('endprice');
            $status = $request->query('status');
            $category = $request->query('category');

            $variables['startPrice'] = $startPrice;
            $variables['endPrice'] = $endPrice;
            $variables['status'] = $status;
            $variables['category'] = $category;

            // Price
            if ($startPrice && $endPrice) {
                $queryArray['bool']['filter'][] = [
                    'range' => [
                        'price' => [
                            'gte' => $startPrice,
                            'lte' => $endPrice,
                        ],
                    ],
                ];
            }

            // Status
            if ($status) {
                $queryArray['bool']['filter'][] = [
                    'term' => [
                        'status' => $status,
                    ],
                ];
            }

            // Category
            if ($category) {
                $queryArray['bool']['filter'][] = [
                    'nested' => [
                        'path' => 'categories',
                        'query' => [
                            'term' => [
                                'categories.name' => $category,
                            ],
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

    protected function getSearchFilterAggregations()
    {
        $params = [
            'index' => 'ecommerce',
            'type' => 'product',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
                'size' => 0,
                'aggs' => [
                    'statuses' => [
                        'terms' => [ 'field' => 'status' ]
                    ],

                    'price_ranges' => [
                        'range' => [
                            'field' => 'price',
                            'ranges' => [
                                [ 'from' => 1, 'to' => 25 ],
                                [ 'from' => 25, 'to' => 50 ],
                                [ 'from' => 50, 'to' => 75 ],
                                [ 'from' => 75, 'to' => 100 ]
                            ],
                        ],
                    ],

                    'categories' => [
                        'nested' => [
                            'path' => 'categories',
                        ],
                        'aggs' => [
                            'categories_count' => [
                                'terms' => [ 'field' => 'categories.name' ]
                            ],

                        ],
                    ],


                ],
            ],
        ];

        return $this->client->search($params);
    }
} 