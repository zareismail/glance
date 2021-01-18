<?php

namespace Zareismail\Glance;

use Laravel\Nova\Dashboard;

abstract class Glance extends Dashboard
{  
    /**
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function meta()
    {
        return array_merge(parent::meta(), [
            'fields' => 'adasd'
        ]);
    }
}
