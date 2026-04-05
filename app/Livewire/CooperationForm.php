<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Mail;
use App\Mail\CooperationRequest;

class CooperationForm extends Component
{
    public $showModal = false;
    public $successMessage = false;

    public $name = '';
    public $profession = '';
    public $organization = '';
    public $email = '';
    public $phone = '';
    public $city = '';
    
    public $cooperationType = [];
    public $cooperationTypeOther = '';
    
    public $questions = '';
    
    public $contactMethod = '';
    public $contactMethodOther = '';
    
    public $contactTime = '';
    
    public $agreement = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'profession' => 'required|string|max:255',
        'organization' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'city' => 'required|string|max:255',
        'cooperationType' => 'array',
        'contactMethod' => 'required|string',
        'contactTime' => 'required|string',
        'agreement' => 'accepted'
    ];

    public function openModal()
    {
        $this->showModal = true;
        $this->successMessage = false;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    
    public function submit()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'profession' => $this->profession,
            'organization' => $this->organization,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'cooperationType' => $this->cooperationType,
            'cooperationTypeOther' => $this->cooperationTypeOther,
            'questions' => $this->questions,
            'contactMethod' => $this->contactMethod,
            'contactMethodOther' => $this->contactMethodOther,
            'contactTime' => $this->contactTime,
        ];

        Mail::to('pr@naegypt.org')
            ->cc('web@naegypt.org')
            ->send(new CooperationRequest($data));

        $this->successMessage = true;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->reset([
            'name', 'profession', 'organization', 'email', 'phone', 'city', 
            'cooperationType', 'cooperationTypeOther', 'questions', 
            'contactMethod', 'contactMethodOther', 'contactTime', 'agreement'
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.cooperation-form');
    }
}
