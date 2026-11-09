<?php

namespace Tests\Unit;

use App\Enums\ApproverRole;
use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Models\Finance\BudgetRequest;
use App\Models\Submission\Submission;
use App\Models\User;
use App\Models\Workflow\ApprovalFlow;
use PHPUnit\Framework\TestCase;

class SipresEnumAndModelTest extends TestCase
{
    public function test_submission_status_enum_helpers(): void
    {
        $draft = SubmissionStatus::DRAFT;
        $this->assertTrue($draft->isEditable());
        $this->assertFalse($draft->isTerminal());
        $this->assertEquals('Draft', $draft->label());
        $this->assertEquals('gray', $draft->badgeColor());

        $completed = SubmissionStatus::COMPLETED;
        $this->assertFalse($completed->isEditable());
        $this->assertTrue($completed->isTerminal());
        $this->assertEquals('Selesai', $completed->label());

        $rejected = SubmissionStatus::REJECTED;
        $this->assertTrue($rejected->isTerminal());
    }

    public function test_approver_role_step_orders(): void
    {
        $this->assertEquals(1, ApproverRole::KAPRODI->stepOrder());
        $this->assertEquals(1, ApproverRole::DOSEN->stepOrder());
        $this->assertEquals(2, ApproverRole::DEKAN->stepOrder());
        $this->assertEquals(3, ApproverRole::WAREK->stepOrder());
    }

    public function test_user_role_methods(): void
    {
        $mahasiswa = new User(['role' => UserRole::MAHASISWA]);
        $this->assertTrue($mahasiswa->isMahasiswa());
        $this->assertFalse($mahasiswa->isDosen());
        $this->assertTrue($mahasiswa->hasRole('mahasiswa'));

        $kaprodi = new User(['role' => UserRole::KAPRODI]);
        $this->assertTrue($kaprodi->isKaprodi());
        $this->assertTrue($kaprodi->hasRole('kaprodi'));

        $warek = new User(['role' => UserRole::WAREK]);
        $this->assertTrue($warek->isWarek());
        $this->assertTrue($warek->hasRole(UserRole::WAREK));
    }

    public function test_model_connections(): void
    {
        $submission = new Submission;
        $this->assertEquals('sipres_submission', $submission->getConnectionName());

        $workflow = new ApprovalFlow;
        $this->assertEquals('sipres_workflow', $workflow->getConnectionName());

        $budget = new BudgetRequest;
        $this->assertEquals('sipres_finance', $budget->getConnectionName());
    }
}
