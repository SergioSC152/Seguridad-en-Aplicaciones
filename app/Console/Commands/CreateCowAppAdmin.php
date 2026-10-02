<?php
namespace App\Console\Commands;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class CreateCowAppAdmin extends Command
{
    protected $signature='cowapp:admin';
    protected $description='Crea la cuenta administradora configurada, sin modificar una cuenta existente';
    public function handle(): int {
        $email=mb_strtolower(trim((string)config('cowapp.mail_settings_admin_email')));
        if (!filter_var($email,FILTER_VALIDATE_EMAIL)) {$this->error('Configura COWAPP_ADMIN_EMAIL en .env.');return self::FAILURE;}
        if (User::whereRaw('LOWER(email) = ?',[$email])->exists()) {$this->info('La cuenta administradora ya existe; no se cambió su contraseña.');return self::SUCCESS;}
        if (!$this->input->isInteractive()) {$this->error('Se requiere terminal interactiva para entrada oculta.');return self::FAILURE;}
        $name=$this->ask('Nombre del administrador','Administrador CowApp');
        try {$password=$this->secret('Contraseña CowApp (entre 12 y 25 caracteres)',false);$confirmation=$this->secret('Confirma la contraseña',false);}catch(\Throwable){$this->error('La terminal no admite entrada oculta.');return self::FAILURE;}
        if (Validator::make(['name'=>$name,'password'=>$password,'password_confirmation'=>$confirmation],['name'=>['required','string','max:100','regex:/^[\p{L}\p{M}]+(?:[ \x{0027}\x{2019}\-][\p{L}\p{M}]+)*$/u'],'password'=>'required|string|min:12|max:25|confirmed'])->fails()) {$this->error('Revisa nombre, longitud y confirmación.');return self::FAILURE;}
        User::create(['name'=>$name,'email'=>$email,'password'=>Hash::make($password)]);unset($password,$confirmation);
        $this->info('Administrador creado. Inicia sesión con el correo configurado y la contraseña CowApp que elegiste.');return self::SUCCESS;
    }
}
