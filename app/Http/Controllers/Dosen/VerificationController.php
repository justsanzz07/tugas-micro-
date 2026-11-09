<?php

namespace App\Http\Controllers\Dosen;

use App\Enums\ApprovalAction;
use App\Http\Controllers\Controller;
use App\Models\Submission\Submission;
use App\Services\Workflow\ApprovalStateMachine;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class VerificationController extends Controller
{
    public function __construct(protected ApprovalStateMachine $stateMachine) {}

    public function dashboard(Request $request)
    {
        $dosen = $request->user();
        $nidn = $dosen->identifier;

        $pendingSubmissions = Submission::with(['competitionDetail', 'members'])
            ->when($nidn, fn ($q) => $q->where('supervisor_nidn', $nidn))
            ->whereIn('status', ['submitted', 'dosen_review'])
            ->latest()
            ->get();

        $historySubmissions = Submission::with(['competitionDetail', 'members'])
            ->when($nidn, fn ($q) => $q->where('supervisor_nidn', $nidn))
            ->whereNotIn('status', ['submitted', 'dosen_review'])
            ->latest()
            ->paginate(10);

        return view('dosen.dashboard', compact('pendingSubmissions', 'historySubmissions'));
    }

    public function show(Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->load([
            'competitionDetail',
            'members',
            'officialLetters',
        ]);

        $flow = $submission->approvalFlow();
        $budget = $submission->budgetRequest();

        return view('dosen.show', compact('submission', 'flow', 'budget'));
    }

    public function process(Request $request, Submission $submission)
    {
        Gate::authorize('approve', $submission);

        $validated = $request->validate([
            'action' => ['required', 'in:APPROVE,REVISE,REJECT'],
            'notes' => ['nullable', 'string'],
        ]);

        $action = ApprovalAction::from($validated['action']);

        try {
            $newStatus = $this->stateMachine->processDecision(
                $submission,
                $request->user(),
                $action,
                $validated['notes']
            );

            $msg = match ($action) {
                ApprovalAction::APPROVE => "Pengajuan [{$submission->code}] berhasil disetujui dan diteruskan ke Dekan Fakultas.",
                ApprovalAction::REVISE => "Pengajuan [{$submission->code}] telah dikembalikan ke Mahasiswa untuk perbaikan.",
                ApprovalAction::REJECT => "Pengajuan [{$submission->code}] telah ditolak.",
                default => 'Keputusan berhasil disimpan.',
            };

            return redirect()->route('dosen.dashboard')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
