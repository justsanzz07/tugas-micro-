<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Submission\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class SubmissionMonitorController extends Controller
{
    public function index(Request $request)
    {
        $query = Submission::with(['competitionDetail', 'members'])->latest('id');

        // Filter Pencarian
        if ($search = $request->query('search')) {
            $parsedId = null;
            if (preg_match('/(?:SUB-)?0*(\d+)/i', $search, $matches)) {
                $parsedId = (int) $matches[1];
            }

            $query->where(function ($q) use ($search, $parsedId) {
                if ($parsedId) {
                    $q->where('id', $parsedId);
                }
                $q->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('submitter_name', 'like', "%{$search}%")
                    ->orWhere('submitter_nim', 'like', "%{$search}%")
                    ->orWhereHas('competitionDetail', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter Fakultas
        if ($faculty = $request->query('faculty')) {
            $prodiMap = [
                'FTI' => ['TI', 'TE', 'SI', 'Teknologi Informasi', 'Teknik Elektro', 'Sistem Informasi'],
                'FAI' => ['PAI', 'PBA', 'MPI', 'HES', 'Pendidikan Agama Islam'],
                'FEB' => ['MNJ', 'AKT', 'Manajemen', 'Akuntansi'],
                'FKES' => ['KPR', 'KBD', 'Keperawatan', 'Kebidanan'],
            ];
            $prodis = $prodiMap[$faculty] ?? [$faculty];

            $query->where(function ($q) use ($faculty, $prodis) {
                $hasFakultasCol = false;
                try {
                    $hasFakultasCol = Schema::connection($q->getModel()->getConnectionName())
                        ->hasColumn($q->getModel()->getTable(), 'fakultas_id');
                } catch (\Throwable) {
                }

                if ($hasFakultasCol) {
                    $q->where('fakultas_id', $faculty)
                        ->orWhereIn('study_program_id', $prodis);
                } else {
                    $q->whereIn('study_program_id', $prodis)
                        ->orWhereIn('study_program_name', $prodis);
                }
            });
        }

        // Filter Tingkat Perlombaan
        if ($level = $request->query('level')) {
            $query->whereHas('competitionDetail', function ($q) use ($level) {
                $q->where('level', $level);
            });
        }

        // Filter Status Pengajuan
        if ($status = $request->query('status')) {
            if ($status === 'kaprodi') {
                $query->whereIn('status', ['submitted', 'kaprodi_review', 'dosen_review', 'review_kaprodi', 'review_dosen']);
            } elseif ($status === 'dekan') {
                $query->whereIn('status', ['dekan_review', 'approved_kaprodi', 'approved_dosen', 'review_dekan']);
            } elseif ($status === 'keuangan') {
                $query->whereIn('status', ['keuangan_review', 'review_keuangan']);
            } elseif ($status === 'warek') {
                $query->whereIn('status', ['warek_review', 'review_warek', 'review_warek_ii']);
            } elseif ($status === 'approved') {
                $query->whereIn('status', ['approved', 'approved_final']);
            } elseif ($status === 'waiting_lpj') {
                $query->whereIn('status', ['waiting_lpj', 'lpj_review', 'review_lpj']);
            } elseif ($status === 'completed') {
                $query->where('status', 'completed');
            } elseif ($status === 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        $submissions = $query->paginate(10)->withQueryString();

        return view('admin.submissions.index', compact('submissions'));
    }

    public function show(Submission $submission)
    {
        $submission->load([
            'competitionDetail',
            'members',
            'officialLetters',
            'lpjReport.attachments',
            'certificates',
        ]);

        $flow = $submission->approvalFlow();
        $budget = $submission->budgetRequest();
        if ($budget) {
            $budget->loadMissing('items');
        }

        return view('admin.submissions.show', compact('submission', 'flow', 'budget'));
    }
}
