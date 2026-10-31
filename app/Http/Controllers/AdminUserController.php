<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAdminUserRequest;
use App\Models\Faculty;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all system users.
     */
    public function index(Request $request)
    {
        $query = User::with(['faculty', 'department', 'roles']);

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('student_id', 'like', "%{$search}%")
                    ->orWhere('staff_id', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(25)->withQueryString();
        $roles = Role::all();
        $faculties = Faculty::with('departments')->get();
        $permissions = Permission::all()->groupBy('group_name');

        return view('portal.admin_users', compact('users', 'roles', 'faculties', 'permissions'));
    }

    /**
     * Create a new user account by Administrator.
     */
    public function store(StoreAdminUserRequest $request)
    {
        $validated = $request->validated();

        $this->ensureCanAssignPrivileges($validated['role'], $validated['permissions'] ?? []);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_title' => $validated['role_title'] ?? null,
            'student_id' => $validated['student_id'] ?? null,
            'staff_id' => $validated['staff_id'] ?? null,
            'faculty_id' => $validated['faculty_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'degree_level' => $validated['degree_level'] ?? null,
            'status' => $validated['status'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $user->role = $validated['role'];
        if (isset($validated['permissions'])) {
            $user->permissions = $validated['permissions'];
        }
        $user->save();

        $roleModel = Role::where('name', $validated['role'])->first();
        if ($roleModel) {
            $user->roles()->sync([$roleModel->id]);
        }

        return back()->with('success', "İstifadəçi ({$user->name}) uğurla yaradıldı!");
    }

    /**
     * Change any user's password directly.
     */
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $this->ensureCanManageUser($user);

        $validated = $request->validate([
            'password' => 'required|string|min:6',
        ], [
            'password.required' => 'Yeni şifrə mütləq daxil edilməlidir.',
            'password.min' => 'Şifrə minimum 6 simvoldan ibarət olmalıdır.',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', "İstifadəçinin ({$user->name}) şifrəsi uğurla dəyişdirildi!");
    }

    /**
     * Update roles, page visibility and granular permissions for a user.
     */
    public function updatePermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role' => 'required|string|in:system_admin,admin,teacher,student,master_student,applicant',
            'role_title' => 'nullable|string|max:255',
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'status' => 'required|in:active,graduated,suspended,pending',
            'permissions' => 'nullable|array',
        ]);

        $this->ensureCanManageUser($user);
        $this->ensureCanAssignPrivileges($validated['role'], $validated['permissions'] ?? []);

        $user->role = $validated['role'];
        $user->role_title = $request->input('role_title') ?: match ($validated['role']) {
            'system_admin' => 'Baş Sistem Administratoru',
            'admin' => 'Dekan / Məktəb Admini',
            'teacher' => 'Fənn Müəllimi',
            'master_student' => 'Magistrant',
            'student' => 'Tələbə',
            'applicant' => 'Abituriyent',
            default => 'Əməkdaş',
        };
        $user->faculty_id = $validated['faculty_id'] ?? null;
        $user->department_id = $validated['department_id'] ?? null;
        $user->status = $validated['status'];
        $user->permissions = $request->has('permissions') ? $request->input('permissions') : null;
        $user->save();

        $roleModel = Role::where('name', $validated['role'])->first();
        if ($roleModel) {
            $user->roles()->sync([$roleModel->id]);
        }

        return back()->with('success', "İstifadəçinin ({$user->name}) rolları və icazələri uğurla yeniləndi!");
    }

    /**
     * Impersonate / Login as target user.
     */
    public function impersonate(Request $request, $id)
    {
        $currentUser = Auth::user();

        // Only system_admin can initiate impersonation
        if (! $currentUser->isSystemAdmin()) {
            abort(403, 'Yalnız Baş Sistem Administratoru başqa istifadəçi profilinə keçid edə bilər.');
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'Öz hesabınıza keçid edə bilməzsiniz.');
        }

        // Store original admin id in session
        session(['impersonator_id' => $currentUser->id]);

        Auth::login($targetUser);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard')
            ->with('success', "Siz hazırda {$targetUser->name} ({$targetUser->role_title}) kimi daxil oldunuz.");
    }

    /**
     * Stop impersonating and return to original System Admin account.
     */
    public function stopImpersonating(Request $request)
    {
        if (! session()->has('impersonator_id')) {
            return redirect()->route('portal.dashboard');
        }

        $adminId = session()->pull('impersonator_id');
        $adminUser = User::findOrFail($adminId);

        Auth::login($adminUser);
        $request->session()->regenerate();

        return redirect()->route('portal.dashboard')
            ->with('success', 'Super Admin hesabınıza uğurla qayıtdınız.');
    }

    /**
     * Delegated user managers (non system admins) may not touch system administrator accounts.
     */
    private function ensureCanManageUser(User $targetUser): void
    {
        if (! Auth::user()->isSystemAdmin() && $targetUser->isSystemAdmin()) {
            abort(403, 'Sistem Administratorunun hesabını yalnız Baş Sistem Administratoru dəyişə bilər.');
        }
    }

    /**
     * Only system admins may grant the system_admin role or full-system permissions.
     *
     * @param  array<int, string>  $permissions
     */
    private function ensureCanAssignPrivileges(string $role, array $permissions): void
    {
        if (Auth::user()->isSystemAdmin()) {
            return;
        }

        if ($role === 'system_admin' || array_intersect($permissions, ['system.all', 'roles.manage'])) {
            abort(403, 'Sistem Administratoru rolunu və ya tam sistem icazələrini yalnız Baş Sistem Administratoru verə bilər.');
        }
    }
}
