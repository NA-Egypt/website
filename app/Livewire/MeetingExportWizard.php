<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceBody;

class MeetingExportWizard extends Component
{
    public $step = 1;
    public $serviceBodies = [];
    public $selectedServiceBodies = [];
    public $selectedFields = ['topic', 'time', 'type', 'lang']; // default fields
    public $pageSize = 'A4'; // default paper size

    public function mount()
    {
        // Load service bodies for selection
        $this->serviceBodies = ServiceBody::orderBy('ar_name')->get()->toArray();
    }

    public function goToStepTwo()
    {
        if (empty($this->selectedServiceBodies)) {
            $this->addError('selectedServiceBodies', 'يجب اختيار جهة خدمة واحدة على الأقل.');
            return;
        }

        $this->step = 2;
    }

    public function backToStepOne()
    {
        $this->step = 1;
    }

    public function generate()
    {
        if (empty($this->selectedFields)) {
            $this->addError('selectedFields', 'يجب اختيار حقل واحد على الأقل للتصدير.');
            return;
        }

        // Build parameters for the download route
        $params = [
            'service_bodies' => $this->selectedServiceBodies,
            'fields' => $this->selectedFields,
            'page_size' => $this->pageSize,
        ];

        return redirect()->route('meetings.export.download', $params);
    }

    public function render()
    {
        return view('livewire.meeting-export-wizard')
            ->layout('components.layout');
    }
}
