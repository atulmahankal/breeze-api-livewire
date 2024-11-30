<?php

namespace App\Trait;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

trait CollectionPagination
{
    public function with(Request $request)
    {
        return [
            'status' => 'success',
        ];
    }

    public function withResponse(Request $request, JsonResponse $response)
    {
        $response->header('Accept', 'application/json');
        $response->header('Type', 'User');
    }

    /**
     * Customize the pagination information for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array $paginated
     * @param  array $default
     * @return array
     */
    public function paginationInformation($request, $paginated, $default)
    {
        unset($paginated['data']);
        unset($paginated['total']);
        $paginated['links'] = $default['links'];
        return $paginated;

        unset($default['meta']['total']);
        unset($default['meta']['links']);
        return $default;
    }
}
