<?php

namespace App\Services;

use App\Models\MailSetting;
use Illuminate\Support\Facades\Mail;

class MailSettingsService
{
    public function current(): ?MailSetting
    {
        return MailSetting::query()->first();
    }

    /** @param array{host:string,port:int|string,username?:?string,password?:?string,encryption:string,from_address:string,from_name:string} $data */
    public function save(array $data): MailSetting
    {
        $settings = $this->current() ?? new MailSetting;

        if (($data['password'] ?? '') === '') {
            unset($data['password']);
        }

        $settings->fill($data);
        $settings->save();

        $this->apply($settings);

        return $settings;
    }

    public function apply(?MailSetting $settings = null): void
    {
        $settings ??= $this->current();

        if (! $settings) {
            return;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.transport' => 'smtp',
            'mail.mailers.smtp.scheme' => $settings->encryption === 'ssl' ? 'smtps' : 'smtp',
            'mail.mailers.smtp.url' => null,
            'mail.mailers.smtp.host' => $settings->host,
            'mail.mailers.smtp.port' => $settings->port,
            'mail.mailers.smtp.username' => $settings->username,
            'mail.mailers.smtp.password' => $settings->password,
            'mail.mailers.smtp.timeout' => 10,
            'mail.from.address' => $settings->from_address,
            'mail.from.name' => $settings->from_name,
        ]);

        Mail::purge('smtp');
    }
}
