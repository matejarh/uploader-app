# AGENTS.md

## Project

This file contains implementation instructions for the `uploader-app` Laravel application.

The goal of this task is to finish and cleanly implement **document archiving**.

Repository:

`matejarh/uploader-app`

---

# 1. IMPORTANT: Existing Partial Implementation

The repository already contains a **partial implementation of document archiving**.

Before making any changes:

1. Inspect the existing repository.
2. Inspect the `Document` model.
3. Inspect `DocumentsController`.
4. Inspect `routes/web.php`.
5. Inspect:

   * `resources/js/Pages/Documents/Index.vue`
   * `resources/js/Pages/Documents/Partials/TableList.vue`
   * `resources/js/Pages/Documents/Partials/TableItem.vue`
6. Inspect existing document migrations.
7. Inspect the permission system and permission seeders.

Do **NOT** create a second independent archiving system.

Refactor and complete the existing implementation according to this specification.

Do not unnecessarily rewrite working functionality.

---

# 2. Technology

The application uses:

* Laravel
* PHP
* Laravel Jetstream
* Inertia.js
* Vue 3
* Tailwind CSS
* Heroicons
* Spatie Laravel Permission or the permission system already present in the repository

Follow the existing project conventions.

Do not introduce a new framework or UI library.

Do not replace existing components unnecessarily.

---

# 3. Functional Goal

Documents must have two logical states:

```text
ACTIVE
ARCHIVED
```

Archiving is NOT deletion.

When a document is archived:

* the database record remains;
* the physical file remains in storage;
* the file path remains unchanged;
* the document can still be viewed/downloaded;
* the document can later be restored.

The lifecycle is:

```text
ACTIVE
   |
   | Archive
   v
ARCHIVED
   |
   | Restore
   v
ACTIVE
```

Deletion remains a separate operation.

---

# 4. Database

Add a migration for document archiving.

The `documents` table must contain:

```text
archived
archived_at
archived_by
```

Recommended structure:

```php
$table->boolean('archived')
    ->default(false)
    ->index();

$table->timestamp('archived_at')
    ->nullable();

$table->foreignId('archived_by')
    ->nullable()
    ->constrained('users')
    ->nullOnDelete();
```

Place the migration after the existing document migrations.

Do not modify old migrations that may already have been executed in production.

Create a new migration instead.

---

# 5. Document Model

Update `App\Models\Document`.

Ensure the model supports:

```php
protected $fillable = [
    // existing fields...
    'archived',
    'archived_at',
    'archived_by',
];
```

Ensure casts include:

```php
'archived' => 'boolean',
'archived_at' => 'datetime',
```

Add relationship:

```php
public function archivedBy()
{
    return $this->belongsTo(User::class, 'archived_by');
}
```

Do not remove or break existing relationships.

Do not change the existing physical-file deletion behavior.

Archiving must NOT trigger the model's deletion logic.

---

# 6. Controller

Modify the existing:

```text
app/Http/Controllers/DocumentsController.php
```

Do not create a second controller unless absolutely necessary.

Add dedicated methods:

```php
public function archive(Document $document)
```

and:

```php
public function restore(Document $document)
```

## Archive

The archive method must:

1. Verify that the authenticated user is allowed to archive the document.
2. Set:

```php
archived = true
archived_at = now()
archived_by = Auth::id()
```

3. Save the document.
4. Return to the previous page or appropriate document list.
5. Provide an appropriate success flash message.

## Restore

The restore method must:

1. Verify that the authenticated user is allowed to restore the document.
2. Set:

```php
archived = false
archived_at = null
archived_by = null
```

3. Save the document.
4. Return to the previous page or appropriate document list.
5. Provide an appropriate success flash message.

---

# 7. Do NOT Use Generic Update for Archiving

The current implementation may use the generic document update endpoint to toggle `archived`.

Do not continue this design.

Archiving and restoring must have explicit endpoints:

```text
archive
restore
```

This keeps the API and authorization logic clear.

The existing `update()` functionality for other document properties must continue to work.

---

# 8. Routes

Modify:

```text
routes/web.php
```

Add explicit routes following the existing route conventions.

Recommended:

```php
Route::post(
    '/documents/{document}/archive',
    [DocumentsController::class, 'archive']
)->name('documents.archive');

Route::post(
    '/documents/{document}/restore',
    [DocumentsController::class, 'restore']
)->name('documents.restore');
```

Use the existing route grouping/middleware structure.

Do not duplicate route definitions.

Use route model binding consistently with the existing application.

---

# 9. Document List

The normal Documents page must show only active documents.

Conceptually:

```php
where('archived', false)
```

The archive page must show only archived documents:

```php
where('archived', true)
```

Existing filtering must continue to work.

Existing search/filter functionality must work independently inside both views.

For example:

```text
Documents + search "invoice"
```

must search active documents.

And:

```text
Archive + search "invoice"
```

must search archived documents.

Do not remove the existing document filtering behavior.

---

# 10. Archive URL

Use a dedicated archive URL.

Preferred:

```text
/documents
/documents/archive
```

Do not rely exclusively on:

```text
/documents?archived=1
```

The normal document page should represent active documents.

The archive page should represent archived documents.

It is acceptable to use the same Vue page/component for both states if this fits the existing architecture.

---

# 11. Vue UI

The current UI contains an "Arhiviran" checkbox/column.

Remove the checkbox-based archive interaction.

Archiving should be an explicit action.

Do NOT show:

```text
[ ] Arhiviran
```

in the normal document table.

Instead, add an action button.

Active document:

```text
View
Download
Archive
Delete
```

Archived document:

```text
View
Download
Restore
Delete
```

Use the project's existing Heroicons.

Do not introduce another icon library.

---

# 12. Documents / Archive Navigation

The Documents UI should clearly distinguish:

```text
Documents
Archive
```

Preferred UI:

```text
[ Documents ] [ Archive ]
```

When viewing active documents:

```text
Documents
```

is active.

When viewing archived documents:

```text
Archive
```

is active.

Use existing Tailwind styling conventions.

Keep the UI visually consistent with the rest of the application.

---

# 13. Table

Modify:

```text
resources/js/Pages/Documents/Partials/TableList.vue
```

Remove the dedicated:

```text
Arhiviran
```

column.

The archive/restore operation belongs in the existing operations column.

Do not unnecessarily restructure the table.

The existing columns should remain:

```text
Datum
Stranka
Podjetje
Leto
Mapa
Datoteka
Obdelan
Operacije
```

subject to the existing permission/role-based visibility.

---

# 14. TableItem.vue

Modify:

```text
resources/js/Pages/Documents/Partials/TableItem.vue
```

Replace the current checkbox-based archive mechanism with explicit actions.

For active documents:

```text
Archive
```

For archived documents:

```text
Restore
```

Use Inertia requests to the dedicated routes.

Example concept:

```js
router.post(route('documents.archive', item.key))
```

and:

```js
router.post(route('documents.restore', item.key))
```

Adapt this to the actual existing route/model binding implementation.

Do not blindly copy these examples if the repository uses a different pattern.

---

# 15. Confirmation

Archiving and restoring are reversible, but they are still meaningful document state changes.

Use the existing confirmation/dialog pattern in the application if one exists.

Archive confirmation example:

```text
Archive this document?
```

Restore confirmation example:

```text
Restore this document?
```

Do not introduce a new modal framework.

If the project already has a reusable confirmation component, use it.

---

# 16. Permissions

Extend the existing permission system.

Add:

```text
archive any document
archive own documents
restore any document
restore own documents
```

Do not remove existing permissions.

Existing permissions such as:

```text
view own documents
view any document
upload documents
delete own documents
delete any document
```

must continue to work.

---

# 17. Permission Semantics

Use the same ownership model already used by the application.

For a user with:

```text
archive any document
```

the user may archive any document they are allowed to access.

For:

```text
archive own documents
```

the user may archive only documents where:

```php
document.user_id === Auth::id()
```

The same logic applies to restore.

Do not give users broader access merely because they can archive/restore.

---

# 18. Admin and Super Admin

Preserve the existing application behavior for:

```text
admin
super admin
```

Do not hard-code role names if the existing permission system already handles them.

If the repository uses a Gate or role override for Super Admin, preserve it.

Do not introduce a separate authorization system.

---

# 19. DocumentsController Permission Logic

Review the current middleware/authorization logic carefully.

The existing controller may have a permission configuration around:

```text
index
show
destroy
```

and may additionally check permissions inside methods.

Make sure the final implementation allows:

* users with `view own documents` to access their own document list;
* users with `view any document` to access all documents;
* archive permissions to follow the same ownership rules;
* restore permissions to follow the same ownership rules;
* delete permissions to remain unchanged.

Do not accidentally make `index` inaccessible to users who only have `view own documents`.

---

# 20. Archive Query Logic

The active list should effectively use:

```php
where('archived', false)
```

The archive list:

```php
where('archived', true)
```

Then apply the existing filters.

Preferred conceptual order:

```php
Document::query()
    ->where('archived', $showArchived)
    ->filter(...)
    ->latest(...)
```

Do not duplicate filtering code unnecessarily.

---

# 21. Pagination

Ensure pagination works correctly for both:

```text
/documents
/documents/archive
```

Search/filter state must survive pagination.

Do not introduce broken links where clicking page 2 accidentally returns to the active documents list.

---

# 22. Sorting

For normal documents, preserve the existing sorting behavior.

For archive, preferably sort by:

```text
archived_at DESC
```

so recently archived documents appear first.

Do not change unrelated sorting behavior.

---

# 23. Physical Files

CRITICAL:

Archiving must NEVER:

* delete the file;
* move the file;
* rename the file;
* change `file_path`;
* change `key`.

The physical document must remain exactly where it was.

The existing `deleting()` behavior in the model must remain reserved for actual deletion.

---

# 24. Delete Behavior

Do not redefine deletion.

Deleting a document should continue to use the existing deletion mechanism.

The expected lifecycle is:

```text
Active
  |
  | Archive
  v
Archived
  |
  | Restore
  v
Active
```

and independently:

```text
Active or Archived
  |
  | Delete
  v
Deleted
```

---

# 25. Archive Metadata

Store:

```text
archived_at
archived_by
```

even if they are not initially displayed in the table.

This provides a foundation for future audit/history functionality.

If displaying archive metadata is easy and consistent with the existing UI, it may be added to the archive view.

Do not overcomplicate the first implementation.

---

# 26. Existing `processed` Functionality

The application already has a separate:

```text
processed
```

state.

Do NOT combine:

```text
processed
```

with:

```text
archived
```

They represent different concepts.

A document can be:

```text
processed = true
archived = false
```

or:

```text
processed = true
archived = true
```

etc.

Do not change existing processed behavior.

---

# 27. Backward Compatibility

Existing documents created before the archive feature must automatically be considered active.

The migration must therefore use:

```php
default(false)
```

for `archived`.

Do not require manually updating existing documents.

---

# 28. Existing Upload Functionality

Do not modify upload behavior unless necessary.

Newly uploaded documents must automatically have:

```text
archived = false
```

This should happen through the database default unless the existing upload logic explicitly assigns the field.

Do not break uploads.

---

# 29. Existing Download/View Functionality

Archived documents must still be downloadable/viewable by users who have permission to view them.

Do not add:

```text
where('archived', false)
```

to generic document lookup code used for viewing/downloading unless that would prevent legitimate archive access.

Archive filtering belongs primarily in the list/query layer.

---

# 30. Localization / Language

The existing application contains Slovenian UI strings.

Follow the existing localization conventions.

Use appropriate Slovenian labels where the current interface uses Slovenian:

```text
Dokumenti
Arhiv
Arhiviraj
Obnovi
Prenos
Ogled
Izbriši
```

Do not introduce inconsistent English UI labels unless the application already uses English for that specific area.

---

# 31. Code Quality

Follow existing project style.

Do not:

* rewrite unrelated code;
* rename unrelated variables;
* upgrade Laravel;
* upgrade Vue;
* change Tailwind configuration;
* change authentication;
* change storage configuration;
* change database architecture unnecessarily;
* introduce new dependencies.

Keep the change focused.

---

# 32. Testing

After implementation, inspect the existing test structure.

If tests exist for documents/controllers/permissions, add appropriate tests.

At minimum verify:

### Archive

```text
Active document
    ↓
Archive
    ↓
archived = true
archived_at != null
archived_by = current user
```

### Restore

```text
Archived document
    ↓
Restore
    ↓
archived = false
archived_at = null
archived_by = null
```

### Active list

Archived documents do not appear.

### Archive list

Active documents do not appear.

### Ownership

A user with only:

```text
archive own documents
```

cannot archive another user's document.

### Restore ownership

A user with only:

```text
restore own documents
```

cannot restore another user's document.

### Physical file

Archiving does not remove the physical file.

### Delete

Existing delete behavior continues to work.

---

# 33. Validation

After implementation run the appropriate checks available in the repository.

At minimum:

```bash
php artisan migrate
php artisan test
```

If the project has lint/build commands, run them as well.

For frontend changes run the project's existing build command, for example:

```bash
npm run build
```

Do not assume commands exist. Inspect `composer.json` and `package.json` first.

---

# 34. Migration Safety

Before running migrations:

1. Inspect the existing migration history.
2. Make sure the new migration does not duplicate an existing `archived` column.
3. If an existing migration already creates `archived`, do not create a duplicate column.
4. If the database is already partially migrated, adapt the migration strategy appropriately.

Never modify an old migration solely to make the new feature work.

---

# 35. Git

Before changing files:

```bash
git status
```

Review the current branch and working tree.

Do not overwrite unrelated user changes.

If there are uncommitted changes unrelated to this feature:

* preserve them;
* do not reset;
* do not stash unless necessary and safe.

Recommended branch:

```text
feature/document-archiving
```

If the user is already working on another feature branch, keep using the existing branch unless explicitly instructed otherwise.

---

# 36. Implementation Order

Follow this order:

## Step 1

Inspect the current repository and existing partial archive implementation.

## Step 2

Inspect:

```text
Document.php
DocumentsController.php
routes/web.php
documents migrations
PermissionsSeeder.php
Index.vue
TableList.vue
TableItem.vue
```

## Step 3

Create/update migration.

## Step 4

Update Document model.

## Step 5

Add archive/restore controller methods.

## Step 6

Add routes.

## Step 7

Update permissions.

## Step 8

Refactor document query/list logic.

## Step 9

Implement Documents/Archive navigation.

## Step 10

Remove archive checkbox.

## Step 11

Add Archive/Restore actions.

## Step 12

Add confirmation.

## Step 13

Run tests.

## Step 14

Run frontend build/lint if available.

## Step 15

Review the final diff.

---

# 37. Final Review

Before considering the task complete, verify:

```text
[ ] Migration exists
[ ] archived defaults to false
[ ] archived_at exists
[ ] archived_by exists
[ ] Document model updated
[ ] archived cast is boolean
[ ] archived_at cast is datetime
[ ] archivedBy relationship exists
[ ] archive() exists
[ ] restore() exists
[ ] dedicated routes exist
[ ] archive permissions exist
[ ] restore permissions exist
[ ] active list excludes archived documents
[ ] archive list excludes active documents
[ ] search works in both views
[ ] pagination works in both views
[ ] archive checkbox removed
[ ] Archive action exists
[ ] Restore action exists
[ ] confirmation exists
[ ] physical files are not moved
[ ] physical files are not deleted during archive
[ ] existing delete still works
[ ] existing upload still works
[ ] existing processed functionality still works
[ ] existing download/view functionality still works
[ ] tests pass
[ ] frontend build passes
```

---

# 38. IMPORTANT: Do Not Stop at Analysis

This task is an implementation task.

Do not merely describe the required changes.

After inspecting the repository, implement the feature.

If an existing partial implementation conflicts with this specification, modify it to match this specification.

If something is genuinely ambiguous, prefer the existing project conventions and make the smallest safe change.

At the end, report:

1. files changed;
2. what was implemented;
3. tests/checks executed;
4. any remaining issues.

Do not claim tests passed unless they were actually executed.

---

# 39. Desired Final Architecture

The final result should conceptually look like:

```text
                    DOCUMENT
                       |
              +--------+--------+
              |                 |
           ACTIVE            ARCHIVED
              |                 |
          Archive            Restore
              |                 |
              +--------+--------+
                       |
                    Delete
```

The UI:

```text
Documents                         Archive

[ Documents ] [ Archive ]        [ Documents ] [ Archive ]

Active documents                 Archived documents

View  Download  Archive Delete  View Download Restore Delete
```

The database:

```text
documents
---------
id
user_id
company_id
key
file_name
year
file_path
file_mime_type
folder
processed
archived
archived_at
archived_by
created_at
updated_at
```

The archive feature must remain simple, explicit, reversible, permission-aware, and compatible with the existing uploader application.
