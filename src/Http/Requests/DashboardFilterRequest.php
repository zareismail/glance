<?php

namespace Zareismail\Glance\Http\Requests;

use Laravel\Nova\Nova;
use Laravel\Nova\Http\Requests\NovaRequest;

class DashboardFilterRequest extends NovaRequest
{
    /**
     * Get all of the possible filters for the request.
     *
     * @param  string  $dashboard 
     * @return \Illuminate\Support\Collection
     */
    public function availableFilters($dashboard)
    {
        return with($this->dashboard($dashboard), function($dashboard) { 
            return is_callable([$dashboard, 'filters'])? call_user_func([$dashboard, 'filters'], $this) : [];  
        });
    }

    /**
     * Get the the dashboard for the given key.
     * 
     * @param  string  $dashboard
     * @return Collection
     */
    public function dashboard($dashboard)
    {
        return Nova::dashboardForKey($dashboard, $this);
    }
}
