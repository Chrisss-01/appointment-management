<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'staff_type',
        'student_id',
        'department',
        'program',
        'year_level',
        'phone',
        'avatar',
        'signature_image',
        'license_number',
        'is_active',
        'dob',
        'sex',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ── Role Helpers ────────────────────────────────────────────────

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'staff' && $this->staff_type === 'doctor';
    }

    public function isNurse(): bool
    {
        return $this->role === 'staff' && $this->staff_type === 'nurse';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    // ── Scopes ──────────────────────────────────────────────────────

    public function scopeStudents(Builder $query): Builder
    {
        return $query->where('role', 'student');
    }

    public function scopeStaff(Builder $query): Builder
    {
        return $query->where('role', 'staff');
    }

    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', 'admin');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    // ── Relationships ───────────────────────────────────────────────

    /**
     * Availability slots created by this staff member.
     */
    public function availabilitySlots(): HasMany
    {
        return $this->hasMany(AvailabilitySlot::class, 'staff_id');
    }

    /**
     * Generated slots assigned to this staff member.
     */
    public function generatedSlots(): HasMany
    {
        return $this->hasMany(GeneratedSlot::class, 'staff_id');
    }

    /**
     * Appointments where this user is the student.
     */
    public function studentAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'student_id');
    }

    /**
     * Appointments where this user is the staff member.
     */
    public function staffAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'staff_id');
    }

    /**
     * Medical records for this student.
     */
    public function medicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'student_id');
    }

    /**
     * Medical records created by this staff member.
     */
    public function createdMedicalRecords(): HasMany
    {
        return $this->hasMany(MedicalRecord::class, 'staff_id');
    }

    /**
     * Certificates requested by this student.
     */
    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    /**
     * Certificate requests by this student.
     */
    public function certificateRequests(): HasMany
    {
        return $this->hasMany(CertificateRequest::class, 'student_id');
    }

    /**
     * Announcements authored by this user.
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(Announcement::class, 'author_id');
    }

    /**
     * Notifications for this user.
     */
    public function userNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    /**
     * Clinic tasks assigned to this user.
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(ClinicTask::class, 'assigned_to');
    }

    /**
     * Clinic tasks created by this user.
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(ClinicTask::class, 'assigned_by');
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $url = url(route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ], false));

        Mail::to($this->email)->send(new ResetPasswordMail($url));
    }
}
