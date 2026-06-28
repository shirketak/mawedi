<div class="page-toolbar d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center mb-3">
    <h6 class="page-toolbar-title mb-0">{{ $title }}</h6>
    @if(!empty($actionUrl))
        <div class="page-toolbar-actions flex-shrink-0">
            <a href="{{ $actionUrl }}" class="btn {{ $actionClass ?? 'btn-primary' }} btn-sm w-100 w-sm-auto">
                @if(!empty($actionIcon))<i class="bi {{ $actionIcon }}"></i> @endif
                {{ $actionLabel }}
            </a>
        </div>
    @endif
</div>
