<?php
use App\Http\Requests\userRequest;
use App\Http\Controllers\UserController;

use function Livewire\Volt\state;
use function Livewire\Volt\layout;

layout('layouts.app');
state(['users','userid','name','email','password','password_confirmation']);

$save = function (){
    $this->setErrorBag(array());

    // Prepare the data to validate
    $data = [
        // 'userid' => $this->userid ?? null,
        'name' => $this->name,
        'email' => $this->email,
        'password' => $this->password,
        'password_confirmation' => $this->password_confirmation,
    ];

    // Create Request
    $request = new \Illuminate\Http\Request($data);

    // Update request method
    // $request->setMethod('PATCH');

    // Call the UserController store method
    $controller = app(Usercontroller::class);
    $response = $controller->store($request);

    $data = $response->getData(true);
    $status = $response->getStatusCode();

    // Handle response
    if ($response->getStatusCode() === 201 || $response->getStatusCode() === 200) {
        $this->reset(['name','email','password','password_confirmation']);
        $this->setErrorBag(array());
        $this->dispatch('notify', ['type' => 'success', 'message' => 'User successfully saved!']);
    } else {
        $this->dispatch('notify', ['type' => 'error', 'message' => 'An error occurred while saving the user.']);
    }
};

?>

<div>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Users') }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

          <!-- Notification message -->
          <x-notification on="notify" />

          <form wire:submit="save">
            <!-- Name Address -->
            <div>
              <x-input-label for="name" :value="__('Name')" />
              <x-text-input class="block w-full mt-1" wire:model="name" id="name" required autofocus />
              <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div class="mt-4">
              <x-input-label for="email" :value="__('Email')" />
              <x-text-input class="block w-full mt-1" wire:model="email" id="email" type="email" autocomplete="username"
                required />
              <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
              <x-input-label for="password" :value="__('Password')" />

              <x-text-input class="block w-full mt-1" wire:model="password" id="password"
                autocomplete="current-password" type="password" required />

              <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Password Confirmation -->
            <div class="mt-4">
              <x-input-label for="password_confirmation" :value="__('Password Confirmation')" />

              <x-text-input class="block w-full mt-1" wire:model="password_confirmation" id="password_confirmation"
                autocomplete="current-password_confirmation" type="password" required />

              <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
              <x-primary-button class="ms-3">
                {{ __('Log in') }}
              </x-primary-button>
            </div>
          </form>

        </div>
      </div>

      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-3 w-full">
        <div class="p-6 text-gray-900">
          <table class="table-auto w-full">
            <thead>
              <tr>
                <th>Song</th>
                <th>Artist</th>
                <th>Year</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>The Sliding Mr. Bones (Next Stop, Pottersville)</td>
                <td>Malcolm Lockyer</td>
                <td>1961</td>
              </tr>
              <tr>
                <td>Witchy Woman</td>
                <td>The Eagles</td>
                <td>1972</td>
              </tr>
              <tr>
                <td>Shining Star</td>
                <td>Earth, Wind, and Fire</td>
                <td>1975</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
