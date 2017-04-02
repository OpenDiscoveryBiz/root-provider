<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OpenDiscoveryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function frontpage()
    {
        return redirect("https://github.com/OpenDiscoveryBiz/root-provider");
    }

    public function lookup(Request $request, $id)
    {
        $pretty = $request->query('pretty');

        if (empty($id)) {
            return response()->json([
                'type' => 'official',
                'error' => 'missing_id',
            ], 400);
        }

        $id = Str::upper($id);
        $id = preg_replace("/[^A-Z0-9]+/", "", $id);

        $idMatches = [];
        if (!preg_match("/^([A-Z]{2,2})([A-Z0-9]+)$/", $id, $idMatches)) {
            return response()->json([
                'type' => 'official',
                'error' => 'invalid_id',
            ], 400);
        }

        $country = $idMatches[1];
        $localId = $idMatches[2];

        $providerEnv = env('PROVIDER_'.$country);
        if (empty($providerEnv)) {
            return response()->json([
                'type' => 'official',
                'error' => 'country_not_supported',
                'error_detailed' => 'The country '.$country.' is not supported',
            ], 404);
        }

        $providers = explode(",", $providerEnv);

        $response = [
            'type' => 'redirect',
            'id' => $country,
            'providers' => $providers,
            'ttl' => (int) env('ROOT_TTL'),
        ];

        if (!empty($pretty)) {
            return response()->json($response, 200, [], JSON_PRETTY_PRINT);
        }

        return response()->json($response, 200);
    }
}
