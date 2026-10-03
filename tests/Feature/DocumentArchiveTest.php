<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DocumentArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionsSeeder::class);
    }

    public function test_user_can_archive_document_without_removing_file_and_document_remains_viewable(): void
    {
        Storage::fake('local');
        $user = $this->createUserWithRole('client');
        $document = $this->createDocument($user, 'archive-me.pdf');
        Storage::disk('local')->put($document->file_path, 'document contents');

        $this->actingAs($user);

        $response = $this->post(route('documents.archive', $document));

        $response->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'archived' => true,
            'archived_by' => $user->id,
        ]);
        $this->assertNotNull($document->fresh()->archived_at);
        Storage::disk('local')->assertExists($document->file_path);

        $indexResponse = $this->get(route('documents.index'));
        $indexResponse->assertDontSee('archive-me.pdf');

        $archivedResponse = $this->get(route('documents.archive.index'));
        $archivedResponse->assertSee('archive-me.pdf');
        $this->get(route('documents.show', $document))->assertOk();
        $this->get(route('documents.download', $document))->assertOk();
    }

    public function test_user_can_restore_own_archived_document_and_metadata_is_cleared(): void
    {
        $user = $this->createUserWithRole('client');
        $document = $this->createDocument($user, 'restore-me.pdf', [
            'archived' => true,
            'archived_at' => now(),
            'archived_by' => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('documents.restore', $document))
            ->assertRedirect();

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'archived' => false,
            'archived_at' => null,
            'archived_by' => null,
        ]);
        $this->get(route('documents.index'))->assertSee('restore-me.pdf');
        $this->get(route('documents.archive.index'))->assertDontSee('restore-me.pdf');
    }

    public function test_archive_and_restore_own_permissions_cannot_change_another_users_document(): void
    {
        $user = $this->createUserWithRole('client');
        $otherUser = $this->createUserWithRole('client');
        $otherActiveDocument = $this->createDocument($otherUser, 'other-active.pdf');
        $otherArchivedDocument = $this->createDocument($otherUser, 'other-archived.pdf', [
            'archived' => true,
            'archived_at' => now(),
            'archived_by' => $otherUser->id,
        ]);

        $this->actingAs($user)
            ->post(route('documents.archive', $otherActiveDocument))
            ->assertForbidden();

        $this->post(route('documents.restore', $otherArchivedDocument))
            ->assertForbidden();
    }

    public function test_archive_permission_does_not_grant_document_view_access(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo('archive own documents');
        $document = $this->createDocument($user, 'private.pdf');

        $this->actingAs($user)
            ->post(route('documents.archive', $document))
            ->assertForbidden();

        $this->get(route('documents.index'))->assertForbidden();
    }

    public function test_users_with_view_own_documents_only_see_their_documents_in_both_lists(): void
    {
        $user = $this->createUserWithRole('client');
        $otherUser = $this->createUserWithRole('client');
        $ownActive = $this->createDocument($user, 'own-active.pdf');
        $otherActive = $this->createDocument($otherUser, 'other-active.pdf');
        $ownArchived = $this->createDocument($user, 'own-archived.pdf', ['archived' => true]);
        $otherArchived = $this->createDocument($otherUser, 'other-archived.pdf', ['archived' => true]);

        $this->actingAs($user);
        $this->get(route('documents.index'))
            ->assertSee($ownActive->file_name)
            ->assertDontSee($otherActive->file_name);
        $this->get(route('documents.archive.index'))
            ->assertSee($ownArchived->file_name)
            ->assertDontSee($otherArchived->file_name);
    }

    public function test_admin_can_view_archive_and_restore_other_users_documents(): void
    {
        $admin = $this->createUserWithRole('admin');
        $owner = $this->createUserWithRole('client');
        $activeDocument = $this->createDocument($owner, 'admin-archive.pdf');
        $archivedDocument = $this->createDocument($owner, 'admin-restore.pdf', [
            'archived' => true,
            'archived_at' => now(),
            'archived_by' => $owner->id,
        ]);

        $this->actingAs($admin);
        $this->get(route('documents.index'))->assertSee($activeDocument->file_name);
        $this->get(route('documents.archive.index'))->assertSee($archivedDocument->file_name);

        $this->post(route('documents.archive', $activeDocument))->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'id' => $activeDocument->id,
            'archived' => true,
            'archived_by' => $admin->id,
        ]);

        $this->post(route('documents.restore', $archivedDocument))->assertRedirect();
        $this->assertDatabaseHas('documents', [
            'id' => $archivedDocument->id,
            'archived' => false,
            'archived_at' => null,
            'archived_by' => null,
        ]);
    }

    public function test_generic_update_cannot_change_archive_state(): void
    {
        $user = $this->createUserWithRole('super-admin');
        $document = $this->createDocument($user, 'explicit-action.pdf');

        $this->actingAs($user)
            ->put(route('documents.update', $document), ['archived' => true])
            ->assertStatus(422);

        $this->assertDatabaseHas('documents', [
            'id' => $document->id,
            'archived' => false,
        ]);
    }

    public function test_search_and_pagination_keep_the_archive_view(): void
    {
        $user = $this->createUserWithRole('client');
        $this->createDocument($user, 'active-invoice.pdf');
        $this->createDocument($user, 'archived-invoice.pdf', ['archived' => true]);
        for ($index = 0; $index < 10; $index++) {
            $this->createDocument($user, "archived-invoice-{$index}.pdf", ['archived' => true]);
        }

        $this->actingAs($user);
        $this->get(route('documents.index', ['najdi' => 'invoice']))
            ->assertSee('active-invoice.pdf')
            ->assertDontSee('archived-invoice.pdf');

        $archiveResponse = $this->get(route('documents.archive.index', ['najdi' => 'invoice']));
        $archiveResponse->assertSee('archived-invoice.pdf')
            ->assertDontSee('active-invoice.pdf')
            ->assertInertia(fn (Assert $page) => $page
                ->component('Documents/Index')
                ->where('documents.next_page_url', route('documents.archive.index', [
                    'najdi' => 'invoice',
                    'page' => 2,
                ])));
    }

    public function test_deleting_document_still_removes_its_file(): void
    {
        Storage::fake('local');
        $user = $this->createUserWithRole('client');
        $document = $this->createDocument($user, 'delete-me.pdf');
        Storage::disk('local')->put($document->file_path, 'document contents');

        $this->actingAs($user)
            ->delete(route('documents.destroy', $document))
            ->assertRedirect();

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing($document->file_path);
    }

    private function createUserWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    private function createDocument(User $user, string $fileName, array $attributes = []): Document
    {
        return Document::create(array_merge([
            'user_id' => $user->id,
            'file_name' => $fileName,
            'year' => 'Leto 2026',
            'file_path' => 'dokumenti/'.$fileName,
            'file_mime_type' => 'application/pdf',
            'folder' => 'prejeti',
        ], $attributes));
    }
}
