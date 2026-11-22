<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:create-admin-user {--name= : Nom complet} {--email= : Adresse e-mail}')]
#[Description('Crée le premier compte administrateur MediCare.')]
class CreateAdminUser extends Command
{
    public function handle(): int
    {
        $name = $this->option('name') ?: $this->ask('Nom complet');
        $email = $this->option('email') ?: $this->ask('Adresse e-mail');
        $password = $this->secret('Mot de passe (12 caractères minimum)');
        $passwordConfirmation = $this->secret('Confirmez le mot de passe');

        if (! is_string($name) || trim($name) === '' || ! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Un nom et une adresse e-mail valides sont requis.');

            return self::FAILURE;
        }

        if (! is_string($password) || mb_strlen($password) < 12) {
            $this->error('Le mot de passe doit contenir au moins 12 caractères.');

            return self::FAILURE;
        }

        if ($password !== $passwordConfirmation) {
            $this->error('Les mots de passe ne correspondent pas.');

            return self::FAILURE;
        }

        if (User::query()->where('email', $email)->exists()) {
            $this->error('Un compte utilise déjà cette adresse e-mail.');

            return self::FAILURE;
        }

        User::query()->create([
            'name' => trim($name),
            'email' => mb_strtolower(trim($email)),
            'password' => Hash::make($password),
            'role' => User::ROLE_ADMIN,
        ]);

        $this->info('Le compte administrateur a été créé.');

        return self::SUCCESS;
    }
}
