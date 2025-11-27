<?php

namespace App\Http\Controllers\Web\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Dette;
use Inertia\Inertia;

class DetteController extends Controller
{
    public function index()
    {
        $client = Client::where('user_id', auth()->id())->first();
        $dettes = $client ? Dette::where('client_id', $client->id)->paginate(15) : [];

        return Inertia::render('Client/Dettes/Index', [
            'dettes' => $dettes,
        ]);
    }
}
