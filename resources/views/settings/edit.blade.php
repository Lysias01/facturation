@extends('layouts.app')

@section('title', 'Paramètres de la société')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <form action="{{ route('settings.reset') }}" method="POST"
            onsubmit="return confirm('Tout sera supprimé. Continuer ?')">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger w-100 mt-3">
                Réinitialiser les paramètres
            </button>
        </form>
        <div class="card shadow-sm p-4">
            <h3 class="mb-4">Informations de l'entreprises</h3>
           <!-- @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif  -->

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Nom de la société -->
                <div class="mb-3">
                    <label class="form-label">Nom de la société</label>
                    <input type="text" name="company_name" class="form-control"
                        value="{{ old('company_name', $settings?->company_name) }}" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="{{ old('email', $settings?->email) }}">
                </div>

                <!-- Téléphone -->
                <div class="mb-3">
                    <label class="form-label">Téléphone</label>
                    <input type="text" name="phone" class="form-control"
                        value="{{ old('phone', $settings?->phone) }}">
                </div>

                <!-- Adresse -->
                <div class="mb-3">
                    <label class="form-label">Adresse</label>
                    <textarea name="address" class="form-control">{{ old('address', $settings?->address) }}</textarea>
                </div>

                <!-- Ville -->
                <div class="mb-3">
                    <label class="form-label">Ville</label>
                    <input type="text" name="city" class="form-control"
                        value="{{ old('city', $settings?->city) }}">
                </div>

                <!-- IFU -->
                <div class="mb-3">
                    <label class="form-label">Numéro IFU</label>
                    <input type="text" name="ifu" class="form-control"
                        value="{{ old('ifu', $settings?->ifu) }}">
                </div>

                <!-- RCCM  -->
                <div class="mb-3">
                    <label class="form-label">Numéro RCCM</label>
                    <input type="text" name="rccm" class="form-control"
                        value="{{ old('rccm', $settings?->rccm) }}">
                </div>

                <!-- Logo -->
                <div class="mb-3">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control">

                    @if($settings?->logo)
                        <img src="{{ asset('storage/'.$settings->logo) }}" alt="Logo"
                             class="mt-2 rounded border" height="80">
                    @endif
                </div>

                <button class="btn btn-primary w-100">Enregistrer</button>
            </form>
        </div>
    </div>
</div>
@endsection
