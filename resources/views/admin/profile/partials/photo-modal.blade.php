@php
    $modalUser = auth()->user();
@endphp
<div class="modal fade" id="profilePhotoModal" tabindex="-1" aria-labelledby="profilePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.profile.photo.update') }}" enctype="multipart/form-data"
                  data-page-loading="Uploading picture…">
                @csrf
                @method('PUT')
                <input type="hidden" name="_reopen_modal" value="profilePhotoModal">
                <div class="modal-header">
                    <h5 class="modal-title" id="profilePhotoModalLabel">Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    @if ($modalUser->profile_photo_thumbnail_url)
                        <img src="{{ $modalUser->profile_photo_thumbnail_url }}" alt="{{ $modalUser->full_name }}"
                             class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    @else
                        <i class="bi bi-person-circle text-body-secondary d-block mb-3" style="font-size: 120px; line-height: 1;" aria-hidden="true"></i>
                    @endif

                    <x-adminlte-input-file name="profile_photo" label="Change picture"
                        accept="image/*" />
                    <p class="form-text text-start mb-0">
                        Accepted formats: {{ strtoupper(implode(', ', config('media.profile_photo.formats'))) }}.
                        Max size: {{ number_format(config('media.profile_photo.max_size_kb') / 1024, 1) }} MB.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1" aria-hidden="true"></i> Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
