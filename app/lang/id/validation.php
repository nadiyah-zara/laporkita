<?php

return [
    'accepted'         => ':attribute harus disetujui.',
    'array'            => ':attribute harus berupa daftar.',
    'confirmed'        => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi saat ini salah.',
    'date'             => ':attribute bukan tanggal yang valid.',
    'email'            => ':attribute harus berupa alamat email yang valid.',
    'exists'           => ':attribute yang dipilih tidak valid.',
    'file'             => ':attribute harus berupa berkas.',
    'image'            => ':attribute harus berupa gambar.',
    'in'               => ':attribute yang dipilih tidak valid.',
    'integer'          => ':attribute harus berupa bilangan bulat.',
    'mimes'            => ':attribute harus berupa berkas bertipe: :values.',
    'numeric'          => ':attribute harus berupa angka.',
    'required'         => ':attribute wajib diisi.',
    'string'           => ':attribute harus berupa teks.',
    'unique'           => ':attribute sudah digunakan.',

    'min' => [
        'array'   => ':attribute minimal berisi :min item.',
        'file'    => 'Ukuran :attribute minimal :min kilobita.',
        'numeric' => ':attribute minimal :min.',
        'string'  => ':attribute minimal :min karakter.',
    ],
    'max' => [
        'array'   => ':attribute maksimal berisi :max item.',
        'file'    => 'Ukuran :attribute maksimal :max kilobita.',
        'numeric' => ':attribute maksimal :max.',
        'string'  => ':attribute maksimal :max karakter.',
    ],
    'uploaded' => ':attribute gagal diunggah.',

    'password' => [
        'letters'       => ':attribute harus mengandung minimal satu huruf.',
        'mixed'         => ':attribute harus mengandung huruf besar dan kecil.',
        'numbers'       => ':attribute harus mengandung minimal satu angka.',
        'symbols'       => ':attribute harus mengandung minimal satu simbol.',
        'uncompromised' => ':attribute pernah bocor di internet. Pilih yang lain.',
    ],

    'attributes' => [
        'name'                  => 'nama',
        'email'                 => 'email',
        'phone'                 => 'nomor HP',
        'password'              => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'current_password'      => 'kata sandi saat ini',
        'title'                 => 'judul',
        'description'           => 'deskripsi',
        'address'               => 'alamat',
        'category_id'           => 'kategori',
        'reporter_phone'        => 'nomor WhatsApp',
        'photo'                 => 'foto',
        'photos'                => 'foto',
        'photos.*'              => 'foto',
    ],
];