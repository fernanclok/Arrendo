<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Factory;

class RegisteredUserController extends Controller
{
    protected $firebaseAuth;

    /**
     * Constructor para inyectar FirebaseAuth.
     */
    public function __construct()
    {
        $firebaseCredentials = base_path('storage/app/firebase/credentials.json');

        // Validar si la ruta apunta a un archivo
        if (!is_file($firebaseCredentials)) {
            throw new \Exception("El archivo de credenciales no se encontró o no es válido: {$firebaseCredentials}");
        }
    
        // Crear el cliente de Firebase
        $factory = (new Factory)->withServiceAccount($firebaseCredentials);
        $this->firebaseAuth = $factory->createAuth();
    }
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => 'required|string|max:15',
            'role' => 'required|string|max:50',
            'emergency_contact_name' => 'required|string|max:255',
            'emergency_contact_phone' => 'required|string|max:15',
        ]);
    
        // 1. Crear usuario en la base de datos de Laravel
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'registration_date' => now(),
        ]);
    
        // 2. Registrar al usuario en Firebase Authentication
        try {
            $firebaseUser = $this->firebaseAuth->createUser([
                'email' => $user->email,
                'password' => $request->password,
                'displayName' => $user->first_name . ' ' . $user->last_name,
            ]);
    
            // 3. Almacenar el UID de Firebase en la base de datos
            $user->firebase_uid = $firebaseUser->uid;
            $user->save();
    
            // 4. Asignar claims personalizados (como el rol del usuario)
            $this->firebaseAuth->setCustomUserClaims($firebaseUser->uid, [
                'role' => $user->role,
            ]);
        } catch (\Exception $e) {
            // Si hay un error, eliminar el usuario de Laravel
            $user->delete();
            return back()->withErrors(['firebase' => 'Error al crear el usuario en Firebase: ' . $e->getMessage()]);
        }
    
        // 5. Iniciar sesión al usuario en Laravel
        event(new Registered($user));
        Auth::login($user);
    
        // 6. Redirigir al home o dashboard
        return redirect(RouteServiceProvider::HOME);
    }
    
}