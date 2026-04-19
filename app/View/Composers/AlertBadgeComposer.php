<?php

namespace App\View\Composers;

use App\Models\Alert;
use Illuminate\View\View;

class AlertBadgeComposer
{
    public function compose(View $view): void
    {
        $view->with('unreadAlertCount', Alert::where('is_read', false)->count());
    }
}
