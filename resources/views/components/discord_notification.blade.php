<button aria-controls="discord-notification-collapse" aria-expanded="false" class="p-0 mb-2 btn btn-link" data-bs-target="#discord-notification-collapse" data-bs-toggle="collapse" type="button">
    {{ __('general.discord_notification') }}
</button>

<div class="collapse" id="discord-notification-collapse">
    <div class="form-check form-switch">
        <input checked class="form-check-input" id="discord_notification_all" name="discord_notification[]" type="checkbox" value="alle">
        <label class="form-check-label" for="discord_notification_all">
            {{ __('general.alle') }}
        </label>
    </div>
    @foreach ($getFractions ?? [] as $fraction)
        <div class="form-check form-switch d-block">
            <input @disabled(empty($fraction->getDiscordWebhook())) class="form-check-input discord_notification-checkbox" id="discord_notification_{{ $fraction->getId() }}" name="discord_notification[]" type="checkbox" value="{{ $fraction->getId() }}">
            <label class="form-check-label" for="discord_notification_{{ $fraction->getId() }}">
                {{ $fraction->getFullName() }}
                @empty($fraction->getDiscordWebhook())
                    <x-icon :classes="['text-danger']" :hovertext="__('general.keine_url_discord')" name="alert-triangle" />
                @endempty
            </label>
        </div>
    @endforeach
</div>
