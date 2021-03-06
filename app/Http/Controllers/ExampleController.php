<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LinkPreview\LinkPreview;
use Cache;

class ExampleController extends Controller
{
    protected $imageWidth = 180;

    protected $cache = 5;

    protected $request;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show()
    {
        return Cache::remember(md5($this->request->input('q')), $this->cache, function () {
            return $this->render();
        });
    }

    private function render()
    {
        if (!$query = $this->request->input('q')) {
            abort(404);
        }

        return $this->parseResults((new LinkPreview($query))->getParsed());
    }

    private function parseResults($parsed)
    {
        $results = [];

        foreach ($parsed as $link) {
            $urlInfo = parse_url(urldecode($link->getUrl()));

            $results['host'] = $urlInfo['host'];
            $results['url'] =  $link->getUrl();
            $results['title'] =  $link->getTitle();
            $results['contentType'] =  $link->getContentType();
            $results['description'] =  $link->getDescription();
            $results['image'] =  $this->getImage($link);
            $results['images'] = $this->getPictures($link);

            if ($link instanceof VideoLink) {
                $results['youtube']['id'] =  $link->getVideoId();
                $results['youtube']['code'] =  $link->getEmbedCode();
            }
        }

        return $results;
    }

    private function getPictures($link)
    {
        if (empty($link->getPictures())) {
            return [];
        }
        return array_map(function ($image) use ($link) {
            return $this->getImageUrl($image, $link);
        }, array_unique($link->getPictures()));
    }

    private function getImage($link)
    {
        if ($image = $link->getImage()) {
            return $this->getImageUrl($image, $link);
        };

        $images = $link->getPictures();

        if (empty($images)) {
            return false;
        }

        foreach ($images as $img) {
            if ($size = $this->getImageSize($this->getImageUrl($img, $link))) {
                if ($size[0] >= $this->imageWidth) {
                     return $img;
                }
            }
        }

        return $this->getImageUrl($images[0], $link);
    }

    private function getImageUrl($img, $link)
    {
        $urlInfo = parse_url($img);
        if (empty($urlInfo['host'])) {
            $linkInfo = parse_url($link->getUrl());
            $img = sprintf('%s://%s/%s', $linkInfo['scheme'], $linkInfo['host'], ltrim($img, '/'));
        }

        return $img;
    }

    private function getImageSize($image)
    {
        try {
            return getimagesize($image);
        } catch (\Exception $exp) {
            return false;
        }
    }
}
