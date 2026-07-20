<?php

namespace App\Models;

use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Invoice extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['invoice_number', 'client_name', 'status', 'total', 'issue_date', 'due_date'])
            ->logOnlyDirty()
            ->useLogName('invoice');
    }

    protected $fillable = [
        'project_id',
        'lead_id',
        'invoice_number',
        'client_name',
        'client_email',
        'issue_date',
        'due_date',
        'status',
        'subtotal',
        'tax',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'due_date' => 'date',
            'status' => InvoiceStatus::class,
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $invoice->invoice_number = static::generateNextNumber();
            }
            if (empty($invoice->unique_access_token)) {
                $invoice->unique_access_token = Str::random(32);
            }
        });
    }

    public static function generateNextNumber(): string
    {
        return DB::transaction(function () {
            $year = date('Y');
            $prefix = "INV-{$year}-";

            $lastInvoice = static::query()
                ->where('invoice_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            if (! $lastInvoice) {
                return "{$prefix}0001";
            }

            $lastInvoiceNumber = $lastInvoice->invoice_number;
            $lastNumStr = substr($lastInvoiceNumber, strlen($prefix));
            $lastNum = (int) $lastNumStr;

            $nextNum = $lastNum + 1;
            $padded = str_pad((string) $nextNum, max(4, strlen((string) $nextNum)), '0', STR_PAD_LEFT);

            return "{$prefix}{$padded}";
        });
    }

    public function calculateTotals(): void
    {
        $subtotal = $this->items()->sum('amount');
        $tax = 0; // standard tax or calculated tax if configured
        $total = $subtotal + $tax;

        $this->update([
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
        ]);

        $this->refresh();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
