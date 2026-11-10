<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Admin\AdminProfile;
use App\Models\MswdoActivityLog;
use App\Models\PasswordResetRequest;
use App\Models\SocialCase\BeneficiaryIntake;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MswdoActivityLogger
{
    public function actor(): ?User
    {
        $request = request();
        if (! $request->hasSession() || $request->session()->get('auth_source') !== 'mswdo') {
            return null;
        }

        $user = User::find($request->session()->get('admin_user_id'));

        // Only the MSWDO account table and recognized MSWDO roles qualify.
        // WCPU/SWD/BCPC accounts and administrators use a separate auth source.
        return $user && UserRole::tryFrom((string) $user->getRawOriginal('role')) ? $user : null;
    }

    public function record(string $type, string $module, string $description, ?Model $subject = null, array $old = [], array $new = [], ?User $actor = null): void
    {
        $actor ??= $this->actor();
        if (! $actor) {
            return;
        }

        MswdoActivityLog::create([
            'user_id' => $actor->getKey(),
            'user_name' => $actor->name,
            'role' => $actor->getRawOriginal('role'),
            'activity_type' => $type,
            'description' => $description,
            'module' => $module,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? (string) $subject->getKey() : null,
            'old_values' => $this->safeValues($old),
            'new_values' => $this->safeValues($new),
            'created_at' => now(),
        ]);
        request()->attributes->set('mswdo_activity_recorded', true);
    }

    public function change(Model $subject, string $type, array $old, array $new, ?User $actor = null): void
    {
        if ($type === 'updated') {
            $changed = array_keys(array_filter($new, fn ($value, $key) => ! array_key_exists($key, $old) || $old[$key] !== $value, ARRAY_FILTER_USE_BOTH));
            $changed = array_diff($changed, ['updated_at', 'remember_token']);
            if (! $changed) {
                return;
            }
            $old = array_intersect_key($old, array_flip($changed));
            $new = array_intersect_key($new, array_flip($changed));

            foreach (['status', 'is_archived', 'archived_at', 'deleted_at'] as $field) {
                if (! in_array($field, $changed, true)) {
                    continue;
                }
                if ($field === 'status') {
                    if (($new[$field] ?? null) === 'archived') {
                        $type = 'archived';
                    } elseif (($old[$field] ?? null) === 'archived') {
                        $type = 'restored';
                    }
                } else {
                    $type = empty($new[$field]) ? 'restored' : 'archived';
                }
                if ($type !== 'updated') {
                    break;
                }
            }
        }

        $module = match (true) {
            $subject instanceof User, $subject instanceof AdminProfile => 'User Management',
            str_contains(get_class($subject), '\\Senior\\') => 'Senior Citizens',
            str_contains(get_class($subject), '\\Financial\\'), $subject instanceof BeneficiaryIntake => 'Financial Assistance',
            $subject instanceof PasswordResetRequest => 'Password Reset Management',
            default => 'Social Case Study',
        };
        $this->record($type, $module, ucfirst($type).' '.class_basename($subject).' #'.$subject->getKey(), $subject, $old, $new, $actor);
    }

    private function safeValues(array $values): array
    {
        // Allowlist only workflow metadata. Never store personal/medical narratives,
        // contact details, credentials, tokens, signatures, attachments or arbitrary JSON.
        $allowed = [
            'role', 'status', 'position', 'signature_position', 'is_archived', 'archived_at',
            'deleted_at', 'eligibility_status', 'workflow_step', 'requirements_complete',
            'interview_complete', 'evaluation_complete', 'report_generated', 'assistance_released',
            'claim_status', 'is_exported', 'is_printed', 'print_count', 'last_printed_at',
            'date_processed', 'interview_date', 'assistance_date', 'released_at', 'eligible_at',
            'date_issued', 'year_applied', 'month', 'year', 'payout_month', 'payout_year',
        ];

        return array_filter(array_intersect_key($values, array_flip($allowed)), fn ($value) => is_scalar($value) || $value === null);
    }
}
