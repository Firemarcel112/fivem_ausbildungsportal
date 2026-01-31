@props([
    'trainer_name' => '',
    'name' => '',
    'birth_date' => '',
    'birth_location' => '',
    'qualification' => '',
    'training_date' => '',
    'organisation_name' => config('settings.certificate_organization_name', config('app.name')),
    'sub_name' => config('settings.certificate_organization_sub_name'),
    'certificate_copyright' => asset('storage/certificates/certificate_copyright.png'),
    'certificate_banner' => asset('storage/certificates/certificate_banner.png'),
    'salutation' => '',
    'salutation_trainer' => '',
])
<!doctype html>
<html lang="de">

    <head>
        <meta charset="utf-8">
        <title>Zertifikat</title>
        <meta content="width=device-width,initial-scale=1" name="viewport">
        <link href="{{ asset('css/tabler.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/custom.css') }}" rel="stylesheet" />
        <style>
            @page {
                size: A4;
                margin: 0;
            }
        </style>
    </head>

    <body>
        <div class="certificate">
            @if ($certificate_copyright)
                <img class="certificate_copyright" src="{{ $certificate_copyright }}" />
            @endif

            <div class="container-xl">
                <div class="row">

                    <div class="col-auto">
                        @if ($certificate_banner)
                            <img class="certificate_banner" src="{{ $certificate_banner }}" />
                        @endif

                    </div>

                    <div class="col" style="max-height: 297mm !important">
                        <div class="mt-6" style="min-height: 250mm !important">
                            <div class="certificate_header">
                                <h1 class="certificate_org_name mb-0">{{ $organisation_name }}</h1>
                                @if (!empty($sub_name))
                                    <h2 class="certificate_sub_name">{{ $sub_name }}</h2>
                                @endif
                            </div>

                            <div class="mt-2 certificate_body">

                                <div class="mt-2">
                                    <h1 class="certificate_title"><b>ZERTIFIKAT</b></h1>
                                </div>

                                <div class="mb-4 certificate_participant">
                                    <h2 class="mb-0">{{ $salutation }} {{ $name }}</h2>
                                    <span class="certificate_meta">
                                        geb. {{ $birth_date }} in {{ $birth_location }}
                                    </span>
                                </div>

                                <div class="certificate_text">
                                    <p>
                                        hat am {{ $training_date }} unter Leitung von {{ $trainer_name }} an der Ausbildung teilgenommen und folgende Qualifikation erreicht
                                    </p>
                                </div>

                                <p class="certificate_qualification">
                                    {{ $qualification }}
                                </p>
                            </div>
                        </div>
                        <div class="certificate_footer">
                            <div class="certificate_signature">
                                <p class="certificate_signature_name"><i>{{ $trainer_name }}</i></p>
                                <p class="certificate_signature_name_normal">{{ $salutation_trainer }} {{ $trainer_name }}</p>
                                <p class="certificate_digital_signature">
                                    -- Dieses Dokument wurde elektronisch erstellt und ist ohne Unterschrift gültig --
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>
