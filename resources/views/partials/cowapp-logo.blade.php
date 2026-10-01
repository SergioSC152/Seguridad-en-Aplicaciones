@if($cowappLogoUrl)
    <img src="{{ $cowappLogoUrl }}" alt="CowApp" width="{{ $logoSize ?? 80 }}" height="{{ $logoSize ?? 80 }}" style="object-fit:contain;max-width:100%;background:#fff;border-radius:10px;flex-shrink:0">
@else
    <span class="fw-bold">CowApp</span>
@endif
