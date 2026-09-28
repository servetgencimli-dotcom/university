<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_title',
        'student_id',
        'staff_id',
        'phone',
        'avatar',
        'faculty_id',
        'department_id',
        'degree_level',
        'status',
        'bio',
        'admission_year',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'permissions' => 'array',
            'gpa' => 'decimal:2',
            'earned_credits' => 'integer',
        ];
    }

    // Role checks
    public function isSystemAdmin(): bool
    {
        return $this->role === 'system_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['system_admin', 'admin']);
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return in_array($this->role, ['student', 'master_student']);
    }

    public function isMasterStudent(): bool
    {
        return $this->role === 'master_student';
    }

    public function isApplicant(): bool
    {
        return $this->role === 'applicant';
    }

    // Role relation
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_has_roles');
    }

    public function hasPermission(string $permissionName): bool
    {
        if ($this->isSystemAdmin()) {
            return true;
        }

        // Check custom user permissions override
        if (is_array($this->permissions) && in_array($permissionName, $this->permissions)) {
            return true;
        }

        // Check assigned roles
        foreach ($this->roles as $role) {
            if ($role->permissions->contains('name', $permissionName)) {
                return true;
            }
        }

        return false;
    }

    // Academic relations
    public function faculty(): BelongsTo
    {
        return $this->belongsTo(Faculty::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function taughtSections(): HasMany
    {
        return $this->hasMany(CourseSection::class, 'teacher_id');
    }

    public function admissionsApplications(): HasMany
    {
        return $this->hasMany(AdmissionsApplication::class, 'user_id');
    }

    public function researchPapers(): HasMany
    {
        return $this->hasMany(ResearchPaper::class, 'author_id');
    }

    public function tuitionInvoices(): HasMany
    {
        return $this->hasMany(TuitionInvoice::class, 'student_id');
    }

    public function masterThesis(): HasMany
    {
        return $this->hasMany(StudentThesis::class, 'student_id');
    }

    public function advisedTheses(): HasMany
    {
        return $this->hasMany(StudentThesis::class, 'advisor_id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(SystemNotification::class, 'user_id')->latest();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class, 'student_id');
    }

    public function hasUnpaidDebt(): bool
    {
        return $this->tuitionInvoices()
            ->where(function ($q) {
                $q->where('status', 'overdue')
                    ->orWhere(function ($sq) {
                        $sq->whereIn('status', ['pending', 'unpaid'])
                            ->where('due_date', '<', now());
                    });
            })
            ->exists();
    }

    public function totalDebtAmount(): float
    {
        return (float) $this->tuitionInvoices()
            ->whereIn('status', ['pending', 'unpaid', 'overdue'])
            ->sum('amount');
    }

    /**
     * Dynamic credit limit based on academic GPA standing.
     */
    public function getMaxCreditLimit(): int
    {
        $gpa = (float) ($this->gpa ?? 0);
        if ($gpa >= 3.50) {
            return 36; // High GPA Honor Overload
        } elseif ($gpa > 0 && $gpa < 2.00) {
            return 24; // Academic Probation limit
        }

        return 30; // Standard credit limit
    }
}
