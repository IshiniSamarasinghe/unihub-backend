@extends('layouts.admin') {{-- or your actual admin layout --}}

@section('title', 'Societies')

@push('styles')
<style>
  .table th, .table td { vertical-align: middle; }
  .soc-logo { width: 42px; height: 42px; object-fit: contain; border-radius: 6px; background:#f4f6f9; }
  .truncate { max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .badge-soft { background:#eef2f7; color:#0c1d36; border-radius: 12px; padding: 4px 8px; font-size: 12px; }
</style>
@endpush

@section('content')
  <div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h3 class="mb-0">Societies</h3>
      <div class="d-flex gap-2">
        {{-- optional buttons to match Users page --}}
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Refresh</a>
        {{-- If you add export later, keep same spot --}}
        {{-- <a href="{{ route('admin.societies.export') }}" class="btn btn-primary">Export</a> --}}
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th style="width: 60px;">id</th>
                <th>Name</th>
                <th>University</th>
                <th>Slug</th>
                <th>Logo</th>
                <th>Join Link</th>
                <th style="width: 160px;">Updated</th>
                <th style="width: 120px;" class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($societies as $s)
                <tr>
                  <td>{{ $s->id }}</td>
                  <td>
                    <div class="fw-semibold">{{ $s->name }}</div>
                    @if(!empty($s->short))
                      <div class="text-muted small">{{ $s->short }}</div>
                    @endif
                  </td>
                  <td>
                    <span class="badge-soft">{{ optional($s->university)->name ?? 'Unknown' }}</span>
                  </td>
                  <td>{{ $s->slug }}</td>
                  <td>
                    @if($s->logo_url)
                      <img class="soc-logo" src="{{ $s->logo_url }}" alt="logo">
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td class="truncate">
                    @if($s->join_link)
                      <a href="{{ $s->join_link }}" target="_blank" rel="noreferrer">{{ $s->join_link }}</a>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </td>
                  <td>
                    <div>{{ optional($s->updated_at)->format('Y-m-d H:i') }}</div>
                    <div class="text-muted small">{{ optional($s->created_at)->diffForHumans() }}</div>
                  </td>
                  <td class="text-end">
                    {{-- keep the same icon buttons row style as Users page --}}
                    <div class="btn-group">
                      <button type="button" class="btn btn-sm btn-outline-primary"
                        data-bs-toggle="modal" data-bs-target="#societyEditModal"
                        data-id="{{ $s->id }}"
                        data-name="{{ $s->name }}"
                        data-join="{{ $s->join_link }}"
                        data-logo="{{ $s->logo_url }}"
                        data-desc="{{ $s->description }}">
                        <i class="bi bi-pencil"></i>
                      </button>
                      {{-- If you’ll add details view later: --}}
                      {{-- <a href="{{ route('admin.societies.show', $s->id) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-person-lines-fill"></i></a> --}}
                      {{-- For delete later: --}}
                      {{-- <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button> --}}
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted py-4">No societies found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Minimal edit modal (submits to your existing API: PUT /api/admin/societies/{id}) --}}
  <div class="modal fade" id="societyEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Society</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="societyEditForm">
          <div class="modal-body">
            <input type="hidden" id="society_id">
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input id="society_name" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea id="society_desc" class="form-control" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Join link</label>
              <input id="society_join" class="form-control" placeholder="https://...">
            </div>
            <div class="mb-3">
              <label class="form-label">Logo URL</label>
              <input id="society_logo" class="form-control" placeholder="/storage/societies/itsa.png">
            </div>
            <div class="text-danger small d-none" id="society_error">Failed to save</div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  const modal = document.getElementById('societyEditModal');
  modal?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    document.getElementById('society_id').value   = btn.getAttribute('data-id');
    document.getElementById('society_name').value = btn.getAttribute('data-name') || '';
    document.getElementById('society_desc').value = btn.getAttribute('data-desc') || '';
    document.getElementById('society_join').value = btn.getAttribute('data-join') || '';
    document.getElementById('society_logo').value = btn.getAttribute('data-logo') || '';
    document.getElementById('society_error').classList.add('d-none');
  });

  document.getElementById('societyEditForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const id   = document.getElementById('society_id').value;
    const name = document.getElementById('society_name').value;
    const description = document.getElementById('society_desc').value;
    const join_link   = document.getElementById('society_join').value;
    const logo_url    = document.getElementById('society_logo').value;

    try {
      // Uses your existing API route in api.php (kept intact):
      const res = await fetch(`/api/admin/societies/${id}`, {
        method: 'PUT',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          // Sanctum session cookie is sent automatically; if you also use a bearer token, add it here
        },
        body: JSON.stringify({ name, description, join_link, logo_url })
      });

      if (!res.ok) throw new Error('Save failed');

      // Refresh the page to reflect changes (simple + consistent with Users page)
      window.location.reload();
    } catch (err) {
      document.getElementById('society_error').classList.remove('d-none');
      console.error(err);
    }
  });
</script>
@endpush
