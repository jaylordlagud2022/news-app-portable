<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GuardianProxyController extends Controller
{
    /**
     * Get news from the Guardian API based on search criteria.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNews(Request $request)
    {
        try {
            // Validate the search input
            $request->validate([
                'search' => 'string|max:255',
                'page' => 'integer',
            ]);

            // Extract the search input from the request
            $searchQuery = $request->input('search');
            $page = $request->input('page');

            // Ensure the API key is stored in your configuration or environment variables
            $apiKey = config('services.guardian.api_key') ?? 'test';
            if (!$apiKey) {
                throw new \Exception('Guardian API key not configured.');
            }

            // Make a GET request to the Guardian API with necessary parameters
            $response = Http::get('https://content.guardianapis.com/search', [
                'api-key' => $apiKey,
                'q' => $searchQuery,
                'page' => $page,
                // Add other necessary parameters as needed
            ]);

            // Check if the request was successful (status code 2xx)
            $response->throw();

            // Return the JSON response from the Guardian API
            return $response->json();
        } catch (\Exception $e) {
            // Handle exceptions, log errors, and return a meaningful response to the client
            return response()->json(['error' => 'Failed to retrieve news.'], 500);
        }
    }
}
