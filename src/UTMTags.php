<?php

namespace XD\UTMTags;

use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Config\Configurable;
use SilverStripe\Core\Injector\Injector;

class UTMTags
{
    use Configurable;

    const UTM_SESSION = 'UTM_SESSION';

    private static $detect_tags = [
        'utm_medium',
        'utm_source',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'utm_*',
    ];

    public static function handleRequest(HTTPRequest $request)
    {
        $vars = $request->getVars();
        $tags = self::getTagsFromArray($vars);
        if (!empty($tags)) {
            $session = $request->getSession();
            $session->set(self::UTM_SESSION, json_encode($tags));
            $session->save($request);
        }
    }

    public static function getTagsFromSession(HTTPRequest $request = null): array
    {
        if (!$request) {
            $request = Injector::inst()->get(HTTPRequest::class);
        }

        $session = $request->getSession();
        return json_decode($session->get(self::UTM_SESSION) ?? '[]', true);
    }

    public static function getTagsFromArray(array $vars): array
    {
        $detect = self::config()->get('detect_tags');
        $widcards = array_filter(self::config()->get('detect_tags'), fn($tag) => strpos($tag, '*') !== false);
        $tags = array_filter($vars, function($key) use ($detect, $widcards) {
            // direct matches
            if (in_array($key, $detect)) {
                return true;
            }

            // handle wildcards
            foreach ($widcards as $wildcard) {
                $pos = strpos($wildcard, '*');
                $match = str_replace('*', '', $wildcard);
                $rest = str_replace($match, '', $key);
                $start = strpos($key, $rest);
                $found = strpos($key, $match);

                // matches and starts after wildcard
                if ($found !== false && $pos === $start) {
                    return true;
                }
            }

            return false;
        }, ARRAY_FILTER_USE_KEY);

        return $tags;
    }
}
