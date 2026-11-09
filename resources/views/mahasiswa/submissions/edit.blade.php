@extends('layouts.app')

@section('title', 'Perbaikan Pengajuan — ' . $submission->code)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center space-x-2 text-xs text-slate-500">
        <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-blue-700 font-medium">Dashboard</a>
        <span>/</span>
        <a href="{{ route('mahasiswa.submissions.show', $submission->id) }}" class="hover:text-blue-700 font-medium">{{ $submission->code }}</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Perbaikan Revisi</span>
    </div>

    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Perbaikan Dokumen Proposal & Usulan</h1>
        <p class="text-xs text-slate-500">Silakan tanggapi dan perbaiki catatan evaluasi dari pemeriksa sebelum mengirimkan kembali.</p>
    </div>

    <!-- Alert Catatan Revisi Aktif -->
    @php
        $flow = $submission->approvalFlow();
        $latestStep = $flow?->currentStep();
        $latestLog = $latestStep?->logs()->latest()->first();
    @endphp
    <div class="bg-amber-50 border-2 border-amber-400 p-5 rounded-2xl shadow-sm space-y-2 text-xs">
        <div class="flex items-center space-x-2 text-amber-900 font-bold text-sm">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg"></i>
            <span>Catatan Revisi dari {{ $latestStep?->approver_role?->label() ?? 'Pemeriksa' }}:</span>
        </div>
        <p class="text-amber-900 bg-white/70 p-3 rounded-xl border border-amber-200 leading-relaxed font-mono">
            "{{ $latestStep?->notes ?? $latestLog?->notes ?? 'Mohon lengkapi rincian dan perbaiki proposal Anda.' }}"
        </p>
    </div>

    <!-- Form Resubmit -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 flex items-center">
            <i class="fa-solid fa-pen-to-square text-blue-600 mr-2"></i>
            Tanggapan & Penjelasan Perbaikan
        </h3>

        <form action="{{ route('mahasiswa.submissions.resubmit', $submission->id) }}" method="POST" class="space-y-4">
            @csrf

            <div class="text-xs">
                <label class="block font-semibold text-slate-700 mb-1">Catatan Tindak Lanjut Perbaikan <span class="text-rose-500">*</span></label>
                <textarea name="revision_notes" rows="4" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none leading-relaxed"
                    placeholder="Jelaskan bagian apa saja yang telah Anda perbaiki sesuai catatan dari pemeriksa di atas..."></textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-3">
                <a href="{{ route('mahasiswa.submissions.show', $submission->id) }}" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center space-x-1.5">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>Ajukan Kembali ke {{ $latestStep?->approver_role?->label() ?? 'Pemeriksa' }}</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

