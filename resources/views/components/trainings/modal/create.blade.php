@can('trainings.store')
    <a class="btn btn-primary" data-bs-target="#create-training" data-bs-toggle="modal">
        {{ __('general.ausbildung_erstellen') }}
    </a>
    <x-modal form_action="{{ route(name: 'ausbildung.store') }}" id="create-training">
        <x-slot:title>{{ __('general.ausbildung_erstellen') }}</x-slot:title>
        <x-slot:body>
            <div>
                <x-forms.select :title="__('general.qualifikation_auswaehlen')" label="{{ __('general.qualifikation') }}" name="qualification_id" required>
                    @foreach ($getQualifications ?? [] as $qualification)
                        <option value="{{ $qualification->getId() }}">{{ $qualification->getName() }}</option>
                    @endforeach
                </x-forms.select>
            </div>

            <div>
                <x-forms.select :title="__('general.ausbilder_auswaehlen')" label="{{ __('general.ausbilder') }}" name="trainer_id" required>
                    @foreach ($getTrainers() ?? [] as $trainer)
                        <option value="{{ $trainer->getId() }}">{{ $trainer->getFullName() }}</option>
                    @endforeach
                </x-forms.select>
            </div>

            <div>
                <x-forms.input name="meeting_point" required value="{{ config('app.default_meeting_point') }}">
                    {{ __('general.treffpunkt') }}
                </x-forms.input>
            </div>

            <div class="row">
                <div class="col">
                    <x-forms.input name="date" required type="date">
                        {{ __('general.datum') }}
                    </x-forms.input>
                </div>
                <div class="col">
                    <x-forms.input name="time" required type="time">
                        {{ __('general.uhrzeit') }}
                    </x-forms.input>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <x-forms.input name="min_participants" required type="number" value="2">
                        {{ __('general.minimale_teilnehmer') }}
                    </x-forms.input>
                </div>
                <div class="col">
                    <x-forms.input name="max_participants" required type="number" value="10">
                        {{ __('general.maximale_teilnehmer') }}
                    </x-forms.input>
                </div>
            </div>
            <div>
                <div class="form-floating">
                    <textarea class="form-control" id="additional_informations" name="infos" placeholder="{{ __('general.zusaetzliche_informationen') }}" style="height: 100px"></textarea>
                    <label for="additional_informations">{{ __('general.zusaetzliche_informationen') }}</label>
                </div>
            </div>
            <div>
                <x-discord-notification />
            </div>
        </x-slot:body>
        <x-slot:footer>
            <button class="btn btn-secondary text-black" data-bs-dismiss="modal" type="button">{{ __('general.abbrechen') }}</button>
            <button class="btn btn-primary" type="submit">{{ __('general.erstellen') }}</button>
        </x-slot:footer>
    </x-modal>

@endcan
