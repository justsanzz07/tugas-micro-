<?php

namespace App\Http\Middleware;

use App\Services\Submission\SubmissionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLpjBlock
{
    public function __construct(protected SubmissionService $submissionService) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isMahasiswa()) {
            $nim = $user->identifier ?? $user->email;
            $blocked = $this->submissionService->checkLpjBlock($nim);

            if ($blocked) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => "Pengajuan baru diblokir karena terdapat tanggungan LPJ pada pengajuan [{$blocked->code}].",
                        'blocked_submission_id' => $blocked->id,
                    ], 403);
                }

                return redirect()->route('mahasiswa.dashboard')->with('error', "Akses pembuatan pengajuan lomba baru diblokir! Anda memiliki tanggungan LPJ pada pengajuan [{$blocked->code}] yang belum diselesaikan.");
            }
        }

        return $next($request);
    }
}
