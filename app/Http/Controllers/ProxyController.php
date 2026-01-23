<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ProxyController extends Controller
{
    public function proxy(Request $request)
    {
        $region = $request->input('region', 'en-us');
        $url = "https://store.playstation.com/{$region}/pages/latest";

        try {
            $client = new Client([
                'timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ]
            ]);

            $response = $client->request('GET', $url);
            $content = $response->getBody()->getContents();

            // Inject <base> tag to fix relative links (images, css, scripts)
            // This tells the browser to load assets relative to the original PlayStation URL
            $baseTag = "<base href=\"https://store.playstation.com/{$region}/pages/latest/\">";
            
            // Insert after <head>
            if (stripos($content, '<head>') !== false) {
                $content = str_ireplace('<head>', '<head>' . $baseTag, $content);
            } else {
                // Fallback if no head tag found (unlikely)
                $content = $baseTag . $content;
            }

            // Optional: Inject some custom CSS to hide header/footer if desired
            // $customCss = "<style>.psw-header, .psw-footer { display: none !important; }</style>";
            // $content = str_ireplace('</head>', $customCss . '</head>', $content);

            return response($content);

        } catch (\Exception $e) {
            Log::error("Proxy Error: " . $e->getMessage());
            return response("Error loading content: " . $e->getMessage(), 500);
        }
    }
}
