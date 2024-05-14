<?php
    namespace App\Http\Controllers;
use App\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Admin;
use App\Models\Sadmin;
    class ChangePasswordController extends Controller {
        public function passwordResetProcess(UpdatePasswordRequest $request)
        {
            $userType = $this->getUserType($request->email);
    
            if (!$userType) {
                return $this->tokenNotFoundError();
            }
    
            switch ($userType) {
                case 'user':
                    $this->resetUserPassword($request);
                    break;
                case 'admin':
                    $this->resetAdminPassword($request);
                    break;
                case 'sadmin':
                    $this->resetSadminPassword($request);
                    break;
            }
    
            return response()->json([
                'data' => 'Password has been updated.'
            ], Response::HTTP_CREATED);
        }
    
        private function updatePasswordRow($request)
        {
            return DB::table('password_resets')->where([
                'email' => $request->email,
                'token' => $request->passwordToken
            ]);
        }
    
        private function getUserType($email)
        {
            if (User::where('email', $email)->exists()) {
                return 'user';
            } elseif (Admin::where('email', $email)->exists()) {
                return 'admin';
            } elseif (Sadmin::where('email', $email)->exists()) {
                return 'sadmin';
            } else {
                return null;
            }
        }
    
        // Réponse en cas de jeton non trouvé
        private function tokenNotFoundError()
        {
            return response()->json([
                'error' => 'Either your email or token is wrong.'
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    
        // Réinitialiser le mot de passe pour l'utilisateur
        private function resetUserPassword($request)
        {
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            $this->updatePasswordRow($request)->delete();
        }
    
        // Réinitialiser le mot de passe pour l'administrateur
        private function resetAdminPassword($request)
        {
            $admin = Admin::where('email', $request->email)->first();
            $admin->password = Hash::make($request->password);
            $admin->save();
            $this->updatePasswordRow($request)->delete();
        }
    
        // Réinitialiser le mot de passe pour le super administrateur
        private function resetSadminPassword($request)
        {
            $sadmin = Sadmin::where('email', $request->email)->first();
            $sadmin->password = Hash::make($request->password);
            $sadmin->save();
            $this->updatePasswordRow($request)->delete();
        }
    }