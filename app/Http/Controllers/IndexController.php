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
        if ($request->isMethod('post')) {
            /**
             * @todo Perform query
             */
        }

        return view('index.index');
    }
} 