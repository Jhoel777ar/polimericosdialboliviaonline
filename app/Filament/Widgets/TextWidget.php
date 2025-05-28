<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class TextWidget extends Widget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.text-widget';
}
