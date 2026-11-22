<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

#[Signature('app:reset-admin-password {--email= : Adresse e-mail du compte administrateur}')]
#[Description('Réinitialise le mot de passe d’un compte administrateur MediCare.')]
class ResetAdminPassword extends Command
{
    public function handle(): int
    {
        $email = $this->option('email') ?: $this->ask('Adresse e-mail du compte administrateur');

        if (! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Une adresse e-mail valide est requise.');

            return self::FAILURE;
        }

        $user = User::query()
            ->where('email', mb_strtolower(trim($email)))
            ->where('role', User::ROLE_ADMIN)
            ->first();

        if ($user === null) {
            $this->error('Aucun compte administrateur ne correspond à cette adresse e-mail.');

            return self::FAILURE;
        }

        $password = $this->secret('Nouveau mot de passe (12 caractères minimum)');
        $passwordConfirmation = $this->secret('Confirmez le nouveau mot de passe');

        if (! is_string($password) || mb_strlen($password) < 12) {
            $this->error('Le mot de passe doit contenir au moins 12 caractères.');

            return self::FAILURE;
        }

        if ($password !== $passwordConfirmation) {
            $this->error('Les mots de passe ne correspondent pas.');

            return self::FAILURE;
        }

        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => null,
        ])->save();

        $this->info('Le mot de passe administrateur a été réinitialisé.');

        return self::SUCCESS;
    }
}
