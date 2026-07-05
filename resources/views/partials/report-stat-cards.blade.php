@props(['cards'])

<div class="row g-3 g-md-4">
    @foreach($cards as $card)
    <div class="col-6 col-md-4 col-xl-3">
        <div class="card stat-card h-100">
            <div class="card-body d-flex align-items-center gap-2 gap-sm-3">
                <div class="icon bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}">
                    <i class="bi {{ $card['icon'] }}"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-muted small">{{ $card['label'] }}</div>
                    <h4 class="mb-0">
                        @if(($card['format'] ?? '') === 'money')
                            {{ number_format($stats[$card['key']], 2) }} د.ل
                        @else
                            {{ number_format($stats[$card['key']]) }}
                        @endif
                    </h4>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
