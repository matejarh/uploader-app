<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Database\Seeders\PermissionsSeeder;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PermissionsSeeder::class);
    }

    /**
     * Test that a user can upload a file.
     *
     * @return void
     */
    public function test_user_can_upload_file()
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $user->assignRole('client');
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->post(route('upload'), [
            'file' => $file,
            'folder' => 'inbox',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('flash.banner', __('Document uploaded successfully!'));

        Storage::disk('local')->assertExists('dokumenti/' . $user->email . '/inbox/' . $file->hashName());

        $this->assertDatabaseHas('documents', [
            'user_id' => $user->id,
            'file_name' => $file->getClientOriginalName(),
            'folder' => 'inbox',
        ]);
    }

    /**
     * Test that a file with the same content cannot be uploaded twice.
     *
     * @return void
     */
    public function test_cannot_upload_duplicate_file()
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $user->assignRole('client');
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        // First upload
        $this->post(route('upload'), [
            'file' => $file,
            'folder' => 'inbox',
        ]);

        // Attempt to upload the same file again
        $response = $this->post(route('upload'), [
            'file' => $file,
            'folder' => 'inbox',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file' => __('A file with the same content already exists in storage.')]);

        Storage::disk('local')->assertExists('dokumenti/' . $user->email . '/' . $file->hashName());
        $this->assertCount(1, Document::all());
    }

    /**
     * Test that a file upload fails with invalid file type.
     *
     * @return void
     */
    public function test_upload_fails_with_invalid_file_type()
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $user->assignRole('client');
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

        $response = $this->post(route('upload'), [
            'file' => $file,
            'folder' => 'inbox',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file']);

        Storage::disk('local')->assertMissing('dokumenti/' . $user->email . '/' . $file->hashName());
    }

    /**
     * Test that a file upload fails with file size exceeding the limit.
     *
     * @return void
     */
    public function test_upload_fails_with_file_size_exceeding_limit()
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $user->assignRole('client');
        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 3000, 'application/pdf');

        $response = $this->post(route('upload'), [
            'file' => $file,
            'folder' => 'inbox',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['file']);

        Storage::disk('local')->assertMissing('dokumenti/' . $user->email . '/' . $file->hashName());
    }
}
