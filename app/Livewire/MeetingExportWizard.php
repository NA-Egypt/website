<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ServiceBody;
use App\Models\City;

class MeetingExportWizard extends Component
{
    public $step = 1;
    public $exportType = 'service_bodies'; // default criteria type: 'cities' or 'service_bodies'
    public $serviceBodies = [];
    public $selectedServiceBodies = [];
    public $cities = [];
    public $selectedCities = [];
    public $selectedFields = ['topic', 'time', 'type', 'lang']; // default fields
    public $pageSize = 'A4'; // default paper size
    public $isModal = false;

    public function mount()
    {
        // Load service bodies and cities for selection
        $this->serviceBodies = ServiceBody::orderBy('ar_name')->get()->toArray();
        $this->cities = City::whereHas('neighborhoods.groups', function($g) {
            $g->whereNotIn('group_type', ['اونلاين', 'اون لاين', 'online'])
              ->whereHas('meetings', function($m) {
                  $m->where('status', 'available');
              });
        })
        ->orderBy('ar_name')
        ->get()
        ->toArray();
    }

    public function updatedSelectedCities($value)
    {
        if (count($this->selectedCities) > 3) {
            array_pop($this->selectedCities);
            $this->addError('selectedCities', 'يمكنك اختيار 3 مدن كحد أقصى.');
        } else {
            $this->resetErrorBag('selectedCities');
        }
    }

    public function updatedSelectedServiceBodies($value)
    {
        if (count($this->selectedServiceBodies) > 2) {
            array_pop($this->selectedServiceBodies);
            $this->addError('selectedServiceBodies', 'يمكنك اختيار هيئتين خدميتين كحد أقصى.');
        } else {
            $this->resetErrorBag('selectedServiceBodies');
        }
    }

    public function goToStepTwo()
    {
        if ($this->exportType === 'cities') {
            if (empty($this->selectedCities)) {
                $this->addError('selectedCities', 'يجب اختيار مدينة واحدة على الأقل.');
                return;
            }
            if (count($this->selectedCities) > 3) {
                $this->addError('selectedCities', 'يمكنك اختيار 3 مدن كحد أقصى.');
                return;
            }
        } else {
            if (empty($this->selectedServiceBodies)) {
                $this->addError('selectedServiceBodies', 'يجب اختيار جهة خدمة واحدة على الأقل.');
                return;
            }
            if (count($this->selectedServiceBodies) > 2) {
                $this->addError('selectedServiceBodies', 'يمكنك اختيار هيئتين خدميتين كحد أقصى.');
                return;
            }
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
            'export_type' => $this->exportType,
            'cities' => $this->selectedCities,
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
