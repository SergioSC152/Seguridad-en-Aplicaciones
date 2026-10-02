<?php

return [
    // Fail closed: correo administrativo bloqueado si no se configura una cuenta autorizada.
    'mail_settings_admin_email' => env('COWAPP_ADMIN_EMAIL'),
    'suggested_breeds' => ['Brahman', 'Angus', 'Hereford', 'Holstein', 'Jersey', 'Gyr', 'Nelore', 'Simmental', 'Pardo suizo', 'Criollo', 'Cruce / mestizo'],
];
