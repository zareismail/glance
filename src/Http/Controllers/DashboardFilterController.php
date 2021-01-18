<?php

namespace Zareismail\Glance\Http\Controllers;

use Illuminate\Routing\Controller;
use Zareismail\Glance\Http\Requests\DashboardFilterRequest;

class DashboardFilterController extends Controller
{
    /**
     * List the cards for the dashboard.
     *
     * @param  \Laravel\Nova\Http\Requests\DashboardCardRequest  $request
     * @param  string  $dashboard
     * @return \Illuminate\Http\Response
     */
    public function index(DashboardFilterRequest $request, $dashboard = 'main')
    {
        return tap($request->availableFilters($dashboard), function($fields) use ($request) {
            collect($fields)->each->resolve($request->all());
            collect($fields)->each->stacked();
        });
    }
}
