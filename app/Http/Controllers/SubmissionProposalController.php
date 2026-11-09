<?php

namespace App\Http\Controllers;

use App\Models\Submission\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SubmissionProposalController extends Controller
{
    /**
     * Tampilkan atau unduh dokumen proposal kegiatan lomba mahasiswa.
     */
    public function show(Request $request, Submission $submission)
    {
        Gate::authorize('view', $submission);

        $submission->loadMissing([
            'competitionDetail',
            'members',
        ]);

        $path = $submission->proposal_path;
        $resolvedPath = null;

        if ($path) {
            $candidates = [
                storage_path('app/private/'.$path),
                storage_path('app/'.$path),
                storage_path('app/public/'.$path),
                public_path('storage/'.$path),
                $path,
            ];

            foreach ($candidates as $candidate) {
                if (file_exists($candidate) && is_file($candidate)) {
                    $resolvedPath = $candidate;
                    break;
                }
            }
        }

        $cleanTitle = Str::slug($submission->competitionDetail?->competition_name ?? $submission->title ?? 'Kegiatan');
        $filename = "Proposal-{$submission->code}-{$cleanTitle}.pdf";

        // Jika file fisik PDF ditemukan di disk storage
        if ($resolvedPath) {
            if ($request->has('download') || $request->query('action') === 'download') {
                return response()->download($resolvedPath, $filename, [
                    'Content-Type' => 'application/pdf',
                ]);
            }

            return response()->file($resolvedPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
            ]);
        }

        // Jika file fisik belum terunggah / data demo: Tampilkan lembar preview proposal terstruktur
        $budget = $submission->budgetRequest();

        return view('proposals.preview', compact('submission', 'budget', 'filename'));
    }
}
