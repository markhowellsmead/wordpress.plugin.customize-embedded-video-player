<?php

namespace MHM\CustomizeEmbeddedVideoPlayer;

class Plugin
{
    public function __construct()
    {
        add_filter('oembed_result', array($this, 'cleanParameters'), 100, 2);
        add_filter('oembed_result', array($this, 'wrapVideoHTML'), 20, 2);
    }

    /**
     * Amend embedded video URL parameters
     * e.g. hide title and suggested videos
     * @param  string $html The original HTML for the video player
     * @param  strong $url  The original URL of the video embed
     * @return string       The potentally modified HTML for the video player
     */
    public function cleanParameters($html, $url)
    {
        $host = parse_url($url, PHP_URL_HOST);

        switch ($host) {
            case 'vimeo.com':
            case 'www.vimeo.com':
                $html = preg_replace('~(https://player.vimeo.com/video/[0-9]+)"~', '\1?title=0&amp;byline=0&amp;portrait=0"', $html);
                break;

            case 'youtube.com':
            case 'www.youtube.com':
            case 'youtu.be':
                $html = str_replace('?feature=oembed', '?feature=oembed&hl=en&amp;fs=1&amp;showinfo=0&amp;rel=0&amp;iv_load_policy=3&amp;hd=1&amp;vq=hd720&amp;version=3&amp;autohide=1&amp;wmode=opaque&amp;cc=1', $html);
                break;
        }

        return $html;
    }

    public function wrapVideoHTML($html, $url = '')
    {
        preg_match('~[www\.]?(vimeo\.|wordpress\.tv|youtube|youtu\.be)~', $url, $matches);

        if (!is_feed() && (!empty($matches))) {
            $html = sprintf(
                '<div class="responsive-embed widescreen %1$s">%2$s</div>',
                sanitize_title_with_dashes($matches[0]),
                $html
            );
        }

        return $html;
    }
}

new Plugin();
