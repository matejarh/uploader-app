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
            'list' => ['role_or_permission:view all users'],
        ];
    }

    /**
     * Display a listing of the users.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
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

    /**
     * Display the specified user.
     *
     * @param \App\Models\User $user
     * @return \Inertia\Response
     */
    public function show(Request $request, User $user)
    {
        if (Auth::user()->can('view self') || Auth::user()->can('view all users')) {
            $query = $user->documents()->with('user')->filter($request->only(['najdi']))->latest();
            $documents = $query->paginate(10)->onEachSide(3);

            return Inertia::render('Users/Show', [
                'user' => $user->load('roles'),
                'documents' => $documents,
                'filters' => $request->only(['najdi']),
                'links' => $documents->links('vendor.pagination.tailwind-table', [
                    'onEachSide' => 3,
                ])->toHtml(),
            ]);
        }

        abort(403, __('Unauthorized action.'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        $this->assignRoleAndNotify($user, $request->email, $request->password);

        session()->flash('flash.banner', __('User created successfully.'));
        session()->flash('flash.bannerStyle', 'success');

        return redirect()->route('users.index')->with('success', __('User created successfully.'));
    }

    /**
     * Assign role to the user and send notification.
     *
     * @param \App\Models\User $user
     * @param string $email
     * @param string $password
     * @return void
     */
    protected function assignRoleAndNotify(User $user, string $email, string $password): void
    {
        $user->assignRole('client');

        if (in_array(\Illuminate\Contracts\Auth\MustVerifyEmail::class, class_implements($user))) {
            $user->sendEmailVerificationNotification();
        }

        // Send user registration notification with login information
        $user->notify(new UserRegistered($email, $password));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        if (Auth::user()->can('delete users')) {
            // Prevent deletion of super-admin
            if ($user->hasRole('super-admin')) {
                session()->flash('flash.banner', __('super-admin cannot be deleted.'));
                session()->flash('flash.bannerStyle', 'danger');
                return redirect()->route('users.index');
            }

            $user->delete();
            return redirect()->route('users.index');
        }

        abort(403, __('Unauthorized action.'));
    }

    /**
     * List all users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $users = User::select('id', 'name', 'email', 'created_at')->get();

        return response()->json($users);
    }
}
