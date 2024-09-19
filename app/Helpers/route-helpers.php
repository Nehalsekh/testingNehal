<?php

function getPublisherRoute(string $route)
{
    return str_replace(config('app.url'), config('app.publisher.url'), $route);
}

function generateAnchorElement($url, $title = null, string $classes = 'btn btn-sm btn-link ml-2')
{
    $title = $title ?? $url;
    $actions = '';
    $actions .= "<a href='" . $url . "' target='_blank' rel='noreferrer' class='" . $classes . "' data-toggle='tooltip' title='" . htmlspecialchars($url) . "' >" . Str::limit($title, $limit = 50, $end = '...') . "</a>";
    return $actions;
}
