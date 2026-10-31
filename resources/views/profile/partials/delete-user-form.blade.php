<section>
    <header>
        <h2 class="section-title">{{ __('ui.Delete Account') }}</h2>
        <p class="section-sub">
            {{ __('ui.Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="mt-4">
        <x-danger-button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >{{ __('ui.Delete Account') }}</x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()">
        <form method="post" action="{{ route('profile.destroy') }}">
            @csrf
            @method('delete')

            <h2 class="section-title">{{ __('ui.Are you sure you want to delete your account?') }}</h2>

            <p class="section-sub mt-2">
                {{ __('ui.Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
            </p>

            <div class="field">
                <x-input-label for="delete_password" :value="__('ui.Password')" class="visually-hidden" />
                <x-text-input id="delete_password" name="password" type="password" placeholder="{{ __('ui.Password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="form-actions">
                <x-secondary-button x-on:click="$dispatch('close')">{{ __('ui.Cancel') }}</x-secondary-button>

                <x-danger-button>{{ __('ui.Delete Account') }}</x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
