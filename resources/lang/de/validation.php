<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute muss akzeptiert werden.',
    'active_url'           => ':attribute ist keine gültige URL.',
    'after'                => ':attribute muss ein Datum nach :date sein.',
    'alpha'                => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute darf nur Buchstaben, Zahlen, und Dindestriche enthalten.',
    'alpha_num'            => ':attribute darf nur Buchstaben und Zahlen enthalten.',
    'array'                => ':attribute muss ein Array sein.',
    'before'               => ':attribute muss ein Datum vor :date sein.',
    'between'              => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file'    => ':attribute muss zwischen :min und :max kilobytes sein.',
        'string'  => ':attribute muss zwischen :min und :max Zeichen enthalten.',
        'array'   => ':attribute muss zwischen :min und :max Elemente haben.',
    ],
    'boolean'              => 'Das :attribute Feld muss wahr oder falsch sein.',
    'confirmed'            => 'Die :attribute - Bestätigung stimmt nicht überein.',
    'date'                 => ':attribute ist kein gültiges Datum.',
    'date_format'          => ':attribute entspricht nicht dem Format :format.',
    'different'            => ':attribute und :other müssen unterschiedlich sein.',
    'digits'               => ':attribute muss aus :digits Nummern bestehen.',
    'digits_between'       => ':attribute muss zwischen :min und :max Nummern enthalten.',
    'email'                => ':attribute muss eine gültige Email-Adresse sein.',
    'exists'               => ':attribute ist ungültig.',
    'filled'               => 'Das :attribute Feld ist erforderlich.',
    'image'                => ':attribute muss eine Bild-Datei sein.',
    'in'                   => 'Das gewählte :attribute Feld ist ungültig.',
    'integer'              => ':attribute muss eine Zahl sein.',
    'ip'                   => ':attribute muss eine gültige IP Adresse sein.',
    'json'                 => ':attribute muss eine gültige JSON Zeichenfolge sein.',
    'max'                  => [
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'file'    => ':attribute darf nicht größer als :max kilobytes sein.',
        'string'  => ':attribute darf nicht größer als :max Zeichen sein.',
        'array'   => ':attribute darf nicht mehr als :max Elemente haben.',
    ],
    'mimes'                => ':attribute muss eine Datei vom Typ: :values sein.',
    'min'                  => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file'    => ':attribute muss mindestens :min kilobytes haben.',
        'string'  => ':attribute muss mindestens :min Zeichen haben.',
        'array'   => ':attribute muss mindestens :min Elemente haben.',
    ],
    'not_in'               => 'Der gewählte Wert von :attribute ist ungültig.',
    'numeric'              => ':attribute muss eine Zahl sein.',
    'regex'                => 'Das Format von :attribute ist ungültig.',
    'required'             => 'Das :attribute Feld ist erforderlich.',
    'required_if'          => 'Das :attribute Feld ist erforderlich, wenn :other :value ist.',
    'required_unless'      => 'Das :attribute Feld ist erforderlich, wenn :other nicht in :values ist.',
    'required_with'        => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all'    => 'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_without'     => 'Das :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist not.',
    'required_without_all' => 'Das :attribute Feld ist erforderlich, wenn keine von :values vorhanden sind.',
    'same'                 => ':attribute und :other müssen übereinstimmen.',
    'size'                 => [
        'numeric' => ':attribute muss :size sein.',
        'file'    => ':attribute muss :size kilobytes sein.',
        'string'  => ':attribute muss :size Zeichen sein.',
        'array'   => ':attribute muss :size Elemente beinhalten.',
    ],
    'string'               => ':attribute muss eine Zeichenkette sein.',
    'timezone'             => ':attribute muss eine gültige Zone sein.',
    'unique'               => ':attribute ist bereits vergeben.',
    'url'                  => 'Das Format von :attribute ist ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
