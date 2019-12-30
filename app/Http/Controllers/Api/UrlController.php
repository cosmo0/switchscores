<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as Controller;

use App\Traits\SiteRequestData;
use App\Traits\WosServices;

class UrlController extends Controller
{
    use SiteRequestData;
    use WosServices;

    public function generateLinkText()
    {
        $request = request();

        $title = $request->title;

        if (!$title) {
            return response()->json(['error' => 'Missing data: title'], 404);
        }

        $serviceUrl = $this->getServiceUrl();
        $linkText = $serviceUrl->generateLinkText($title);

        if ($linkText) {
            $data = array(
                'linkText' => $linkText,
            );
            return response()->json($data, 200);
        } else {
            return response()->json(['error' => 'Failed to generate link text'], 400);
        }
    }
}
