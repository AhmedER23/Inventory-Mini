<x-app-layout>
    <x-breadcrumbs :items="[['label' => __('ui.Dashboard'), 'url' => route('dashboard')], ['label' => __('ui.Profile')]]" />

    <section class="panel page-head">
        <div>
            <h1 class="page-title">{{ __('ui.Profile') }}</h1>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-8">
            <section class="panel">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="panel">
                @include('profile.partials.update-password-form')
            </section>

            <section class="panel">
                @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
</x-app-layout>
