<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientResource;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ClientController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ClientResource::collection(Client::paginate());
    }

    /**
     * @param Client $client
     * @return ClientResource
     */
    public function show(Client $client): ClientResource
    {

        return ClientResource::make($client);
    }
}
