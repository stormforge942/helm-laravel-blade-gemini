<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class LabelWithTooltip extends Component
{
    public $label;
    public $id;
    public $text;

    public function __construct($label, $id, $text)
    {
        $this->label = $label;
        $this->id = $id;
        $this->text = $text;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.label-with-tooltip');
    }
}
