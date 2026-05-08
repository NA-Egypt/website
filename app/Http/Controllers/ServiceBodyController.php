<?php

namespace App\Http\Controllers;
use App\Http\Requests\ServiceBodyRequest;

use App\Models\Day;
use App\Models\ServiceBody;
use Illuminate\Http\Request;

class ServiceBodyController extends Controller
{

    
    public function index() {

        $sb = ServiceBody::all();
        return view('serviceBody.index', ['sb' => $sb]);
    }

    public function create() {

        $days = Day::all();
        return view('serviceBody.create', ['days'=>$days]);
    }

    public function store(ServiceBodyRequest $request) {

        $validatedData = $request->validated();

        ServiceBody::create($validatedData);

        return redirect()->route('serviceBody.index');

    }

    public function edit(ServiceBody $serviceBody) {

        $days = Day::all();
        return view('serviceBody.edit', ['serviceBody'=>$serviceBody, 'days'=>$days]);
    }

    public function update(ServiceBodyRequest $request, ServiceBody $serviceBody) {

        $fields = $request->validated();

        $serviceBody->update($fields);

        return redirect()->route('serviceBody.index');
        
    }

    public function destroy(ServiceBody $serviceBody) {
        
        $serviceBody->delete();

        return redirect()->route('serviceBody.index');
    }

    public function agendas(ServiceBody $serviceBody) {
        // Eager load groups and agendas to avoid N+1 problem
        $serviceBody->load('agendas.group');
        
        return view('serviceBody.agendas', [
            'serviceBody' => $serviceBody,
            'agendas' => $serviceBody->agendas()->orderBy('agenda_date', 'desc')->get()
        ]);
    }
}
