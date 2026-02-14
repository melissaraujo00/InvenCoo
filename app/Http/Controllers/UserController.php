<?php

namespace App\Http\Controllers;
use App\Models\office;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {

        $users = User::with([ 'office'])
            ->when($request->filled('search'), fn($query) =>
                $query->whereAny(['name', 'last_name', 'email', 'number'], 'LIKE', "%{$request->search}%")
            )
            ->paginate(10)
            ->withQueryString();

        return view('pages.users.index', compact('users'));
    }

    public function create()
    {
        $offices = office::all();
        return view('pages.users.create', compact('offices'));
    }

    public function store(StoreUserRequest $request){
        $data = $request->validated();
        $data['status'] = true;
        User::create($data);
        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        // Necesitamos las oficinas para llenar el <select> nuevamente
        $offices = office::all();

        return view('pages.users.edit', compact('user', 'offices'));
    }

    /**
     * Actualiza el usuario en la base de datos
     */
    public function update(UpdateUserRequest $request, User $user)
    {

        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}
