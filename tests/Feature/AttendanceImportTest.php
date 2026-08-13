<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Student;
use App\Models\AttendanceSession;
use Illuminate\Http\UploadedFile;
use Smalot\PdfParser\Parser;
use Mockery\MockInterface;

class AttendanceImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_users_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/import');
        $response->assertRedirect('/login');
    }

    public function test_uploading_invalid_corrupted_pdf_gracefully_fails()
    {
        $admin = User::factory()->create();
        
        // We do NOT mock the parser here so it throws its natural exception when given a fake text file
        $fakePdf = UploadedFile::fake()->create('corrupted.pdf', 10, 'application/pdf');

        $response = $this->actingAs($admin)->post('/import', [
            'week_number' => 1,
            'session_date' => '2024-01-01',
            'pdf_file' => $fakePdf
        ]);

        $response->assertSessionHas('error');
        // Assert no sessions were created due to rollback inside DB::transaction
        $this->assertDatabaseCount('attendance_sessions', 0);
    }

    public function test_uploading_identical_pdf_twice_updates_instead_of_duplicating()
    {
        $admin = User::factory()->create();
        
        // Mock the Parser Service to return a specific valid text block
        $this->mock(\App\Services\PdfAttendanceParser::class, function (MockInterface $mock) {
            $mock->shouldReceive('extractText')->andReturn("1 105285 MUHAMMAD SYAHMI BIN MASRA 09:59:44 11:54:33\n2 105286 JOHN DOE");
        });

        $fakePdf = UploadedFile::fake()->create('attendance.pdf', 10, 'application/pdf');

        // First upload
        $this->actingAs($admin)->post('/import', [
            'week_number' => 1,
            'session_date' => '2024-01-01',
            'pdf_file' => $fakePdf
        ])->assertSessionHas('success');

        $this->assertDatabaseCount('students', 2);
        
        // Upload identically again 
        $this->actingAs($admin)->post('/import', [
            'week_number' => 2,
            'session_date' => '2024-01-08',
            'pdf_file' => $fakePdf
        ])->assertSessionHas('success');

        // Assert students count is still 2 (prevented duplication via updateOrCreate/firstOrCreate)
        $this->assertDatabaseCount('students', 2);
    }
}
