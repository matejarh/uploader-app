<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware = [
            'index' => ['permission:view all users'],
            'show' => ['permission:view self', 'permission:view all users'],
            'destroy' => ['permission:delete users'],
        ];
    }
    public function index(Request $request)
    {
        if (Auth::user()->can('view all users')) {
            $query = User::filter($request->only(['najdi']))->latest();
            $users = $query->paginate(10)->onEachSide(3);

            return Inertia::render('Users/Index', [
                'users' => $users,
                'filters' => $request->only(['najdi']),
                'links' => $users->links('vendor.pagination.tailwind-table', [
                    'onEachSide' => 3,
                ])->toHtml(),
            ]);

        }
        abort(403, __('Unauthorized action.'));
    }

    public function show(User $user)
    {
        if (Auth::user()->can('view self') || Auth::user()->can('view all users')) {
            return Inertia::render('Users/Show', [
                'user' => $user->load('roles'),
            ]);
        }
        abort(403, __('Unauthorized action.'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $user->assignRole('client');

        if (in_array(\Illuminate\Contracts\Auth\MustVerifyEmail::class, class_implements($user))) {
            $user->sendEmailVerificationNotification();
        }

        // Send user registration notification with login information
        $user->notify(new UserRegistered($request->email, $request->password));

        session()->flash('flash.banner', __('User created successfully.'));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('users.index')->with('success', __('User created successfully.'));
    }

    public function destroy(User $user)
    {
        if (Auth::user()->can('delete users')) {
            // Prevent deletion of Super-Admin
            if ($user->hasRole('Super-Admin')) {
                session()->flash('flash.banner', __('Super-Admin cannot be deleted.'));
                session()->flash('flash.bannerStyle', 'danger');
                return redirect()->route('users.index');
            }

            $user->delete();
            return redirect()->route('users.index');
        }
        abort(403, __('Unauthorized action.'));
    }
}
