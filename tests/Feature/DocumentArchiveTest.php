<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionsSeeder::class);
    }

    public function test_super_admin_can_archive_document_and_it_is_hidden_from_active_list(): void
    {
        $user = User::factory()->create();
        $user->assignRole('super-admin');

        $company = Company::create([
            'user_id' => $user->id,
            'company_name' => 'Test Company',
            'company_address' => 'Test Address',
        ]);

        $document = Document::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'file_name' => 'archive-me.pdf',
            'year' => 'Leto 2026',
            'file_path' => 'dokumenti/test-company/archive-me.pdf',
            'file_mime_type' => 'application/pdf',
            'folder' => 'prejeti',
        ]);

        $this->actingAs($user);

        $response = $this->put(route('documents.update', $document), [
            'archived' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'archived' => true,
        ]);

        $indexResponse = $this->get(route('documents.index'));
        $indexResponse->assertDontSee('archive-me.pdf');

        $archivedResponse = $this->get(route('documents.index', ['archived' => 1]));
        $archivedResponse->assertSee('archive-me.pdf');
    }
}
